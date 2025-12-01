<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployerTaskCommentController extends Controller
{
    public function addComment(Request $request, Task $task)
    {
        try {
            $this->authorize('update', $task);

            $validatedData = $request->validate([
                'content' => 'required|string|max:65535',
            ]);

            Log::info('Añadiendo comentario a tarea de empleador', [
                'task_id' => $task->id,
                'user_id' => Auth::id(),
                'content' => $validatedData['content']
            ]);

            $comment = $task->comments()->create([
                'content' => $validatedData['content'],
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'comment' => $comment->load('user'),
                'message' => 'Comentario agregado exitosamente.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al añadir comentario a tarea de empleador', [
                'error' => $e->getMessage(),
                'task_id' => $task->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al añadir el comentario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateComment(Request $request, Task $task, Comment $comment)
    {
        $this->authorize('update', $comment);
    
        $validatedData = $request->validate([
            'content' => 'required|string|max:65535',
        ]);
    
        $comment->update($validatedData);
    
        return response()->json([
            'success' => true,
            'comment' => $comment->fresh()->load('user'),
            'message' => 'Comentario actualizado exitosamente.'
        ]);
    }

    public function deleteComment(Task $task, Comment $comment)
{
    $this->authorize('delete', $comment);

    $comment->delete();

    return response()->json([
        'success' => true,
        'message' => 'Comentario eliminado exitosamente.'
    ]);
}
}