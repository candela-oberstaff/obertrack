<!-- resources/views/tareas/edit-comment.blade.php -->

<form action="{{ route('tareas.comment.update', $comment->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-4">
        <label for="commentContent" class="block text-gray-700 text-sm font-bold mb-2">Editar Comentario:</label>
        <textarea id="commentContent" name="content" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" rows="3">{{ $comment->content }}</textarea>
    </div>
    <div class="flex items-center justify-between">
        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
            Actualizar Comentario
        </button>
    </div>
</form>
