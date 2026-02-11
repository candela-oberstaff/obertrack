<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.groq.com/openai/v1';
    protected string $model;
    protected int $maxRetries = 3;

    public function __construct()
    {
        $this->apiKey = config('ai.drivers.groq.api_key', env('GROQ_API_KEY', ''));
        $this->model = config('ai.drivers.groq.model', 'llama-3.3-70b-versatile');
    }

    /**
     * Send a message to Groq with optional tools, with automatic retry on rate limits
     */
    public function chat(array $messages, array $tools = [])
    {
        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.1,
        ];

        if (!empty($tools)) {
            $payload['tools'] = $tools;
            $payload['tool_choice'] = 'auto';
        }

        $lastException = null;

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $response = Http::withToken($this->apiKey)
                    ->timeout(60)
                    ->post("{$this->baseUrl}/chat/completions", $payload);

                if ($response->successful()) {
                    return $response->json();
                }

                $status = $response->status();
                $body = $response->body();

                // Rate limit (429) — retry with short backoff, but fail fast if wait is too long
                if ($status === 429) {
                    $retryAfter = $response->header('retry-after');
                    $waitSeconds = $retryAfter ? min((int)$retryAfter, 10) : (2 ** $attempt); // max 10s
                    
                    // If Groq says wait more than 10s, don't block the request — fail fast
                    if ($retryAfter && (int)$retryAfter > 10) {
                        Log::warning("Groq rate limited (429). retry-after={$retryAfter}s is too long, failing fast.");
                        throw new \Exception("El servicio de IA está temporalmente saturado. Por favor intenta de nuevo en un minuto.");
                    }
                    
                    Log::warning("Groq rate limited (429). Retry {$attempt}/{$this->maxRetries} in {$waitSeconds}s");
                    sleep($waitSeconds);
                    continue;
                }

                // Other errors — don't retry
                Log::error("Groq API Error [{$status}]: {$body}");
                throw new \Exception("Error connecting to Groq AI: {$status}");

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                // Network timeout — retry
                Log::warning("Groq connection timeout. Retry {$attempt}/{$this->maxRetries}");
                $lastException = $e;
                sleep(2);
                continue;
            }
        }

        // All retries exhausted
        throw $lastException ?? new \Exception('Groq API: Rate limit exceeded after retries. Please wait a moment and try again.');
    }
}
