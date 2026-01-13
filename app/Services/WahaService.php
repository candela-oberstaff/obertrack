<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WahaService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.waha.base_url', env('WAHA_BASE_URL', 'http://localhost:3000')), '/');
        $this->apiKey = config('services.waha.api_key', env('WAHA_API_KEY'));
    }

    protected function getHeaders()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if ($this->apiKey) {
            $headers['X-Api-Key'] = $this->apiKey;
        }

        return $headers;
    }

    /**
     * Get the session name for a user.
     * We use a predictable naming convention: session_{userId}
     */
    public function getSessionName($userId)
    {
        return "session_{$userId}";
    }

    /**
     * Start a new session for the user
     */
    public function startSession($sessionName)
    {
        try {
            // First try to delete if exists to ensure fresh start
            $this->deleteSession($sessionName);

            $response = Http::withoutVerifying()
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/sessions", [
                    'name' => $sessionName,
                    'config' => [
                        'proxy' => null,
                        'webhooks' => [],
                    ]
                ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error("WAHA startSession error: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Delete session
     */
    public function deleteSession($sessionName)
    {
        try {
             $response = Http::withoutVerifying()
                ->withHeaders($this->getHeaders())
                ->delete("{$this->baseUrl}/api/sessions/{$sessionName}");
            
            return $response->json();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get session status
     */
    public function getSessionStatus($sessionName)
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/api/sessions/{$sessionName}");

            if ($response->successful()) {
                return $response->json();
            }
            // If 404, session doesn't exist
            return ['status' => 'STOPPED'];
        } catch (\Exception $e) {
            return ['status' => 'STOPPED', 'error' => $e->getMessage()];
        }
    }

    /**
     * Get QR Code (Base64 image)
     */
    public function getQrCode($sessionName)
    {
        try {
            // WAHA returns the image binary directly or json depending on endpoint
            // Usually /api/{session}/auth/qr returns image
            $response = Http::withoutVerifying()
                ->withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/api/{$sessionName}/auth/qr");

            if ($response->successful()) {
                $contentType = $response->header('Content-Type');
                if (str_contains($contentType, 'image')) {
                    return 'data:' . $contentType . ';base64,' . base64_encode($response->body());
                }
                return $response->json()['qrcode'] ?? null;
            }
            return null;
        } catch (\Exception $e) {
            Log::error("WAHA getQrCode error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Chat History
     */
    public function getChatHistory($sessionName, $chatId, $limit = 50)
    {
        try {
            // Ensure chatId ends with @c.us
            if (!str_ends_with($chatId, '@c.us')) {
                $chatId .= '@c.us';
            }

            $response = Http::withoutVerifying()
                ->withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/api/{$sessionName}/chats/{$chatId}/messages", [
                    'limit' => $limit,
                    'downloadMedia' => true
                ]);

            if ($response->successful()) {
                return $response->json();
            }
            return [];
        } catch (\Exception $e) {
            Log::error("WAHA getChatHistory error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Send Text Message
     */
    public function sendMessage($sessionName, $chatId, $message)
    {
        try {
            if (!str_ends_with($chatId, '@c.us')) {
                $chatId .= '@c.us';
            }

            $response = Http::withoutVerifying()
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/sendText", [
                    'session' => $sessionName,
                    'chatId' => $chatId,
                    'text' => $message
                ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error("WAHA sendMessage error: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Send Image/File
     * Currently supports passing a URL or base64 data URL
     */
    public function sendFile($sessionName, $chatId, $fileData, $caption = '', $filename = 'file')
    {
        try {
            if (!str_ends_with($chatId, '@c.us')) {
                $chatId .= '@c.us';
            }

            $payload = [
                'session' => $sessionName,
                'chatId' => $chatId,
                'caption' => $caption,
                'file' => [
                    'mimetype' => $this->getMimeType($fileData),
                    'filename' => $filename,
                    // If fileData is a URL, WAHA can handle it if configured, 
                    // otherwise if it's base64, we need to parse it.
                    // For simplicity, assuming fileData is a public URL for now 
                    // or we construct a data object. 
                    // WAHA /api/sendImage expects 'file' object.
                    'url' => $fileData 
                ]
            ];

            // NOTE: Different WAHA versions have different payloads for sendImage/sendFile.
            // Using /api/sendFile which is more generic
            $response = Http::withoutVerifying()
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/sendFile", $payload);

            return $response->json();
        } catch (\Exception $e) {
            Log::error("WAHA sendFile error: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Logout / Stop Session
     */
    public function logout($sessionName)
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/sessions/{$sessionName}/logout");
            
            return $response->json();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function getMimeType($url)
    {
        // Simple helper, or better use a library
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $mimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        return $mimes[strtolower($ext)] ?? 'application/octet-stream';
    }
}
