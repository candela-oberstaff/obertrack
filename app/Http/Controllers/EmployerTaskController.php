<?php



namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class EmployerTaskController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private \App\Services\TaskManagementService $taskManagementService,
        private \App\Services\TaskCommentService $taskCommentService
    ) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $teamTasks = $this->taskManagementService->getCompanyTasks($user, $request->all())->values();

        // 2. Employees (needed for modals and dropdowns)
        $ownerId = $user->tipo_usuario === 'empleador' ? $user->id : $user->empleador_id;
        
        if ($user->isSuperAdmin()) {
            $employees = \App\Models\User::where('tipo_usuario', 'empleado')->get();
        } else {
             $employees = \App\Models\User::where('empleador_id', $ownerId)->get();
        }

        return view('empleadores.ver_tareas_empleados', compact('teamTasks', 'employees'));
    }

    public function create()
    {
        $empleados = Auth::user()->empleados;
        return view('empleadores.ver_tareas_empleados', compact('empleados'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'assignees' => 'required|array|min:1',
            'assignees.*' => 'exists:users,id',
        ]);

        // Service handles 'assignees' directly
        // $validatedData['assignees'] = [$validatedData['employee_id']]; // Handled in Service

        $this->taskManagementService->createTask($validatedData);

        return redirect()->route('empleador.tareas.index')->with('success', 'Tarea creada y asignada exitosamente.');
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $empleados = Auth::user()->empleados;
        return view('empleadores.ver_tareas_empleados', compact('task', 'empleados'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'employee_id' => 'required|exists:users,id',
        ]);

        $this->taskManagementService->updateTask($task, $validatedData);

        return redirect()->route('empleador.tareas.index')->with('success', 'Tarea actualizada exitosamente.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $this->taskManagementService->deleteTask($task->id);
        return redirect()->route('empleador.tareas.index')->with('success', 'Tarea eliminada exitosamente.');
    }

    public function toggleCompletion(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        try {
            $result = $this->taskManagementService->toggleCompletion($task->id);
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
            'content' => 'required|string|max:500',
        ]);

        $comment = $this->taskCommentService->addComment($taskId, $validatedData['content']);
        $comment->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Comentario agregado con éxito',
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => $comment->created_at,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'avatar' => $comment->user->avatar,
                ],
                'task_id' => $comment->task_id
            ]
        ]);
    }

    public function updateComment(Request $request, $taskId, $commentId)
    {
        $validatedData = $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $comment = $this->taskCommentService->updateComment($commentId, $validatedData['content']);

        return response()->json(['success' => true, 'comment' => $comment]);
    }

    public function deleteComment($taskId, $commentId)
    {
        $this->taskCommentService->deleteComment($taskId, $commentId);
        return response()->json(['success' => true]);
    }

    public function uploadFile(Request $request, $taskId)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $task = Task::findOrFail($taskId);
        $file = $request->file('file');

        $filename = $file->getClientOriginalName();
        // Standardize to use 'local' disk and 'task_attachments' folder to match download logic
        $path = $file->store('task_attachments', 'local');

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