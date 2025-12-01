<?php



namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployerTaskController extends Controller
{
    public function index()
    {
        $tasks = Task::where('created_by', Auth::id())->with('visibleTo')->get();
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

        $task->update($validatedData);

        return redirect()->route('empleadores.ver_tareas_empleados')->with('success', 'Tarea actualizada exitosamente.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return redirect()->route('empleadores.ver_tareas_empleados')->with('success', 'Tarea eliminada exitosamente.');
    }

    public function toggleCompletion(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->completed = !$task->completed;
        $task->save();

        return response()->json([
            'success' => true,
            'completed' => $task->completed,
            'message' => $task->completed ? 'Tarea marcada como completada' : 'Tarea marcada como en progreso'
        ]);
    }

    public function addComment(Request $request, $taskId)
{
    $validatedData = $request->validate([
        'content' => 'required|string',
    ]);

    $task = Task::findOrFail($taskId);

    $comment = new Comment();
    $comment->content = $validatedData['content'];
    $comment->task_id = $taskId;
    $comment->user_id = Auth::id();

    if ($comment->save()) {
        // Cargar la relación del usuario para incluirla en la respuesta
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
                ],
                'task_id' => $comment->task_id
            ]
        ]);
    } else {
        return response()->json(['success' => false, 'message' => 'Error al agregar el comentario'], 500);
    }
}


    public function updateComment(Request $request, $taskId, $commentId)
    {
        $comment = Comment::find($commentId);
        $comment->content = $request->input('content');
        $comment->save();

        return response()->json(['success' => true, 'comment' => $comment]);
    }

    public function deleteComment($taskId, $commentId)
    {
        $comment = Comment::find($commentId);
        $comment->delete();

        return response()->json(['success' => true]);
    }
}