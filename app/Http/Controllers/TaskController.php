<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        return redirect()->route('empresa.tareas.index');
    }

    public function create()
    {
        // Redirect to the new dashboard view where creation is inline
        return redirect()->route('empresa.tareas.index');
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

    public function updateStatus(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            
            // Log for debugging
            \Illuminate\Support\Facades\Log::info("UpdateStatus called for Task {$id}", [
                'user' => Auth::id(),
                'input' => $request->all(),
            ]);

            $request->validate([
                'status' => 'required|in:por_hacer,en_proceso,finalizado'
            ]);

            $newStatus = $request->input('status');
            $oldStatus = $task->status;
            
            if ($newStatus === $oldStatus) {
                return response()->json(['success' => true, 'message' => 'El estado no ha cambiado']);
            }

            // Use direct update to handle boolean casing correctly for Postgres
            Task::where('id', $id)->update([
                'status' => $newStatus,
                'completed' => ($newStatus === 'finalizado') ? DB::raw('true') : DB::raw('false'),
                'updated_at' => now()
            ]);
            
            // Refresh model instance for response
            $task->refresh();

            // Notify if changed by an employee
            if (Auth::user()->tipo_usuario === 'empleado') {
                $this->taskManagementService->notifyClientOfStatusChange($task, $oldStatus);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Estado actualizado correctamente',
                'task' => $task->fresh(['comments.user', 'attachments.uploader', 'assignees', 'createdBy'])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Datos inválidos: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error updating status for task {$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()], 500);
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

        // Restriction for 'empleador' removed to allow them to toggle completion.


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

    public function createForProfessional()
    {
        // Redirect to the new dashboard view where creation is inline
        return redirect()->route('empresa.tareas.index');
    }

    public function storeForProfessional(StoreTaskRequest $request)
    {
        $validatedData = $request->validated();
        // Service handles 'professional_id' mapping to assignees
        
        $this->taskManagementService->createTask($validatedData);
        return redirect()->route('empresa.tareas.index')->with('success', 'Tarea creada y asignada exitosamente.');
    }

    public function edit(Task $task)
    {
        // Redirect to the new dashboard view where editing is inline
        return redirect()->route('empresa.tareas.index');
    }

    public function updateForCompany(UpdateTaskRequest $request, Task $task)
    {
        $validatedData = $request->validated();
        // Service handles updates
        
        $this->taskManagementService->updateTask($task, $validatedData);
        return redirect()->route('empresa.tareas.index')->with('success', 'Tarea actualizada exitosamente.');
    }

    public function toggleCompanyTaskCompletion(Request $request, $taskId)
    {

        // Restriction for 'empleador' removed.

        $task = Task::findOrFail($taskId);
        
        // Verificar si el usuario autenticado es la empresa de esta tarea
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

    public function editCompanyTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        
        // Verificar si el usuario autenticado es la empresa de esta tarea
        if ($task->created_by !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para editar esta tarea');
        }

        return view('empresas.tareas.edit', compact('task'));
    }

    public function updateCompanyTask(UpdateTaskRequest $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        
        // Verificar si el usuario autenticado es la empresa de esta tarea
        if ($task->created_by !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para actualizar esta tarea');
        }

        $this->taskManagementService->updateTask($task, $request->validated());
        return redirect()->route('empresa.tareas.index')->with('success', 'Tarea actualizada con éxito');
    }

    public function downloadAttachment(\App\Models\TaskAttachment $attachment)
    {
        $task = $attachment->task;
        $user = auth()->user();

        // Use the 'view' policy to determine access
        // This unifies logic: if you can view the task, you can download its attachments
        if ($user->cannot('view', $task)) {
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
        $task = Task::with(['comments' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'comments.user', 'attachments.uploader', 'assignees', 'createdBy'])->findOrFail($taskId);
        return response()->json($task);
    }

    public function show($id)
    {
        $task = Task::with(['comments' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'comments.user', 'attachments.uploader', 'assignees', 'createdBy'])->findOrFail($id);
        
        if (request()->wantsJson()) {
            return response()->json(['task' => $task]);
        }
        
        // Return view if not JSON (future proofing)
        return view('tareas.show', compact('task'));
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

        return response()->json([
            'success' => true,
            'attachment' => $attachment->load('uploader')
        ]);
    }

    public function deleteAttachment(\App\Models\TaskAttachment $attachment)
    {
        $task = $attachment->task;
        $user = Auth::user();

        // Allow deletion if user is uploader OR task creator (company/manager)
        $canDelete = $attachment->uploaded_by === $user->id || 
                     $task->created_by === $user->id || 
                     ($user->tipo_usuario === 'empleador' && $task->assignees()->where('empleador_id', $user->id)->exists());

        if (!$canDelete) {
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