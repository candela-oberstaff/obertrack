<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Services\AiAgentService;
use Illuminate\Support\Facades\Log;

class AiChatStreamController extends Controller
{
    public function __invoke(Request $request, AiAgentService $aiAgent)
    {
        $request->validate([
            'conversation_id' => 'required|exists:ai_conversations,id',
            'message' => 'required|string'
        ]);

        $conversation = AiConversation::where('user_id', Auth::id())
            ->findOrFail($request->conversation_id);

        $user = Auth::user();

        // Get the last user message from the conversation
        $lastUserMessage = $conversation->messages()
            ->where('role', 'user')
            ->orderBy('created_at', 'desc')
            ->first();

        $userText = $lastUserMessage ? $lastUserMessage->content : '';

        return response()->stream(function () use ($aiAgent, $conversation, $user, $userText) {
            // 1. Send immediate "thinking" ping
            echo "data: " . json_encode(['content' => '']) . "\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

            try {
                // 2. Execute the Agent (handles tool calls internally, returns final text)
                $responseContent = $aiAgent->sendMessage($userText, $conversation, $user);

                if (empty($responseContent)) {
                    $responseContent = 'No pude procesar tu solicitud. Inténtalo de nuevo.';
                }

                // 3. Simulate streaming by sending the response in small chunks
                // This gives the "typing" effect while keeping tool execution synchronous
                $chunks = $this->splitIntoChunks($responseContent);
                
                foreach ($chunks as $chunk) {
                    echo "data: " . json_encode(['content' => $chunk]) . "\n\n";
                    if (ob_get_level() > 0) ob_flush();
                    flush();
                    
                    // Small delay between chunks for natural typing feel
                    usleep(8000); // 8ms between chunks
                }

            } catch (\Exception $e) {
                Log::error('AI Agent Stream Error: ' . $e->getMessage());
                echo "data: " . json_encode(['content' => 'Error: ' . $e->getMessage()]) . "\n\n";
                if (ob_get_level() > 0) ob_flush();
                flush();
            }

            // 4. Signal done
            echo "data: [DONE]\n\n";
            if (ob_get_level() > 0) ob_flush();
            flush();

        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Split text into small chunks to simulate streaming/typing effect
     */
    private function splitIntoChunks(string $text): array
    {
        $chunks = [];
        $words = explode(' ', $text);
        $buffer = [];

        foreach ($words as $word) {
            $buffer[] = $word;
            
            // Send chunks of 2 words for fast, natural typing feel
            if (count($buffer) >= 2 || str_contains($word, "\n")) {
                // Add trailing space so words don't merge between chunks
                $chunks[] = implode(' ', $buffer) . ' ';
                $buffer = [];
            }
        }

        // Last partial chunk (no trailing space needed at end)
        if (!empty($buffer)) {
            $chunks[] = implode(' ', $buffer);
        }

        return $chunks;
    }
}
