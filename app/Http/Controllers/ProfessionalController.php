<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\WorkHours;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Services\CalendarService;

class ProfessionalController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        $empresa = $user->empresa;
        
        $tareasCreadas = Task::where('created_by', $user->id)->get();
        $tareas = $tareasCreadas;
        $tareasAsignadas = $user->assignedTasks()->with('assignees')->get(); // visible_para -> assignedTasks
        
        $tareasManagers = Task::whereHas('createdBy', function($query) use ($empresa) {
            $query->where('empleador_id', $empresa->id)
                  ->whereRaw('is_manager IS TRUE')
                  ->where('tipo_usuario', 'empleado');
        })
        ->whereHas('assignees', function($q) use ($user) { // whereNotNull('visible_para') implies assigned to someone? or specifically this user? Context implies tasks assigned to this user from managers.
            $q->where('users.id', $user->id); 
        })
        ->with(['createdBy', 'assignees'])
        ->get();

        $tareasEmpresa = Task::where('created_by', $empresa->id)
            ->whereHas('assignees', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->with(['createdBy', 'assignees', 'comments.user'])
            ->get();

        return view('profesionales.crear_tarea', compact('tareas', 'tareasAsignadas', 'tareasManagers', 'tareasEmpresa', 'empresa'));
    }

    public function registrarHoras(Request $request, CalendarService $calendarService)
    {
        $user = auth()->user();

        // Enforce profile completion
        if (empty($user->phone_number) || empty($user->location)) {
            return redirect()->route('profile.edit')->with('error', 'Por favor, completa tus datos personales (teléfono y ubicación) antes de registrar horas.');
        }

        $currentMonth = $request->month ? Carbon::parse($request->month) : Carbon::now();
        
        $calendar = $calendarService->generateCalendar($currentMonth, $user->id);
        $totalHours = $calendarService->getTotalHoursForMonth($currentMonth, $user->id);
        $debtSummary = \App\Http\Controllers\RecoveryHoursController::getDebtSummary($user->id);

        $completedTasksCount = $user->assignedTasks()
            ->whereRaw('tasks.completed IS TRUE')
            ->whereMonth('tasks.end_date', $currentMonth->month)
            ->whereYear('tasks.end_date', $currentMonth->year)
            ->count();
            
        $pendingTasksCount = $user->assignedTasks()
            ->whereRaw('tasks.completed IS FALSE')
            ->whereMonth('tasks.end_date', $currentMonth->month)
            ->whereYear('tasks.end_date', $currentMonth->year)
            ->count();

        $pendingTasks = $user->assignedTasks()
            ->whereRaw('tasks.completed IS FALSE')
            ->with('createdBy')
            ->distinct()
            ->get();

        return view('profesionales.registrar_horas', compact('calendar', 'currentMonth', 'totalHours', 'debtSummary', 'completedTasksCount', 'pendingTasksCount', 'pendingTasks'));
    }

}
