<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class TaskCommentService
{
    /**
     * Add a comment to a task
     */
    public function addComment($taskId, $content)
    {
        return Comment::create([
            'content' => $content,
            'task_id' => $taskId,
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Update an existing comment
     */
    public function updateComment($commentId, $content)
    {
        $comment = Comment::findOrFail($commentId);
        
        // Check authorization if needed (usually handled in controller/policy)
        
        $comment->update([
            'content' => $content,
            'updated_by' => Auth::id(),
        ]);

        return $comment;
    }

    /**
     * Delete a comment
     */
    public function deleteComment($taskId, $commentId)
    {
        $comment = Comment::where('task_id', $taskId)
            ->where('id', $commentId)
            ->firstOrFail();
            
        return $comment->delete();
    }
}
