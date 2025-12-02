<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class ZapierService
{
    private string $webhookUrl = 'https://hooks.zapier.com/hooks/catch/12433184/24b9yg9/';

    /**
     * Send report notification to Zapier
     */
    public function notifyReportDownload(
        Carbon $month,
        string $csvContent,
        User $employee,
        array $summary
    ): void {
        $formattedMonth = $month->format('F Y');
        $employer = auth()->user()->name;
        $employerEmail = auth()->user()->email;
        $downloadTime = now()->toDateTimeString();

        // Crear contenido HTML con Tailwind CSS
        $formattedContent = $this->generateHTMLContent(
            $formattedMonth,
            $employee,
            $employer,
            $employerEmail,
            $downloadTime,
            $summary
        );

        // Preparar los datos para enviar a Zapier
        $data = [
            'month' => $formattedMonth,
            'professional_name' => $employee->name,
            'professional_email' => $employee->email,
            'employer' => $employer,
            'employer_email' => $employerEmail,
            'csv_content' => base64_encode($csvContent),
            'formatted_content' => $formattedContent,
            'download_time' => $downloadTime,
            'total_hours' => $summary['total_hours'],
            'total_records' => $summary['total_records'],
            'employer_signature' => $summary['employer_signature'],
        ];

        // Enviar los datos a Zapier
        Http::post($this->webhookUrl, $data);
    }

    /**
     * Generate HTML formatted content for email
     */
    private function generateHTMLContent(
        string $month,
        User $employee,
        string $employer,
        string $employerEmail,
        string $downloadTime,
        array $summary
    ): string {
        return "
            <html>
            <head>
                <link href='https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' rel='stylesheet'>
            </head>
            <body class='bg-gray-100'>
                <div class='p-6 bg-white rounded-lg shadow-md max-w-2xl mx-auto mt-10'>
                    <h1 class='text-2xl font-bold mb-4'>Reporte de Horas</h1>
                    <p class='text-lg'><strong>Mes:</strong> {$month}</p>
                    <p class='text-lg'><strong>Profesional:</strong> {$employee->name}</p>
                    <p class='text-lg'><strong>Empleador:</strong> {$employer}</p>
                    <p class='text-lg'><strong>Email del empleador:</strong> {$employerEmail}</p>
                    <p class='text-lg'><strong>Hora de descarga:</strong> {$downloadTime}</p>
                    <h2 class='text-xl font-semibold mt-6 mb-2'>Resumen</h2>
                    <p class='text-lg'><strong>Total de horas:</strong> {$summary['total_hours']}</p>
                    <p class='text-lg'><strong>Total de registros:</strong> {$summary['total_records']}</p>
                    <h2 class='text-xl font-semibold mt-6 mb-2'>Firma</h2>
                    <p class='text-lg'>{$summary['employer_signature']}</p>
                </div>
            </body>
            </html>
        ";
    }
}
