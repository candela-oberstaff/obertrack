<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\WorkHours;

class ReportService
{
    /**
     * Prepare report data for a specific employee and month
     */
    public function prepareReportData(User $employee, $workHours, Carbon $month): array
    {
        $reportData = [];
        $weekStart = $month->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        while ($weekStart->lte($month->endOfMonth())) {
            $weekHours = $workHours->filter(function ($workHour) use ($weekStart, $weekEnd) {
                return Carbon::parse($workHour->work_date)->between($weekStart, $weekEnd);
            });
            
            $totalHours = $weekHours->sum('hours_worked');

            $reportData[] = [
                'profesional' => $employee->name,
                'semana' => $weekStart->format('d/m/Y') . ' - ' . min($weekEnd, $month->copy()->endOfMonth())->format('d/m/Y'),
                'horas_trabajadas' => $totalHours,
                'estado' => $totalHours > 0 ? 'Aprobado' : 'Sin horas'
            ];

            $weekStart->addWeek();
            $weekEnd->addWeek();
        }

        return $reportData;
    }

    /**
     * Generate CSV content from report data
     */
    public function generateCSV(array $reportData, User $employee, Carbon $month): string
    {
        $csv = fopen('php://temp', 'r+');
        
        // Añadir título y detalles del reporte
        fputcsv($csv, ['REPORTE MENSUAL DE HORAS TRABAJADAS']);
        fputcsv($csv, []);
        fputcsv($csv, ['Empresa:', auth()->user()->name]);
        fputcsv($csv, ['Profesional:', $employee->name]);
        fputcsv($csv, ['Email del Profesional:', $employee->email]);
        fputcsv($csv, ['Mes:', $month->format('F Y')]);
        fputcsv($csv, ['Generado el:', Carbon::now()->format('d/m/Y H:i:s')]);
        fputcsv($csv, []);
        
        // Añadir texto de certificación
        fputcsv($csv, ['CERTIFICACIÓN']);
        fputcsv($csv, ['Certifico que las horas aquí mostradas son correctas y autorizo el pago al Profesional.']);
        fputcsv($csv, []);
        
        // Añadir encabezados de la tabla
        fputcsv($csv, ['', 'PROFESIONAL', 'SEMANA', 'HORAS TRABAJADAS', 'ESTADO']);
        
        // Añadir datos y calcular el total de horas
        $rowNumber = 1;
        $totalHours = 0;
        foreach ($reportData as $row) {
            fputcsv($csv, [
                $rowNumber,
                $row['profesional'],
                $row['semana'],
                $row['horas_trabajadas'],
                $row['estado']
            ]);
            $totalHours += floatval($row['horas_trabajadas']);
            $rowNumber++;
        }
        
        // Añadir resumen
        fputcsv($csv, []);
        fputcsv($csv, ['RESUMEN']);
        fputcsv($csv, ['Total de registros:', count($reportData)]);
        fputcsv($csv, ['Total de horas:', number_format($totalHours, 2)]);
        
        // Añadir firma y fecha
        fputcsv($csv, []);
        fputcsv($csv, ['FIRMA']);
        fputcsv($csv, ['Empleador:', auth()->user()->name]);
        fputcsv($csv, ['Fecha:', Carbon::now()->format('d/m/Y')]);
        fputcsv($csv, ['Firma: ', auth()->user()->name]);
        
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);
        
        return $content;
    }

    /**
     * Extract summary information from CSV content
     */
    public function extractCSVSummary(string $csvContent): array
    {
        $lines = explode("\n", $csvContent);
        $totalHours = 0;
        $totalRecords = 0;
        $employerSignature = '';

        foreach ($lines as $line) {
            if (strpos($line, 'Total de horas:') !== false) {
                $parts = str_getcsv($line);
                $totalHours = isset($parts[1]) ? $parts[1] : 0;
            } elseif (strpos($line, 'Total de registros:') !== false) {
                $parts = str_getcsv($line);
                $totalRecords = isset($parts[1]) ? $parts[1] : 0;
            }
        }

        // Extraer la firma del empleador
        foreach (array_reverse($lines) as $line) {
            if (strpos($line, 'Firma:') !== false) {
                $parts = str_getcsv($line);
                $employerSignature = isset($parts[1]) ? $parts[1] : '';
                break;
            }
        }

        return [
            'total_hours' => $totalHours,
            'total_records' => $totalRecords,
            'employer_signature' => $employerSignature,
        ];
    }
    /**
     * Orchestrate the generation of a monthly report
     */
    public function generateMonthlyReportOrchestration(User $employee, Carbon $month): array
    {
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        // Obtener las horas trabajadas para el mes especificado
        $workHours = WorkHours::where('user_id', $employee->id)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->where('approved', true)
            ->get();

        // Calcular el total de horas aprobadas
        $totalApprovedHours = $workHours->sum('hours_worked');

        // Verificar si hay suficientes horas aprobadas
        if ($totalApprovedHours < 160) {
            throw new \Exception('No se pueden descargar reportes hasta que se hayan aprobado al menos 160 horas.');
        }

        // Preparar los datos para el CSV
        $reportData = $this->prepareReportData($employee, $workHours, $month);

        // Generar el contenido del CSV
        $csvContent = $this->generateCSV($reportData, $employee, $month);
    
        // Extraer resumen del CSV
        $summary = $this->extractCSVSummary($csvContent);

        // Notificar a Zapier (usando el servicio inyectado si fuera necesario, pero aquí lo haremos en el controlador o inyectaremos ZapierService aquí)
        // Para mantener este servicio puro, devolveremos los datos necesarios para que el controlador o un orquestador superior llame a ZapierService
        
        return [
            'csvContent' => $csvContent,
            'summary' => $summary,
            'fileName' => "reporte_mensual_{$employee->name}_{$month->format('Y_m')}.csv"
        ];
    }
}
