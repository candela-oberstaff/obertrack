<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Gate;
use App\Models\Comment;
use App\Notifications\NewTaskAssigned;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;


class ManagerTaskController extends Controller
{
    public function __construct(
        private \App\Services\TaskManagementService $taskManagementService
    ) {}
    private function checkManagerAccess()
    {
        if (!(auth()->user()->tipo_usuario === 'empleador' || auth()->user()->is_manager)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
    }

    public function index()
    {
        $user = Auth::user();
        $tareas = $this->taskManagementService->getCompanyTasks($user);
        
        $ownerId = $user->tipo_usuario === 'empleador' ? $user->id : $user->empleador_id;
        $profesionales = \App\Models\User::where('empleador_id', $ownerId)->get();

        $currentUserData = [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ? (str_starts_with($user->avatar, 'http') ? $user->avatar : asset('avatars/' . $user->avatar)) : '',
            'tipo_usuario' => $user->tipo_usuario,
            'is_superadmin' => $user->is_superadmin
        ];

        return view('manager.tasks.index', compact('tareas', 'profesionales', 'currentUserData'));
    }

    public function create()
    {
        $this->checkManagerAccess();
        
        $profesionales = auth()->user()->compañerosDeTrabajo();
        return view('manager.tasks.create', compact('profesionales'));
    }

    public function store(StoreTaskRequest $request)
    {
        $this->checkManagerAccess();
        
        $validatedData = $request->validated();

        $this->taskManagementService->createTask($validatedData);

        return redirect()->route('manager.tasks.index')->with('success', 'Tarea creada y asignada exitosamente.');
    }

    public function edit(Task $task)
    {
        Gate::authorize('update', $task);
        $profesionales = auth()->user()->compañerosDeTrabajo();
        return view('manager.tasks.edit', compact('task', 'profesionales'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);

        try {
            $validatedData = $request->validated();
            $task->update($validatedData);
            
            if (isset($validatedData['visible_para'])) {
                $task->assignees()->sync([$validatedData['visible_para']]);
            } elseif (isset($validatedData['assignees'])) {
                $task->assignees()->sync($validatedData['assignees']);
            }

            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }
            return redirect()->route('manager.tasks.index')->with('success', 'Tarea actualizada exitosamente.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Hubo un problema al actualizar la tarea. Por favor, inténtalo de nuevo.');
        }
    }

    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);
        $task->delete();
        return redirect()->route('manager.tasks.index')->with('success', 'Tarea eliminada exitosamente.');
    }






    public function addComment(Request $request, Task $task)
    {
        $request->validate([
            'content' => 'required|string|max:500'
        ]);
    
        $comment = new Comment([
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);
    
        $task->comments()->save($comment);
    
        return response()->json([
            'success' => true,
            'comment' => $comment->load('user')
        ]);
    }
    
    public function updateComment(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }
    
        $request->validate([
            'content' => 'required|string|max:500'
        ]);
    
        $comment->update(['content' => $request->content]);
    
        return response()->json([
            'success' => true,
            'comment' => $comment->load('user')
        ]);
    }
    
    public function deleteComment(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403);
        }
    
        $comment->delete();
    
        return response()->json(['success' => true]);
    }

    public function uploadFile(Request $request, Task $task)
    {
        $this->checkManagerAccess();

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path = $file->store('task-attachments', 'public');

        $attachment = new \App\Models\TaskAttachment();
        $attachment->task_id = $task->id;
        $attachment->uploaded_by = Auth::id();
        $attachment->filename = $filename;
        $attachment->stored_filename = $path;
        $attachment->mime_type = $file->getMimeType();
        $attachment->file_size = $file->getSize();
        $attachment->save();

        // Load uploader for response
        $attachment->load('uploader');

        return response()->json([
            'success' => true,
            'attachment' => $attachment
        ]);
    }
}