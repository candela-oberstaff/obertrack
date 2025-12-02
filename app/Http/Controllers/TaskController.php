<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TaskManagementService;
use App\Services\TaskCommentService;

class TaskController extends Controller
{
    public function __construct(
        private TaskManagementService $taskManagementService,
        private TaskCommentService $taskCommentService
    ) {}

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

        $this->taskManagementService->createTask($validatedData);

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
        $this->taskManagementService->updateTask($task, $validatedData);

        return back()->with('success', 'Tarea actualizada exitosamente.');
    }

    public function destroy($taskId)
    {
        $this->taskManagementService->deleteTask($taskId);
        return redirect()->back()->with('success', 'Tarea eliminada con éxito');
    }

    public function toggleCompletion(Request $request, $taskId)
    {
        try {
            $result = $this->taskManagementService->toggleCompletion($taskId);
            return response()->json($result);
        } catch (\Exception $e) {
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

        $this->taskCommentService->addComment($taskId, $validatedData['content']);

        return back()->with('success', 'Comentario agregado exitosamente.');
    }

    public function updateComment(Request $request, $taskId, $commentId)
    {
        $validatedData = $request->validate([
            'content' => 'required|string|max:65535',
        ]);

        $this->taskCommentService->updateComment($commentId, $validatedData['content']);

        return back()->with('success', 'Comentario actualizado exitosamente.');
    }

    public function deleteComment($taskId, $commentId)
    {
        $this->taskCommentService->deleteComment($taskId, $commentId);
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

        // Map employee_id to visible_para for service compatibility
        $validatedData['visible_para'] = $validatedData['employee_id'];
        
        $this->taskManagementService->createTask($validatedData);

        return redirect()->route('empleador.tareas.index')->with('success', 'Tarea creada y asignada exitosamente.');
    }

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

        // Map employee_id to visible_para for service compatibility
        $validatedData['visible_para'] = $validatedData['employee_id'];

        $this->taskManagementService->updateTask($task, $validatedData);

        return redirect()->route('empleador.tareas.index')->with('success', 'Tarea actualizada exitosamente.');
    }

    public function toggleEmployerTaskCompletion(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        
        // Verificar si el usuario autenticado es el empleador de esta tarea
        if ($task->createdBy->id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para modificar esta tarea'], 403);
        }

        try {
            $result = $this->taskManagementService->toggleCompletion($taskId);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado de la tarea: ' . $e->getMessage()
            ], 500);
        }
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

        $this->taskManagementService->updateTask($task, $validatedData);

        return redirect()->route('empleador.tareas.index')->with('success', 'Tarea actualizada con éxito');
    }
}