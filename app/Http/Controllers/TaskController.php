<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use App\Models\WorkHours;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->tipo_usuario === 'empleador') {
            $tasks = Task::where('created_by', $user->id)->with('visibleTo')->get();
        } else {
            $tasks = Task::where('visible_para', $user->id)->get();
        }
        
        return view('tareas.index', compact('tasks'));
    }

    public function create()
    {
        $empleados = Auth::user()->empleados;
        return view('tareas.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'completed' => 'boolean',
        ]);

        $task = Task::create([
            'created_by' => Auth::id(),
            'visible_para' => Auth::user()->empleador_id,
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'priority' => $validatedData['priority'],
            'completed' => $validatedData['completed'] ?? false,
        ]);

        return back()->with('success', 'Tarea creada exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $task = Task::findOrFail($id);
        $task->update($validatedData);

        return back()->with('success', 'Tarea actualizada exitosamente.');
    }

//     public function update(Request $request, Task $task)
// {
//     $validatedData = $request->validate([
//         'title' => 'required|string|max:255',
//         'description' => 'nullable|string',
//         'start_date' => 'required|date',
//         'end_date' => 'required|date|after_or_equal:start_date',
//         'priority' => 'required|in:low,medium,high,urgent',
//         'employee_id' => 'required|exists:users,id',
//     ]);

//     $task->update([
//         'visible_para' => $validatedData['employee_id'],
//         'title' => $validatedData['title'],
//         'description' => $validatedData['description'],
//         'start_date' => $validatedData['start_date'],
//         'end_date' => $validatedData['end_date'],
//         'priority' => $validatedData['priority'],
//     ]);

//     return redirect()->route('empleador.tareas.index')->with('success', 'Tarea actualizada exitosamente.');
// }



    public function destroy($taskId)
    {
        $task = Task::findOrFail($taskId);
        
        WorkHours::where([
            'user_id' => $task->created_by,
            'work_date' => $task->created_at->toDateString(),
        ])->delete();

        $task->delete();

        return redirect()->back()->with('success', 'Tarea eliminada con éxito');
    }

    public function toggleCompletion(Request $request, $taskId)
    {
        \Log::info('Toggling completion for task ID: ' . $taskId);

        try {
            $task = Task::findOrFail($taskId);
            
            \Log::info('Current task data: ' . json_encode($task->toArray()));

            $task->completed = !$task->completed;
            $result = $task->save();

            \Log::info('Update result: ' . ($result ? 'true' : 'false'));
            \Log::info('New task data: ' . json_encode($task->fresh()->toArray()));

            if (!$result) {
                throw new \Exception('Failed to update task');
            }

            return response()->json([
                'success' => true,
                'completed' => $task->completed
            ]);
        } catch (\Exception $e) {
            \Log::error('Error toggling task completion: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado de la tarea: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addComment(Request $request, $taskId)
    {
        $validatedData = $request->validate([
            'content' => 'required|string|max:65535',
        ]);

        $comment = new Comment([
            'content' => $validatedData['content'],
            'task_id' => $taskId,
            'user_id' => auth()->id(),
        ]);
        $comment->save();

        return back()->with('success', 'Comentario agregado exitosamente.');
    }

    public function updateComment(Request $request, $taskId, $commentId)
    {
        $validatedData = $request->validate([
            'content' => 'required|string|max:65535',
        ]);

        $comment = Comment::findOrFail($commentId);
        $comment->update([
            'content' => $validatedData['content'],
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Comentario actualizado exitosamente.');
    }

    public function deleteComment($taskId, $commentId)
    {
        $comment = Comment::where('task_id', $taskId)->where('id', $commentId)->firstOrFail();
        $comment->delete();
        return back()->with('success', 'Comentario eliminado exitosamente.');
    }

    // Funciones para crear tareas de empresa

    public function createForEmployee()
    {
        $empleados = Auth::user()->empleados;
        return view('tareas.create_for_employee', compact('empleados'));
    }

    public function storeForEmployee(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'employee_id' => 'required|exists:users,id',
        ]);

        $task = Task::create([
            'created_by' => Auth::id(),
            'visible_para' => $validatedData['employee_id'],
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'priority' => $validatedData['priority'],
            'completed' => false,
        ]);

        return redirect()->route('empleador.tareas.index')->with('success', 'Tarea creada y asignada exitosamente.');
    }

    // public function indexForEmployer()
    // {
    //     $user = Auth::user();
    //     $tasks = Task::where('created_by', $user->id)->with('visibleTo')->get();
    //     return view('empleadores.ver_tareas_empleados', compact('tasks'));
    // }

    public function edit(Task $task)
    {
        $empleados = Auth::user()->empleados;
        return view('tareas.edit', compact('task', 'empleados'));
    }

    public function updateForEmployer(Request $request, Task $task)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'employee_id' => 'required|exists:users,id',
        ]);

        $task->update([
            'visible_para' => $validatedData['employee_id'],
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'priority' => $validatedData['priority'],
        ]);

        return redirect()->route('empleador.tareas.index')->with('success', 'Tarea actualizada exitosamente.');
    }















    public function toggleEmployerTaskCompletion(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        
        // Verificar si el usuario autenticado es el empleador de esta tarea
        if ($task->createdBy->id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para modificar esta tarea'], 403);
        }

        $task->completed = !$task->completed;
        $task->save();

        return response()->json([
            'success' => true,
            'completed' => $task->completed,
            'message' => $task->completed ? 'Tarea marcada como completada' : 'Tarea marcada como en progreso'
        ]);
    }

    public function editEmployerTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        
        // Verificar si el usuario autenticado es el empleador de esta tarea
        if ($task->createdBy->id !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para editar esta tarea');
        }

        return view('empleador.tareas.edit', compact('task'));
    }

    public function updateEmployerTask(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        
        // Verificar si el usuario autenticado es el empleador de esta tarea
        if ($task->createdBy->id !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para actualizar esta tarea');
        }

        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $task->update($validatedData);

        return redirect()->route('empleador.tareas.index')->with('success', 'Tarea actualizada con éxito');
    }
}