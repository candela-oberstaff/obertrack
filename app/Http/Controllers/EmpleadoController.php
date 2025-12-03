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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Empty - consider removing if not used
    }

    public function create()
    {
        $user = auth()->user();
        $empleador = User::find($user->empleador_id);
        
        $tareasCreadas = Task::where('created_by', $user->id)->get();
        $tareas = $tareasCreadas;
        $tareasAsignadas = Task::where('visible_para', $user->id)->with('visibleTo')->get();
        
        $tareasManageres = Task::whereHas('createdBy', function($query) use ($empleador) {
            $query->where('empleador_id', $empleador->id)
                  ->where('is_manager', true)
                  ->where('tipo_usuario', 'empleado');
        })
        ->whereNotNull('visible_para')
        ->with(['createdBy', 'visibleTo'])
        ->get();

        $tareasEmpleador = Task::where('created_by', $empleador->id)
            ->where('visible_para', $user->id)
            ->with(['createdBy', 'visibleTo', 'comments.user'])
            ->get();

        return view('empleados.crear_tarea', compact('tareas', 'tareasAsignadas', 'tareasManageres', 'tareasEmpleador', 'empleador'));
    }

    public function registrarHoras(Request $request, CalendarService $calendarService)
    {
        $user = auth()->user();
        $currentMonth = $request->month ? Carbon::parse($request->month) : Carbon::now();
        
        $calendar = $calendarService->generateCalendar($currentMonth, $user->id);
        $totalHours = $calendarService->getTotalHoursForMonth($currentMonth, $user->id);

        return view('empleados.registrar_horas', compact('calendar', 'currentMonth', 'totalHours'));
    }

}
