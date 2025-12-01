<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CommentController extends Controller
{

    use AuthorizesRequests;

    public function index($taskId)
    {
        $task = Task::findOrFail($taskId);
        $comments = $task->comments()->with('user')->get();
        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'content' => 'required|string',
        ]);
    
        $comment = Comment::create([
            'task_id' => $request->task_id,
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);
    
        return response()->json($comment->load('user'));
    }

    public function update(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $this->authorize('update', $comment);
    
            $request->validate([
                'content' => 'required|string',
            ]);
    
            $comment->update(['content' => $request->content]);
    
            return response()->json([
                'success' => true,
                'comment' => $comment->load('user'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function destroy(Comment $comment)
    {
        try {
            $this->authorize('delete', $comment);
    
            $comment->delete();
    
            return response()->json(['success' => true, 'message' => 'Comentario eliminado con éxito']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }













    

    public function storeEmployerComment(Request $request)
    {
        $validatedData = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'content' => 'required|string'
        ]);

        $task = Task::findOrFail($validatedData['task_id']);

        // Verificar si el usuario autenticado es el empleador de esta tarea
        if ($task->createdBy->id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para comentar en esta tarea'], 403);
        }

        $comment = new Comment([
            'content' => $validatedData['content'],
            'user_id' => Auth::id(),
            'task_id' => $validatedData['task_id']
        ]);

        $comment->save();

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user'),
            'message' => 'Comentario añadido con éxito'
        ]);
    }

    public function updateEmployerComment(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        // Verificar si el usuario autenticado es el autor del comentario
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para editar este comentario'], 403);
        }

        $validatedData = $request->validate([
            'content' => 'required|string'
        ]);

        $comment->update($validatedData);

        return response()->json([
            'success' => true,
            'comment' => $comment->fresh()->load('user'),
            'message' => 'Comentario actualizado con éxito'
        ]);
    }

    public function destroyEmployerComment($id)
    {
        $comment = Comment::findOrFail($id);

        // Verificar si el usuario autenticado es el autor del comentario o el empleador de la tarea
        if ($comment->user_id !== Auth::id() && $comment->task->createdBy->id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'No tienes permiso para eliminar este comentario'], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comentario eliminado con éxito'
        ]);
    }

    
}