<?php

namespace App\Services;

use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Model\SendSmtpEmail;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

use Illuminate\Support\Facades\URL;

class BrevoEmailService
{
    private TransactionalEmailsApi $apiInstance;
    private string $senderEmail;
    private string $senderName;
    private string $baseUrl = 'https://obertrack.com';

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey(
            'api-key',
            config('services.brevo.api_key')
        );

        // Configure Guzzle client with SSL options
        $guzzleConfig = [
            'timeout' => 30,
        ];

        // SSL verification logic
        $sslVerify = config('services.brevo.ssl_verify');
        if ($sslVerify !== null) {
            $guzzleConfig['verify'] = filter_var($sslVerify, FILTER_VALIDATE_BOOLEAN);
        } elseif (config('app.env') === 'local' || config('app.env') === 'development') {
            $guzzleConfig['verify'] = false; // Disable SSL verification in development
        }

        $this->apiInstance = new TransactionalEmailsApi(
            new Client($guzzleConfig),
            $config
        );

        $this->senderEmail = config('services.brevo.sender_email');
        $this->senderName = config('services.brevo.sender_name');

        // Force production URL for routes generated within this service (emails)
        URL::forceRootUrl($this->baseUrl);
        if (strpos($this->baseUrl, 'https') === 0) {
            URL::forceScheme('https');
        }
    }

    /**
     * Send email notification for task status update
     */
    public function sendTaskStatusNotification($recipientEmail, $recipientName, $taskData)
    {
        try {
            $statusLabel = $taskData['status_label'] ?? 'Actualizado';
            $sendSmtpEmail = new SendSmtpEmail([
                'subject' => '🔄 Estado de tarea actualizado: ' . $taskData['title'] . ' (' . $statusLabel . ')',
                'sender' => ['name' => $this->senderName, 'email' => $this->senderEmail],
                'to' => [['email' => $recipientEmail, 'name' => $recipientName]],
                'htmlContent' => $this->renderTaskStatusEmail($taskData),
            ]);

            $result = $this->apiInstance->sendTransacEmail($sendSmtpEmail);

            Log::info('Brevo: Task status notification sent', [
                'recipient' => $recipientEmail,
                'task_id' => $taskData['id'] ?? null,
                'message_id' => $result->getMessageId()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Brevo: Failed to send task status notification', [
                'recipient' => $recipientEmail,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Send email notification for a new task assignment
     */
    public function sendNewTaskNotification($recipientEmail, $recipientName, $taskData)
    {
        try {
            $sendSmtpEmail = new SendSmtpEmail([
                'subject' => '📋 Nueva tarea asignada: ' . $taskData['title'],
                'sender' => ['name' => $this->senderName, 'email' => $this->senderEmail],
                'to' => [['email' => $recipientEmail, 'name' => $recipientName]],
                'htmlContent' => $this->renderNewTaskEmail($taskData),
            ]);

            $result = $this->apiInstance->sendTransacEmail($sendSmtpEmail);

            Log::info('Brevo: New task notification sent', [
                'recipient' => $recipientEmail,
                'task_id' => $taskData['id'] ?? null,
                'message_id' => $result->getMessageId()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Brevo: Failed to send new task notification', [
                'recipient' => $recipientEmail,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Send email notification for pending hours approval
     */
    public function sendPendingHoursNotification($recipientEmail, $recipientName, $pendingHoursData)
    {
        try {
            $sendSmtpEmail = new SendSmtpEmail([
                'subject' => '⏰ Tienes horas pendientes por aprobar',
                'sender' => ['name' => $this->senderName, 'email' => $this->senderEmail],
                'to' => [['email' => $recipientEmail, 'name' => $recipientName]],
                'htmlContent' => $this->renderPendingHoursEmail($pendingHoursData),
            ]);

            $result = $this->apiInstance->sendTransacEmail($sendSmtpEmail);

            Log::info('Brevo: Pending hours notification sent', [
                'recipient' => $recipientEmail,
                'message_id' => $result->getMessageId()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Brevo: Failed to send pending hours notification', [
                'recipient' => $recipientEmail,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Send email notification for recovery hours request
     */
    public function sendRecoveryRequestNotification($recipientEmail, $recipientName, $recoveryData)
    {
        try {
            $htmlContent = view('emails.recovery-report', [
                'recipientName' => $recipientName,
                'recoveryData' => $recoveryData
            ])->render();

            return $this->sendEmail($recipientEmail, $recipientName, '🔄 Reporte de Horas Recuperadas', $htmlContent);
        } catch (\Exception $e) {
            Log::error('Brevo: Failed to send recovery request notification', [
                'recipient' => $recipientEmail,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send email notification for absence registration
     */
    public function sendAbsenceNotification($recipientEmail, $recipientName, $date, $endOfMonth)
    {
        try {
            $htmlContent = view('emails.absence-registered', [
                'recipientName' => $recipientName,
                'date' => $date,
                'endOfMonth' => $endOfMonth
            ])->render();

            return $this->sendEmail($recipientEmail, $recipientName, '📅 Registro de Ausencia - Obertrack', $htmlContent);
        } catch (\Exception $e) {
            Log::error('Brevo: Failed to send absence notification', [
                'recipient' => $recipientEmail,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send email functionality for password reset code
     */
    public function sendPasswordResetCode($recipientEmail, $recipientName, $code)
    {
        try {
            $sendSmtpEmail = new SendSmtpEmail([
                'subject' => 'Código de Verificación - Cambio de Contraseña',
                'sender' => ['name' => $this->senderName, 'email' => $this->senderEmail],
                'to' => [['email' => $recipientEmail, 'name' => $recipientName]],
                'htmlContent' => view('emails.password-verification-code', ['code' => $code])->render(),
            ]);

            $result = $this->apiInstance->sendTransacEmail($sendSmtpEmail);

            Log::info('Brevo: Password reset code sent', [
                'recipient' => $recipientEmail,
                'message_id' => $result->getMessageId()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Brevo: Failed to send password reset code', [
                'recipient' => $recipientEmail,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Send email notification for work hours approval
     */
    public function sendWorkHoursApprovedNotification($recipientEmail, $recipientName, $approvalData)
    {
        try {
            $sendSmtpEmail = new SendSmtpEmail([
                'subject' => '✅ Horas registradas aprobadas',
                'sender' => ['name' => $this->senderName, 'email' => $this->senderEmail],
                'to' => [['email' => $recipientEmail, 'name' => $recipientName]],
                'htmlContent' => $this->renderWorkHoursApprovedEmail($approvalData),
            ]);

            $result = $this->apiInstance->sendTransacEmail($sendSmtpEmail);

            Log::info('Brevo: Work hours approval notification sent', [
                'recipient' => $recipientEmail,
                'message_id' => $result->getMessageId()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Brevo: Failed to send work hours approval notification', [
                'recipient' => $recipientEmail,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Send email notification for recovery hours status update
     */
    public function sendRecoveryStatusNotification($recipientEmail, $recipientName, $recoveryData)
    {
        try {
            $status = $recoveryData['approved'] ? 'Aprobada' : 'Rechazada';
            $sendSmtpEmail = new SendSmtpEmail([
                'subject' => '🔄 Estado de recuperación: ' . $status,
                'sender' => ['name' => $this->senderName, 'email' => $this->senderEmail],
                'to' => [['email' => $recipientEmail, 'name' => $recipientName]],
                'htmlContent' => $this->renderRecoveryStatusEmail($recoveryData),
            ]);

            $result = $this->apiInstance->sendTransacEmail($sendSmtpEmail);

            Log::info('Brevo: Recovery status notification sent', [
                'recipient' => $recipientEmail,
                'message_id' => $result->getMessageId()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Brevo: Failed to send recovery status notification', [
                'recipient' => $recipientEmail,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Render HTML email for task status update
     */
    private function renderTaskStatusEmail($taskData)
    {
        return view('emails.task-status-updated', [
            'taskTitle' => $taskData['title'],
            'statusLabel' => $taskData['status_label'] ?? 'Actualizado',
            'previousStatus' => $taskData['previous_status_label'] ?? 'Desconocido',
            'updatedBy' => $taskData['updated_by'] ?? 'Profesional',
            'completed' => $taskData['completed'] ?? false,
            'taskUrl' => route('empleador.tareas.index')
        ])->render();
    }

    /**
     * Render HTML email for new task assignment
     */
    private function renderNewTaskEmail($taskData)
    {
        $priority = $taskData['priority'] ?? 'medium';
        
        $priorityLabels = [
            'low' => 'Baja',
            'medium' => 'Media',
            'high' => 'Alta',
            'urgent' => 'Urgente'
        ];
        
        $priorityColors = [
            'low' => '#10b981',
            'medium' => '#f59e0b',
            'high' => '#ef4444',
            'urgent' => '#7c3aed'
        ];
        
        $priorityColor = $priorityColors[$priority] ?? '#6b7280';
        $priorityLabel = $priorityLabels[$priority] ?? ucfirst($priority);

        $startDate = isset($taskData['start_date']) ? date('d/m/Y', strtotime($taskData['start_date'])) : 'No especificada';
        $endDate = isset($taskData['end_date']) ? date('d/m/Y', strtotime($taskData['end_date'])) : 'No especificada';

        return view('emails.new-task-assigned', [
            'taskTitle' => $taskData['title'],
            'taskDescription' => $taskData['description'] ?? '',
            'priority' => $priorityLabel,
            'priorityColor' => $priorityColor,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'assignedBy' => $taskData['assigned_by'] ?? 'Obertrack',
            'taskUrl' => route('empleados.tasks.index')
        ])->render();
    }

    /**
     * Render HTML email for pending hours approval
     */
    private function renderPendingHoursEmail($pendingHoursData)
    {
        return view('emails.pending-hours-approval', [
            'employeeName' => $pendingHoursData['employee_name'] ?? 'Profesional',
            'pendingHours' => $pendingHoursData['pending_hours'] ?? [],
            'totalHours' => $pendingHoursData['total_hours'] ?? 0,
            'approvalUrl' => route('empleador.dashboard')
        ])->render();
    }

    /**
     * Render HTML email for work hours approval
     */
    private function renderWorkHoursApprovedEmail($approvalData)
    {
        return view('emails.hours-approved', [
            'type' => $approvalData['type'] ?? 'Semana', // Semana, Mes, Días
            'period' => $approvalData['period'] ?? '',
            'comment' => $approvalData['comment'] ?? null,
            'approvedBy' => $approvalData['approved_by'] ?? 'Administrador',
            'dashboardUrl' => route('empleado.registrar-horas')
        ])->render();
    }

    /**
     * Render HTML email for recovery hours status update
     */
    private function renderRecoveryStatusEmail($recoveryData)
    {
        return view('emails.recovery-status', [
            'hours' => $recoveryData['hours'],
            'date' => $recoveryData['date'],
            'approved' => $recoveryData['approved'],
            'comment' => $recoveryData['comment'] ?? null,
            'approvedBy' => $recoveryData['approved_by'] ?? 'Administrador',
            'dashboardUrl' => route('empleado.registrar-horas')
        ])->render();
    }

    public function sendAnalystAlert(string $toEmail, string $toName, array $data): bool
    {
        $redAlertsHtml = '<ul>';
        foreach ($data['red_alerts'] as $alert) {
            $redAlertsHtml .= "<li><strong>{$alert['user']['name']}</strong> ({$alert['user']['email']}) - Inactivo desde: " . ($alert['last_registration'] ?? 'Nunca') . "</li>";
        }
        $redAlertsHtml .= '</ul>';

        $htmlContent = "
            <h2>Alerta de Inactividad de Profesionales (ROJO)</h2>
            <p>Se han detectado profesionales que llevan 2 o más días sin registrar actividad:</p>
            {$redAlertsHtml}
            <p>Por favor, revisa el <a href=\"" . route('admin.dashboard') . "\">Dashboard del Analista</a> para más detalles.</p>
        ";

        return $this->sendEmail($toEmail, $toName, 'Alertas de Inactividad - Nivel ROJO', $htmlContent);
    }

    public function sendRegistrationReminder(string $toEmail, string $toName, array $absences = []): bool
    {
        $absenceHtml = '';
        if (!empty($absences)) {
            $dates = implode(', ', array_map(fn($d) => date('d/m/Y', strtotime($d)), $absences));
            $absenceHtml = "<p><strong>Nota:</strong> Tienes ausencias registradas registradas recientemente en las siguientes fechas: {$dates}.</p>";
        }

        $htmlContent = "
            <h2>Hola, {$toName}</h2>
            <p>Este es un recordatorio automatizado para mantener tu registro de horas al día.</p>
            <p>Si tienes horas pendientes de cargar, por favor ingrésalas lo antes posible.</p>
            {$absenceHtml}
            <p><a href=\"" . route('empleado.registrar-horas') . "\">Ir a registrar horas</a></p>
        ";

        return $this->sendEmail($toEmail, $toName, 'Recordatorio de Registro de Horas', $htmlContent);
    }

    /**
     * Send email reminder for pending recovery hours
     */
    public function sendRecoveryReminder(string $toEmail, string $toName, float $pendingHours, string $deadline): bool
    {
        try {
            $htmlContent = view('emails.recovery-reminder', [
                'recipientName' => $toName,
                'pendingHours' => $pendingHours,
                'deadline' => $deadline
            ])->render();

            return $this->sendEmail($toEmail, $toName, '⏰ Recordatorio: Horas pendientes de recuperación', $htmlContent);
        } catch (\Exception $e) {
            Log::error('Brevo: Failed to send recovery reminder', [
                'recipient' => $toEmail,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Generic method to send an email via Brevo
     */
    public function sendEmail(string $toEmail, string $toName, string $subject, string $htmlContent, ?array $attachment = null): bool
    {
        try {
            $emailData = [
                'subject' => $subject,
                'sender' => ['name' => $this->senderName, 'email' => $this->senderEmail],
                'to' => [['email' => $toEmail, 'name' => $toName]],
                'htmlContent' => $htmlContent,
            ];

            if ($attachment) {
                $emailData['attachment'] = [[
                    'content' => base64_encode($attachment['content']),
                    'name' => $attachment['name']
                ]];
            }

            $sendSmtpEmail = new SendSmtpEmail($emailData);

            $this->apiInstance->sendTransacEmail($sendSmtpEmail);
            return true;
        } catch (\Exception $e) {
            Log::error('Brevo: Generic sendEmail failed', [
                'recipient' => $toEmail,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send mass communication email with attachments
     */
    public function sendMassCommunication(string $toEmail, string $toName, string $subject, string $htmlMessage, string $companyName, array $attachments = []): bool
    {
        try {
            $htmlContent = view('emails.mass-communication', [
                'subject' => $subject,
                'htmlMessage' => $htmlMessage,
                'companyName' => $companyName,
                'attachments' => $attachments // Just for the view, Brevo handles the actual attachments separately
            ])->render();

            $sendSmtpEmail = new SendSmtpEmail([
                'subject' => $subject,
                'sender' => ['name' => $companyName . ' (via Obertrack)', 'email' => $this->senderEmail],
                'to' => [['email' => $toEmail, 'name' => $toName]],
                'htmlContent' => $htmlContent,
            ]);

            // Add attachments if present
            if (!empty($attachments)) {
                $brevoAttachments = [];
                foreach ($attachments as $file) {
                    $brevoAttachments[] = [
                        'content' => base64_encode(file_get_contents($file['path'])),
                        'name' => $file['name']
                    ];
                }
                $sendSmtpEmail['attachment'] = $brevoAttachments;
            }

            $this->apiInstance->sendTransacEmail($sendSmtpEmail);
            return true;
        } catch (\Exception $e) {
            Log::error('Brevo: Mass communication failed', [
                'recipient' => $toEmail,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send weekly report to company
     */
    public function sendWeeklyReport(string $toEmail, string $toName, array $data, ?array $attachment = null): bool
    {
        try {
            $emailData = [
                'to' => [['email' => $toEmail, 'name' => $toName]],
                'templateId' => 1, // Weekly report template
                'params' => $data,
            ];

            if ($attachment) {
                $emailData['attachment'] = [[
                    'content' => base64_encode($attachment['content']),
                    'name' => $attachment['name']
                ]];
            }

            $sendSmtpEmail = new SendSmtpEmail($emailData);

            $this->apiInstance->sendTransacEmail($sendSmtpEmail);
            
            Log::info('Weekly report email sent', [
                'to' => $toEmail,
                'week' => $data['week_start'] . ' - ' . $data['week_end'],
                'has_attachment' => !is_null($attachment)
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Weekly report email failed', [
                'to' => $toEmail,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send monthly report to company
     */
    public function sendMonthlyReport(string $toEmail, string $toName, array $data, ?array $attachment = null): bool
    {
        try {
            $emailData = [
                'to' => [['email' => $toEmail, 'name' => $toName]],
                'templateId' => 2, // Monthly report template
                'params' => $data,
            ];

            if ($attachment) {
                $emailData['attachment'] = [[
                    'content' => base64_encode($attachment['content']),
                    'name' => $attachment['name']
                ]];
            }

            $sendSmtpEmail = new SendSmtpEmail($emailData);

            $this->apiInstance->sendTransacEmail($sendSmtpEmail);
            
            Log::info('Monthly report email sent', [
                'to' => $toEmail,
                'month' => $data['month'],
                'has_attachment' => !is_null($attachment)
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Monthly report email failed', [
                'to' => $toEmail,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
