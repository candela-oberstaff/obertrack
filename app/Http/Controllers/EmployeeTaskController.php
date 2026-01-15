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
        // Fetch only tasks for the current month based on end_date
        $allTasks = $user->assignedTasks()
                         ->with(['assignees', 'createdBy', 'comments', 'attachments'])
                         ->orderBy('end_date', 'desc')
                         ->get();

        $teamTasks = $allTasks;

        $individualTasks = collect([]);

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
        // Permission check
        if (!$task->assignees->contains(Auth::id()) && $task->created_by !== Auth::id()) {
            abort(403);
        }

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

        $newValue = !$task->completed;
        $task->update(['completed' => $newValue ? \Illuminate\Support\Facades\DB::raw('true') : \Illuminate\Support\Facades\DB::raw('false')]);

        return response()->json([
            'success' => true,
            'completed' => $task->completed
        ]);
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