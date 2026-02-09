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
        // Load context (last 10 messages)
        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();
            
        return response()->stream(function () use ($messages, $conversation) {
            // 1. Send immediate ping to show the user we are working
            echo "data: " . json_encode(['content' => '']) . "\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

            // 2. Build context inside the stream (lazy loading)
            
            // 2. Build context
            $messagesArray = $messages->map(function($msg) {
                $payload = [
                    'role' => $msg->role,
                    'content' => $msg->content
                ];
                
                // Image handling (Groq supports vision in some models, but we'll keep it simple for now or compatible)
                if ($msg->attachment_path && $msg->attachment_type === 'image') {
                     $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($msg->attachment_path);
                     if (file_exists($fullPath)) {
                         // OpenAI/Groq format for images is different, but for now let's keep it compatible 
                         // or just strip images if driver is groq and model doesn't support it.
                         // For simplicity in this iteration, we pass text only for Groq unless we confirm vision model.
                         // But Ollama logic was: images: [base64]
                         
                         $driver = config('ai.default');
                         if ($driver === 'ollama') {
                             $payload['images'] = [base64_encode(file_get_contents($fullPath))];
                         } 
                         // For Groq/OpenAI, we would need content: [ {type: text...}, {type: image_url...} ]
                         // We will implement that later if needed.
                     }
                }
                return $payload;
            })->toArray();

            // 3. Inject System Prompt
            $user = Auth::user();
            $systemPrompt = config('ai.system_prompt', 'You are a helpful assistant.');
            
            // Improve context injection
            $contextData = [
                'user_name' => $user->name,
                'user_role' => $user->tipo_usuario,
                'date' => now()->toDateTimeString(),
                'app_name' => config('app.name'),
            ];
            
            foreach ($contextData as $key => $value) {
                $systemPrompt = str_replace(":{$key}", $value, $systemPrompt);
            }
            
            array_unshift($messagesArray, [
                'role' => 'system',
                'content' => $systemPrompt
            ]);

            // 4. Configure Driver
            $driver = config('ai.default', 'groq');
            $config = config("ai.drivers.{$driver}");
            
            $url = $config['url'];
            $model = $config['model'];
            $apiKey = $config['api_key'] ?? null;
            
            \Illuminate\Support\Facades\Log::info("AI Chat connecting to: {$driver} ({$model})");

            // 5. Prepare Payload
            $data = [
                'model' => $model,
                'messages' => $messagesArray,
                'stream' => true,
            ];
            
            if ($driver === 'ollama') {
                $data['keep_alive'] = '5m';
            }

            // 6. Init Curl
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); 
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120);
            
            $headers = ['Content-Type: application/json'];
            if ($apiKey) {
                $headers[] = "Authorization: Bearer {$apiKey}";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $chunk) use ($driver) {
                // OpenAI/Groq format is "data: {...}"
                // Ollama format is " {...} "
                
                $lines = explode("\n", $chunk);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    
                    // Handle "data: [DONE]" for OpenAI/Groq
                    if ($line === 'data: [DONE]') {
                        echo "data: [DONE]\n\n";
                         if (ob_get_level() > 0) ob_flush();
                        flush();
                        continue;
                    }
                    
                    // Parse JSON
                    $jsonStr = $line;
                    if ($driver === 'groq' && str_starts_with($line, 'data: ')) {
                        $jsonStr = substr($line, 6);
                    }
                    
                    $json = json_decode($jsonStr, true);
                    
                    $content = null;
                    $error = null;
                    $done = false;

                    if ($driver === 'ollama') {
                        $content = $json['message']['content'] ?? null;
                        $done = $json['done'] ?? false;
                        $error = $json['error'] ?? null;
                    } else {
                        // Groq / OpenAI
                        $content = $json['choices'][0]['delta']['content'] ?? null;
                        $finishReason = $json['choices'][0]['finish_reason'] ?? null;
                        if ($finishReason) $done = true;
                    }

                    if ($content !== null) {
                        echo "data: " . json_encode(['content' => $content]) . "\n\n";
                        if (ob_get_level() > 0) ob_flush();
                        flush();
                    }
                    
                    if ($error) {
                         \Illuminate\Support\Facades\Log::error("AI API Error: " . $error);
                         echo "data: " . json_encode(['error' => $error]) . "\n\n";
                    }
                }
                return strlen($chunk);
            });

            curl_exec($ch);
            
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                \Illuminate\Support\Facades\Log::error("AI Stream Error: " . $error);
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
