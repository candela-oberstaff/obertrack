<div id="commentsSection-{{ $task->id }}" class="hidden space-y-3">
    <div id="commentsList-{{ $task->id }}" class="space-y-2 max-h-40 overflow-y-auto">
        @foreach ($task->comments as $comment)
            <div id="comment-{{ $comment->id }}" class="bg-gray-50 p-2 rounded-md text-sm">
                <div class="flex items-start justify-between">
                    <div class="space-y-1">
                        <p>
                            <span class="font-medium text-gray-900">{{ $comment->user->name }}:</span>
                            <span id="commentContent-{{ $comment->id }}" class="text-gray-700">{{ $comment->content }}</span>
                        </p>
                        <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                    </div>
                    @if($comment->user_id == auth()->id())
                        <div class="flex space-x-1">
                            <button onclick="editComment({{ $comment->id }})" class="text-blue-600 hover:text-blue-800">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                            <button onclick="deleteComment({{ $comment->id }}, {{ $task->id }})" class="text-red-600 hover:text-red-800">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <form onsubmit="addTaskComment(event, {{ $task->id }})" class="mt-2">
        @csrf
        <textarea id="newComment-{{ $task->id }}" rows="2" class="w-full px-3 py-2 text-sm border rounded-md resize-none focus:ring-blue-500 focus:border-blue-500" placeholder="Añadir un comentario..."></textarea>
        <button type="submit" class="mt-2 w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Comentar
        </button>
    </form>
</div>
