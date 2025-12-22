<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\WorkHours;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Services\CalendarService;

class EmpleadoController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        $empleador = User::find($user->empleador_id);
        
        $tareasCreadas = Task::where('created_by', $user->id)->get();
        $tareas = $tareasCreadas;
        $tareasAsignadas = $user->assignedTasks()->with('assignees')->get(); // visible_para -> assignedTasks
        
        $tareasManageres = Task::whereHas('createdBy', function($query) use ($empleador) {
            $query->where('empleador_id', $empleador->id)
                  ->where('is_manager', true)
                  ->where('tipo_usuario', 'empleado');
        })
        ->whereHas('assignees', function($q) use ($user) { // whereNotNull('visible_para') implies assigned to someone? or specifically this user? Context implies tasks assigned to this user from managers.
            $q->where('users.id', $user->id); 
        })
        ->with(['createdBy', 'assignees'])
        ->get();

        $tareasEmpleador = Task::where('created_by', $empleador->id)
            ->whereHas('assignees', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->with(['createdBy', 'assignees', 'comments.user'])
            ->get();

        return view('empleados.crear_tarea', compact('tareas', 'tareasAsignadas', 'tareasManageres', 'tareasEmpleador', 'empleador'));
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

        return view('empleados.registrar_horas', compact('calendar', 'currentMonth', 'totalHours'));
    }

}
