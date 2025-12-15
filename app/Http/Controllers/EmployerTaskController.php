<?php



namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerTaskController extends Controller
{
    public function __construct(
        private \App\Services\TaskManagementService $taskManagementService,
        private \App\Services\TaskCommentService $taskCommentService
    ) {}

    public function index()
    {
        $tasks = Task::where('created_by', Auth::id())->with('assignees')->get();
        return view('empleadores.ver_tareas_empleados', compact('tasks'));
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
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'employee_id' => 'required|exists:users,id',
        ]);

        // Service now handles 'employee_id' or 'assignees'
        // $validatedData['assignees'] = [$validatedData['employee_id']]; // Handled in Service

        $this->taskManagementService->createTask($validatedData);

        return redirect()->route('empleadores.ver_tareas_empleados')->with('success', 'Tarea creada y asignada exitosamente.');
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
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'employee_id' => 'required|exists:users,id',
        ]);

        $this->taskManagementService->updateTask($task, $validatedData);

        return redirect()->route('empleadores.ver_tareas_empleados')->with('success', 'Tarea actualizada exitosamente.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $this->taskManagementService->deleteTask($task->id);
        return redirect()->route('empleadores.ver_tareas_empleados')->with('success', 'Tarea eliminada exitosamente.');
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
            'content' => 'required|string',
        ]);

        $comment = $this->taskCommentService->addComment($taskId, $validatedData['content']);

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
                ],
                'task_id' => $comment->task_id
            ]
        ]);
    }

    public function updateComment(Request $request, $taskId, $commentId)
    {
        $validatedData = $request->validate([
            'content' => 'required|string',
        ]);

        $comment = $this->taskCommentService->updateComment($commentId, $validatedData['content']);

        return response()->json(['success' => true, 'comment' => $comment]);
    }

    public function deleteComment($taskId, $commentId)
    {
        $this->taskCommentService->deleteComment($taskId, $commentId);
        return response()->json(['success' => true]);
    }
}