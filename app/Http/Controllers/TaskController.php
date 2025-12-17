<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TaskManagementService;
use App\Services\TaskCommentService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    public function __construct(
        private TaskManagementService $taskManagementService,
        private TaskCommentService $taskCommentService
    ) {}

    public function index()
    {
        // Redirect to the new dashboard view
        return redirect()->route('empleador.tareas.index');
    }

    public function create()
    {
        // Redirect to the new dashboard view where creation is inline
        return redirect()->route('empleador.tareas.index');
    }

    public function store(StoreTaskRequest $request)
    {
        $this->taskManagementService->createTask($request->validated());
        return back()->with('success', 'Tarea creada exitosamente.');
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        $task = Task::findOrFail($id);
        $this->taskManagementService->updateTask($task, $request->validated());
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
        // Redirect to the new dashboard view where creation is inline
        return redirect()->route('empleador.tareas.index');
    }

    public function storeForEmployee(StoreTaskRequest $request)
    {
        $validatedData = $request->validated();
        // Service handles 'employee_id' mapping to assignees
        
        $this->taskManagementService->createTask($validatedData);
        return redirect()->route('empleador.tareas.index')->with('success', 'Tarea creada y asignada exitosamente.');
    }

    public function edit(Task $task)
    {
        // Redirect to the new dashboard view where editing is inline
        return redirect()->route('empleador.tareas.index');
    }

    public function updateForEmployer(UpdateTaskRequest $request, Task $task)
    {
        $validatedData = $request->validated();
        // Service handles updates
        
        $this->taskManagementService->updateTask($task, $validatedData);
        return redirect()->route('empleador.tareas.index')->with('success', 'Tarea actualizada exitosamente.');
    }

    public function toggleEmployerTaskCompletion(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        
        // Verificar si el usuario autenticado es el empleador de esta tarea
        if ($task->created_by !== Auth::id()) { // Using created_by instead of relation for now, simplified
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
        if ($task->created_by !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para editar esta tarea');
        }

        return view('empleador.tareas.edit', compact('task'));
    }

    public function updateEmployerTask(UpdateTaskRequest $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        
        // Verificar si el usuario autenticado es el empleador de esta tarea
        if ($task->created_by !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para actualizar esta tarea');
        }

        $this->taskManagementService->updateTask($task, $request->validated());
        return redirect()->route('empleador.tareas.index')->with('success', 'Tarea actualizada con éxito');
    }

    public function downloadAttachment(\App\Models\TaskAttachment $attachment)
    {
        $task = $attachment->task;
        
        // Check if user has access to this task
        $user = auth()->user();
        
        $canAccess = $user->id === $task->created_by ||
                     $task->assignees->contains($user->id) ||
                     $user->is_superadmin;
        
        if (!$canAccess) {
            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        return \Storage::disk('local')->download(
            $attachment->stored_filename,
            $attachment->filename
        );
    }
}