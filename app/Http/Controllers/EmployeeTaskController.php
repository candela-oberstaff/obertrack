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
        $user = Auth::user();
        // Fetch all assigned tasks with their assignees to determine if they are team or individual
        $allTasks = $user->assignedTasks()
                         ->with(['assignees', 'createdBy', 'comments', 'attachments']) // Eager load
                         ->orderBy('created_at', 'desc')
                         ->get();

        $teamTasks = $allTasks->filter(function ($task) {
            return $task->assignees->count() > 1;
        });

        $individualTasks = $allTasks->filter(function ($task) {
            return $task->assignees->count() <= 1; // Assuming 1 assignee, which is the current user
        });

        $pendingTasksCount = $allTasks->where('completed', false)->count();
        $completedTasksCount = $allTasks->where('completed', true)->count();

        return view('empleados.tasks.index', compact('teamTasks', 'individualTasks', 'pendingTasksCount', 'completedTasksCount'));
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
        return view('empleados.tasks.show', compact('task', 'comments'));
    }

    public function addComment(Request $request, Task $task)
    {
        // ... (same as before)
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

    // ... (updateComment and deleteComment can remain same as they check comment ownership)

    public function toggleCompletion(Request $request, Task $task)
    {
        if (!$task->assignees->contains(Auth::id())) {
            abort(403);
        }

        $task->update(['completed' => !$task->completed]);

        return response()->json([
            'success' => true,
            'completed' => $task->completed
        ]);
    }
}