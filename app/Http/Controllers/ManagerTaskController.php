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
    private function checkManagerAccess()
    {
        if (!(auth()->user()->tipo_usuario === 'empleador' || auth()->user()->is_manager)) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
    }

    public function index()
    {
        $tareas = Task::where('created_by', Auth::id())
                      ->with('assignees')
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);
        return view('manager.tasks.index', compact('tareas'));
    }

    public function create()
    {
        $this->checkManagerAccess();
        
        $empleados = auth()->user()->compañerosDeTrabajo();
        return view('manager.tasks.create', compact('empleados'));
    }

    public function store(StoreTaskRequest $request)
    {
        $this->checkManagerAccess();
        
        $validatedData = $request->validated();

        $task = Task::create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'created_by' => auth()->id(),
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'priority' => $validatedData['priority'],
            'completed' => false,
        ]);

        if (isset($validatedData['visible_para'])) {
            $task->assignees()->attach($validatedData['visible_para']);
            
            // Enviar notificación al usuario asignado
            $assignedUser = User::find($validatedData['visible_para']);
            $assignedUser->notify(new NewTaskAssigned($task));
        }

        return redirect()->route('manager.tasks.index')->with('success', 'Tarea creada y asignada exitosamente.');
    }

    public function edit(Task $task)
    {
        Gate::authorize('update', $task);
        $empleados = auth()->user()->compañerosDeTrabajo();
        return view('manager.tasks.edit', compact('task', 'empleados'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);

        try {
            $validatedData = $request->validated();
            $task->update($validatedData);
            
            if (isset($validatedData['visible_para'])) {
                $task->assignees()->sync([$validatedData['visible_para']]);
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
            'content' => 'required|string'
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
            'content' => 'required|string'
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
}