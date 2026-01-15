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
        
        // Only the author can update their comment
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar este comentario.');
        }
        
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
        
        // Only the author can delete their comment
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para eliminar este comentario.');
        }
            
        return $comment->delete();
    }
}
