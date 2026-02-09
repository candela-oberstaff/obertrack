<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class OllamaService
{
    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        // Default to localhost:11434 if not set in .env
        $this->baseUrl = env('OLLAMA_URL', 'http://127.0.0.1:11434');
        // Default model, can be overridden
        $this->model = env('OLLAMA_MODEL', 'llama3.2');
    }

    /**
     * Check if Ollama is running.
     */
    public function isRunning(): bool
    {
        try {
            $response = Http::timeout(2)->get("{$this->baseUrl}/api/tags");
            return $response->successful();
        } catch (ConnectionException $e) {
            return false;
        }
    }

    /**
     * Generate a completion (chat) from Ollama.
     * This method is for non-streaming responses (not recommended for chat UX).
     */
    public function chat(array $messages)
    {
        $response = Http::timeout(300)->post("{$this->baseUrl}/api/chat", [
            'model' => $this->model,
            'messages' => $messages,
            'stream' => false,
        ]);

        return $response->json();
    }

    /**
     * Generate a streaming completion.
     * This yields chunks of the response as they arrive.
     */
    public function chatStream(array $messages)
    {
        // Using PHP's native file functions for streaming as Laravel/Guzzle streaming
        // can sometimes buffer. This is a robust way to handle SSE.
        
        $url = "{$this->baseUrl}/api/chat";
        $data = json_encode([
            'model' => $this->model,
            'messages' => $messages,
            'stream' => true,
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) {
            // Yield the chunk to the caller (Livewire or Controller)
            // In a real generator context, we can't 'yield' from a callback directly easily
            // without a specific structure.
            // For simplicity in Livewire v3, we'll use a callback approach or standard generator.
            
            // Let's print the chunk immediately for the output buffer if we were in a raw PHP script,
            // but for Livewire, we need to return it.
            
            $json = json_decode($chunk, true);
            if (isset($json['message']['content'])) {
                 echo "data: " . $json['message']['content'] . "\n\n";
                 ob_flush();
                 flush();
            }
            return strlen($chunk);
        });

        curl_exec($ch);
        curl_close($ch);
    }
    
    /**
     * Modern streaming using Laravel Http Client (requires careful handling)
     * preferred for clean code if it works with the specific Livewire setup.
     */
     public function streamResponse(array $messages)
     {
         return Http::withOptions(['stream' => true])
             ->post("{$this->baseUrl}/api/chat", [
                 'model' => $this->model,
                 'messages' => $messages,
                 'stream' => true,
             ]);
     }
}
