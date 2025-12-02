<?php

namespace App\Services;

use Illuminate\Support\Collection;

class TaskDataService
{
    /**
     * Prepare task data for charts grouped by month
     */
    public function prepareChartData(Collection $tareas)
    {
        $taskData = $tareas->groupBy(function($tarea) {
            return $tarea->created_at->format('Y-m');
        });

        $chartData = [];
        foreach ($taskData as $mes => $tareasDelMes) {
            $chartData[$mes] = [
                'total' => $tareasDelMes->count(),
                'completed' => $tareasDelMes->where('completed', 1)->count(),
                'pending' => $tareasDelMes->where('completed', 0)->count(),
            ];
        }

        return $chartData;
    }
}
