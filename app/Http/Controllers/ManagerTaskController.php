<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Gate as GateFacade;
use Illuminate\Support\Facades\Validator;
use App\Models\Comment;
use App\Notifications\NewTaskAssigned;


class ManagerTaskController extends Controller
{
    private function checkManagerAccess()
    {
        // if (!(auth()->user()->tipo_usuario === 'empleador' || auth()->user()->is_manager)) {
        //     abort(403, 'No tienes permiso para acceder a esta página.');
        // }
    }

    // public function index()
    // {
    //     $this->checkManagerAccess();
        
    //     $tareas = Task::where('created_by', auth()->id())->get();
    //     return view('manager.tasks.index', compact('tareas'));
    // }

    public function index()
{
    $tareas = Task::where('created_by', Auth::id())
                  ->with('visibleTo')
                  ->orderBy('created_at', 'desc')
                  ->paginate(10); // Esto devuelve una instancia de paginación, no una colección
    return view('manager.tasks.index', compact('tareas'));
}

    public function create()
    {
        $this->checkManagerAccess();
        
        $empleados = auth()->user()->compañerosDeTrabajo();
        return view('manager.tasks.create', compact('empleados'));
    }

    public function store(Request $request)
    {
        $this->checkManagerAccess();
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'visible_para' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => auth()->id(),
            'visible_para' => $request->visible_para,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'priority' => $request->priority,
            'completed' => false,
        ]);

        // Enviar notificación al usuario asignado
        $assignedUser = User::find($request->visible_para);
        $assignedUser->notify(new NewTaskAssigned($task));

        return redirect()->route('manager.tasks.index')->with('success', 'Tarea creada y asignada exitosamente.');
    }



    public function edit(Task $task)
    {
        // GateFacade::authorize('update', $task);
        // $empleados = User::where('empleador_id', Auth::id())->get();
        $empleados = auth()->user()->compañerosDeTrabajo();

        return view('manager.tasks.edit', compact('task', 'empleados'));
    }

    // public function update(Request $request, Task $task)
    // {
    //     GateFacade::authorize('update', $task);

    //     $validatedData = $request->validate([
    //         'title' => 'required|max:255',
    //         'description' => 'required',
    //         'visible_para' => 'required|exists:users,id',
    //         'start_date' => 'required|date',
    //         'end_date' => 'required|date|after:start_date',
    //         'priority' => 'required|in:low,medium,high,urgent',
    //         'completed' => 'boolean',
    //     ]);

    //     $task->update($validatedData);

    //     return redirect()->route('manager.tasks.index')
    //                      ->with('success', 'Tarea actualizada exitosamente.');
    // }

    public function update(Request $request, Task $task)
{
    // GateFacade::authorize('update', $task);

    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'visible_para' => 'required|exists:users,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'priority' => 'required|in:low,medium,high,urgent',
    ]);

    $empleados = auth()->user()->compañerosDeTrabajo();
    if ($validator->fails()) {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        return back()->withErrors($validator)->withInput();
    }

    try {
        $task->update($validator->validated());
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('manager.tasks.index')->with('success', 'Tarea actualizada exitosamente.')->compact('empleados');
    } catch (\Exception $e) {
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
        return back()->with('error', 'Hubo un problema al actualizar la tarea. Por favor, inténtalo de nuevo.');
    }
}

    public function destroy(Task $task)
    {
        // GateFacade::authorize('delete', $task);

        $task->delete();

        return redirect()->route('manager.tasks.index')
                         ->with('success', 'Tarea eliminada exitosamente.');
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
        // if ($comment->user_id !== Auth::id()) {
        //     abort(403);
        // }
    
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
        // if ($comment->user_id !== Auth::id()) {
        //     abort(403);
        // }
    
        $comment->delete();
    
        return response()->json(['success' => true]);
    }
}