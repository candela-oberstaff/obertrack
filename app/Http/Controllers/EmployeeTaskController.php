<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeTaskController extends Controller
{
    public function index()
    {
        $tasks = Task::where('visible_para', Auth::id())
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);
        return view('empleados.tasks.index', compact('tasks'));
    }

    // public function show(Task $task)
    // {
    //     if ($task->visible_para !== Auth::id()) {
    //         abort(403);
    //     }
    //     return view('empleados.tasks.show', compact('task'));
    // }

    public function show(Task $task)
{
    // if ($task->visible_para !== Auth::id() && $task->created_by !== Auth::id()) {
    //     abort(403);
    // }
    $comments = $task->comments()
                     ->whereIn('user_id', [$task->visible_para, $task->created_by])
                     ->with('user')
                     ->orderBy('created_at', 'desc')
                     ->get();
    $task->load('createdBy');
    return view('empleados.tasks.show', compact('task', 'comments'));
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

    public function toggleCompletion(Request $request, Task $task)
    {
        // if ($task->visible_para !== Auth::id()) {
        //     abort(403);
        // }

        $task->update(['completed' => !$task->completed]);

        return response()->json([
            'success' => true,
            'completed' => $task->completed
        ]);
    }
}