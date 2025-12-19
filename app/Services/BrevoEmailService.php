<?php

namespace App\Services;

use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Model\SendSmtpEmail;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class BrevoEmailService
{
    private TransactionalEmailsApi $apiInstance;
    private string $senderEmail;
    private string $senderName;

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
     * Render HTML email for new task assignment
     */
    private function renderNewTaskEmail($taskData)
    {
        $priority = $taskData['priority'] ?? 'media';
        $priorityColors = [
            'baja' => '#10b981',
            'media' => '#f59e0b',
            'alta' => '#ef4444'
        ];
        $priorityColor = $priorityColors[$priority] ?? '#6b7280';

        $startDate = isset($taskData['start_date']) ? date('d/m/Y', strtotime($taskData['start_date'])) : 'No especificada';
        $endDate = isset($taskData['end_date']) ? date('d/m/Y', strtotime($taskData['end_date'])) : 'No especificada';

        return view('emails.new-task-assigned', [
            'taskTitle' => $taskData['title'],
            'taskDescription' => $taskData['description'] ?? '',
            'priority' => ucfirst($priority),
            'priorityColor' => $priorityColor,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'assignedBy' => $taskData['assigned_by'] ?? 'Tu empleador',
            'taskUrl' => route('empleados.tasks.index')
        ])->render();
    }

    /**
     * Render HTML email for pending hours approval
     */
    private function renderPendingHoursEmail($pendingHoursData)
    {
        return view('emails.pending-hours-approval', [
            'employeeName' => $pendingHoursData['employee_name'] ?? 'Empleado',
            'pendingHours' => $pendingHoursData['pending_hours'] ?? [],
            'totalHours' => $pendingHoursData['total_hours'] ?? 0,
            'approvalUrl' => route('empleador.dashboard')
        ])->render();
    }
}
