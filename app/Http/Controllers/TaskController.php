<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TaskManagementService;
use App\Services\TaskCommentService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
    use AuthorizesRequests;
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
        
        if ($request->user()->cannot('update', $task)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No tienes permiso para editar esta tarea'], 403);
            }
            return back()->with('error', 'No tienes permiso para editar esta tarea.');
        }

        try {
            $this->taskManagementService->updateTask($task, $request->validated());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Tarea actualizada exitosamente.', 
                    'task' => $task->fresh(['comments.user', 'attachments.uploader', 'assignees', 'createdBy'])
                ]);
            }
            return back()->with('success', 'Tarea actualizada exitosamente.');
        } catch (\Throwable $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    // Syntax fix verification
    public function destroy(Request $request, $taskId)
    {
        try {
            $task = Task::findOrFail($taskId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Idempotency: If task is already gone, consider it a success
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'La tarea ya había sido eliminada']);
            }
            return redirect()->back()->with('success', 'La tarea ya había sido eliminada');
        }
        
        if ($request->user()->cannot('delete', $task)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No tienes permiso para eliminar esta tarea'], 403);
            }
            return back()->with('error', 'No tienes permiso para eliminar esta tarea.');
        }

        try {
            \Illuminate\Support\Facades\Log::info("Intentando eliminar tarea $taskId por usuario " . Auth::id());
            $this->taskManagementService->deleteTask($taskId);
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Tarea eliminada con éxito']);
            }
            return redirect()->back()->with('success', 'Tarea eliminada con éxito');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error al eliminar tarea $taskId: " . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error al eliminar tarea: ' . $e->getMessage());
        }
    }

    public function toggleCompletion(Request $request, $taskId)
    {
        if ($request->user()->tipo_usuario === 'empleador') {
            return response()->json(['success' => false, 'message' => 'Solo los profesionales pueden cambiar el estado de las tareas.'], 403);
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
        // Enforce restriction: Employers cannot change status
        if ($request->user()->tipo_usuario === 'empleador') {
            return response()->json(['success' => false, 'message' => 'Solo los profesionales pueden cambiar el estado de las tareas.'], 403);
        }

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
    
    $isEmployerOfTask = $user->tipo_usuario === 'empleador' && (
        $task->assignees()->where('empleador_id', $user->id)->exists() ||
        ($task->createdBy && $task->createdBy->empleador_id === $user->id)
    );

    $canAccess = $user->id === $task->created_by ||
                 $task->assignees->contains($user->id) ||
                 $user->is_superadmin ||
                 $isEmployerOfTask;
    
    if (!$canAccess) {
        abort(403, 'No tienes permiso para descargar este archivo.');
    }    

        $path = $attachment->stored_filename;
        
        // 1. Try exact path on local disk
        if (\Storage::disk('local')->exists($path)) {
            return \Storage::disk('local')->download($path, $attachment->filename);
        }

        // 2. Try hyphen/underscore swap on local disk
        $swappedPath = str_contains($path, 'task-attachments/') 
            ? str_replace('task-attachments/', 'task_attachments/', $path)
            : str_replace('task_attachments/', 'task-attachments/', $path);
            
        if (\Storage::disk('local')->exists($swappedPath)) {
            return \Storage::disk('local')->download($swappedPath, $attachment->filename);
        }

        // 3. Fallback for files stuck on public disk
        if (str_contains($path, 'task-attachments/')) {
            $publicPath = $path;
            if (\Storage::disk('public')->exists($publicPath)) {
                return \Storage::disk('public')->download($publicPath, $attachment->filename);
            }
        }

        abort(404, 'No se pudo encontrar el archivo físico en el almacenamiento.');
    }

    public function getDetails($taskId)
    {
        $task = Task::with(['comments.user', 'attachments.uploader', 'assignees', 'createdBy'])->findOrFail($taskId);
        return response()->json($task);
    }

    public function uploadAttachment(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        
        // Match consistency with DashboardController
        $filename = $file->getClientOriginalName();
        $path = $file->store('task_attachments', 'local'); // Use local disk and underscore

        $attachment = $task->attachments()->create([
            'filename' => $filename,
            'stored_filename' => $path,
            'mime_type' => $file->getMimeType(), // Correct column name
            'file_size' => $file->getSize(),
            'uploaded_by' => request()->user()->id,
        ]);

        return response()->json($attachment->load('uploader'));
    }

    public function deleteAttachment(\App\Models\TaskAttachment $attachment)
    {
        $task = $attachment->task;
        $user = Auth::user();

        // Only the uploader can delete the attachment
        if ($attachment->uploaded_by !== $user->id) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para eliminar este archivo.'], 403);
        }


        if ($attachment->stored_filename) {
            if (\Storage::disk('public')->exists($attachment->stored_filename)) {
                \Storage::disk('public')->delete($attachment->stored_filename);
            } elseif (\Storage::disk('local')->exists($attachment->stored_filename)) {
                \Storage::disk('local')->delete($attachment->stored_filename);
            }
        }

        $attachment->delete();

        return response()->json(['success' => true, 'message' => 'Archivo eliminado correctamente.']);
    }
}