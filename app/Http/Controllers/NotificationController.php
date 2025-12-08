<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark a task as read for the current user
     */
    public function markTaskAsRead(Request $request, Task $task)
    {
        $user = Auth::user();

        // Only allow marking as read if the task is assigned to the user
        if ($task->visible_para !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Create or update the read status
        TaskRead::updateOrCreate(
            ['task_id' => $task->id, 'user_id' => $user->id],
            ['read_at' => now()]
        );

        return response()->json(['success' => true, 'message' => 'Tarea marcada como leída']);
    }

    /**
     * Mark all tasks as read for the current user
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();

        // Get all unread tasks assigned to the user
        $unreadTasks = Task::where('visible_para', $user->id)
            ->whereDoesntHave('readBy', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->pluck('id');

        // Mark all as read
        foreach ($unreadTasks as $taskId) {
            TaskRead::updateOrCreate(
                ['task_id' => $taskId, 'user_id' => $user->id],
                ['read_at' => now()]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Todas las tareas marcadas como leídas',
            'count' => $unreadTasks->count()
        ]);
    }
}
