<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ChatAttachmentController extends Controller
{
    public function download(Message $message)
    {
        // 1. Authorization: Only sender or recipient can download
        if ($message->from_user_id !== Auth::id() && $message->to_user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        // 2. Check if attachment exists
        if (!$message->attachment_path) {
            abort(404, 'El mensaje no tiene archivo adjunto.');
        }

        $path = $message->attachment_path;
        
        // 3. Try to locate the file in public disk (where it was stored)
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->download($path);
        }

        // 4. Fallback: Try identifying if it was stored with full path or just filename
        // Sometimes it might be stored as 'chat_attachments/file.jpg'
        if (Storage::disk('public')->exists('chat_attachments/' . basename($path))) {
             return Storage::disk('public')->download('chat_attachments/' . basename($path));
        }
        
        // 5. Fallback: Local disk check (custom setup case)
        if (Storage::disk('local')->exists('public/' . $path)) {
            return Storage::disk('local')->download('public/' . $path);
        }

        abort(404, 'Archivo físico no encontrado en el servidor.');
    }
}
