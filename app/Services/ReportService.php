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
}
