<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AiConversation;
use App\Models\AiMessage;
use Illuminate\Support\Facades\Http;

class AiChatStreamController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:ai_conversations,id',
            'message' => 'required|string' // The user's last message, or we can just load context
        ]);

        $conversation = AiConversation::where('user_id', Auth::id())
            ->findOrFail($request->conversation_id);

        // Load context (last 10 messages)
        // We need to include the user's latest message which might just have been saved
        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get()
            ->map(function($msg) {
                return [
                    'role' => $msg->role,
                    'content' => $msg->content
                ];
            })
            ->toArray();

        // Prepare context for Ollama
        $context = $messages;

        return response()->stream(function () use ($context) {
            $ollamaUrl = env('OLLAMA_URL');
            if (empty($ollamaUrl)) {
                 $ollamaUrl = 'http://109.199.104.87:11434';
            }
            $url = $ollamaUrl . '/api/chat';
            $model = env('OLLAMA_MODEL', 'llama3.2');
            
            \Illuminate\Support\Facades\Log::info("Connecting to Ollama at: " . $url);
            
            $data = [
                'model' => $model,
                'messages' => $context,
                'stream' => true,
            ];

            // Send initial ping to keep connection alive and show responsiveness
            echo "data: " . json_encode(['content' => '']) . "\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

            // Open connection to Ollama with timeouts
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); 
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // 5 seconds to connect
            curl_setopt($ch, CURLOPT_TIMEOUT, 120); // 2 minutes max for generation
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $chunk) {
                // Ollama sends JSON objects line by line, or sometimes partial chunks
                // We need to parse this and send SSE format
                
                // Note: In a raw curl callback, $chunk might be multiple lines or partial lines.
                // For simplicity/robustness in this specific setup, we'll try to find valid JSON lines.
                
                // However, directly echoing the chunk might break SSE if it's not formatted.
                // Ollama sends: {"model":..., "created_at":..., "message":{"role":"assistant","content":"H"}, "done":false}
                
                $lines = explode("\n", $chunk);
                foreach ($lines as $line) {
                    if (empty(trim($line))) continue;
                    
                    $json = json_decode($line, true);
                    
                    if (isset($json['message']['content'])) {
                        $content = $json['message']['content'];
                        // Escape newlines for SSE data payload logic if needed, but usually just sending data is fine.
                        // We'll send a JSON object as data
                        echo "data: " . json_encode(['content' => $content]) . "\n\n";
                        
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                    
                    if (isset($json['done']) && $json['done'] === true) {
                        echo "data: [DONE]\n\n";
                        if (ob_get_level() > 0) {
                            ob_flush();
                        }
                        flush();
                    }
                }
                
                return strlen($chunk);
            });

            curl_exec($ch);
            
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                \Illuminate\Support\Facades\Log::error("Ollama Stream Error: " . $error);
                echo "data: " . json_encode(['error' => $error]) . "\n\n";
            }
            
            curl_close($ch);

        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
