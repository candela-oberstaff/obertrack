<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTaskRequest;
use App\Services\TaskManagementService;

class ProfessionalTaskController extends Controller
{
    public function __construct(
        private TaskManagementService $taskManagementService
    ) {}

    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        
        // If no assignees selected, default to self. 
        // If assignees are selected, use them.
        // We do NOT force assign to self if others are selected, unless the user selects themselves.
        if (empty($data['assignees'])) {
            $data['assignees'] = [\Illuminate\Support\Facades\Auth::id()];
        }
        
        $this->taskManagementService->createTask($data);

        // Redirect based on user role
        if (Auth::user()->tipo_usuario === 'empleador') {
            return redirect()->route('empresa.tareas.index')->with('success', 'Tarea creada exitosamente.');
        }

        if (Auth::user()->is_manager) {
            return redirect()->route('profesionales.tasks.index')->with('success', 'Tarea creada exitosamente.');
        }
        
        return redirect()->route('profesionales.tasks.index')->with('success', 'Tarea creada exitosamente.');
    }

    public function update(Request $request, Task $task)
    {
        // Permission check: Creator, Manager, or Superadmin
        $canEdit = $task->created_by === Auth::id() || Auth::user()->tipo_usuario === 'empleador' || Auth::user()->is_manager || Auth::user()->is_superadmin;

        if (!$canEdit) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No tienes permiso para editar esta tarea'], 403);
            }
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'end_date' => 'required|date',
            'assignees' => 'sometimes|array',
        ]);

        try {
            $this->taskManagementService->updateTask($task, $validated);

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

    public function destroy(Task $task)
    {
        // Permission check: Creator, Manager, or Superadmin
        $canDelete = $task->created_by === Auth::id() || Auth::user()->tipo_usuario === 'empleador' || Auth::user()->is_manager || Auth::user()->is_superadmin;

        if (!$canDelete) {
            abort(403);
        }

        $task->delete();

        return response()->json(['success' => true]);
    }

    public function index()
    {
        $user = Auth::user();
        
        // Fetch tasks where user is assignee OR creator
        // We use the service or build a query similar to getCompanyTasks but scoped to the professional
        $allTasks = Task::where(function($q) use ($user) {
                            $q->where('created_by', $user->id)
                              ->orWhereHas('assignees', function($sub) use ($user) {
                                  $sub->where('users.id', $user->id);
                              });
                         })
                         ->with(['assignees', 'createdBy', 'comments.user', 'attachments.uploader'])
                         ->orderBy('end_date', 'desc')
                         ->get()
                         ->map(function($task) {
                             // Preprocess comments to ensure user names are available
                             $task->comments->transform(function($comment) {
                                 $userName = null;
                                 $userAvatar = null;
                                 
                                 // 1. Try loaded relation
                                 if ($comment->user) {
                                     $userName = $comment->user->name;
                                     $userAvatar = $comment->user->avatar;
                                 } 
                                 // 2. Try auth user fallback
                                 else if ($comment->user_id && $comment->user_id == auth()->id()) {
                                     $userName = auth()->user()->name;
                                     $userAvatar = auth()->user()->avatar;
                                 }
                                 // 3. Direct DB query
                                 else if ($comment->user_id) {
                                     try {
                                         $dbUser = \App\Models\User::withoutGlobalScopes()->find($comment->user_id);
                                         if ($dbUser) {
                                             $userName = $dbUser->name;
                                             $userAvatar = $dbUser->avatar;
                                         }
                                     } catch (\Exception $e) { /* ignore */ }
                                 }
                                 
                                 // 4. Final Fallback
                                 if (!$userName) {
                                     $userName = $comment->user_id ? 'Usuario ' . $comment->user_id : 'Usuario Anónimo';
                                 }

                                 // Add user_name attribute for template access
                                 $comment->user_name = $userName;
                                 $comment->user_avatar = $userAvatar;
                                 
                                 return $comment;
                             });
                             
                             return $task;
                         });

        $teamTasks = $allTasks;
        $individualTasks = collect([]);

        $pendingTasksCount = $allTasks->where('completed', false)->count();
        $completedTasksCount = $allTasks->where('completed', true)->count();

        // Fetch colleagues for the assignment modal
        $profesionales = $user->compañerosDeTrabajo()->filter(function ($colleague) {
            return $colleague->tipo_usuario !== 'empleador';
        })->sortBy('name');

        // Mark tasks as read
        $allTasks->each(function ($task) use ($user) {
            if (!$task->isReadBy($user->id)) {
                $task->readBy()->attach($user->id, ['read_at' => now()]);
            }
        });

        return view('profesionales.tasks.index', compact('teamTasks', 'individualTasks', 'pendingTasksCount', 'completedTasksCount', 'profesionales'));
    }

    public function show(Task $task)
    {
        // Check if user is an assignee or creator
        if (!$task->assignees->contains(Auth::id()) && $task->created_by !== Auth::id()) {
            abort(403);
        }
        
        $allowedUserIds = $task->assignees->pluck('id')->push($task->created_by)->unique();

        $comments = $task->comments()
                         ->whereIn('user_id', $allowedUserIds)
                         ->with('user')
                         ->orderBy('created_at', 'desc')
                         ->get();
        $task->load('createdBy');
        return view('profesionales.tasks.show', compact('task', 'comments'));
    }

    public function addComment(Request $request, Task $task)
    {
        // Permission check
        if (!$task->assignees->contains(Auth::id()) && $task->created_by !== Auth::id()) {
            abort(403);
        }

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
        
        // Return structured data to match what the frontend expects
        $comment->load('user');
        
        return response()->json([
            'success' => true,
            'comment' => $comment
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

    public function toggleCompletion(Request $request, Task $task)
    {
        if (!$task->assignees->contains(Auth::id())) {
            abort(403);
        }

        $result = app(\App\Services\TaskManagementService::class)->toggleCompletion($task->id);

        return response()->json($result);
    }

    public function uploadFile(Request $request, Task $task)
    {
        // Permission check
        if (!$task->assignees->contains(Auth::id()) && $task->created_by !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

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