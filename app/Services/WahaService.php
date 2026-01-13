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
            // 1. Check if session already exists
            $statusData = $this->getSessionStatus($sessionName);
            $currentStatus = $statusData['status'] ?? null;

            if ($currentStatus && $currentStatus !== 'STOPPED' && !isset($statusData['errorCode'])) {
                // Session exists and is distinct from STOPPED (e.g. WORKING, STARTING, SCAN_QR_CODE)
                Log::info("WAHA Session {$sessionName} already exists and is {$currentStatus}. Reusing.");
                return $statusData;
            }

            // 2. If STOPPED, try to start it explicitly (instead of recreate)
            // Some WAHA versions need explicit start if it persists but is stopped.
            // But usually POST /sessions handles this if we don't send config? 
            // Let's try to delete ONLY if we got a weird error or if we really want fresh.
            // Actually, to be safe and fast:
            // If it's STOPPED or 404, we try to create (POST /sessions). 
            // If POST fails with 422, it means it exists, so we try POST /sessions/{name}/start?
            // No, standard WAHA flow: POST /api/sessions creates and starts.
            
            // If 404 (does not exist), we create.
            // If STOPPED (exists), we might need to delete first OR check if POST /sessions overwrites.
            // WAHA usually throws 422 if exists.
            
            if ($currentStatus === 'STOPPED' && !isset($statusData['errorCode'])) {
                 // Try to delete to force fresh config, BUT do it quickly without wait loop?
                 // Or better: try to START it first.
                 // Assuming we don't have a specific 'start' endpoint implemented here yet?
                 // Let's try to delete but WITHOUT the long wait loop, just one shot.
                 $this->deleteSession($sessionName);
                 sleep(1); // minimal wait
            }

            // 3. Create new session
            $response = Http::withoutVerifying()
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/sessions", [
                    'name' => $sessionName,
                    'config' => [
                        'proxy' => null,
                        'webhooks' => [],
                    ]
                ]);

            if ($response->failed()) {
                if ($response->status() === 422) {
                    // It exists. 
                     Log::info("WAHA Session Create 422. Assuming it exists. Checking status again.");
                     return $this->getSessionStatus($sessionName);
                }
                return ['error' => "Error WAHA ({$response->status()}): " . $response->body()];
            }

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
            
            // Explicitly verify if 404 (Session not found) -> STOPPED
            if ($response->status() === 404) {
                return ['status' => 'STOPPED'];
            }
            
            // If other error (e.g. 500, 422 on GET?), we assume STOPPED but log it
            \Log::warning("WAHA getSessionStatus error {$response->status()}: {$response->body()}");
            return ['status' => 'STOPPED', 'errorCode' => $response->status()];

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
            $response = Http::withoutVerifying()
                ->withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/api/{$sessionName}/auth/qr");

            if ($response->successful()) {
                $contentType = $response->header('Content-Type');
                
                // Case 1: Direct image response
                if (str_contains($contentType, 'image')) {
                    return 'data:' . $contentType . ';base64,' . base64_encode($response->body());
                }
                
                // Case 2: JSON response with base64 QR
                if (str_contains($contentType, 'json')) {
                    $data = $response->json();
                    
                    // Try different possible JSON structures
                    if (isset($data['qr'])) {
                        // If already base64 data URL
                        if (str_starts_with($data['qr'], 'data:image')) {
                            return $data['qr'];
                        }
                        // If just base64 string
                        return 'data:image/png;base64,' . $data['qr'];
                    }
                    
                    if (isset($data['qrcode'])) {
                        if (str_starts_with($data['qrcode'], 'data:image')) {
                            return $data['qrcode'];
                        }
                        return 'data:image/png;base64,' . $data['qrcode'];
                    }
                    
                    // Log unexpected JSON structure
                    Log::warning("WAHA QR JSON unexpected format: " . json_encode($data));
                }
            }
            
            // Log failure
            Log::warning("WAHA getQrCode failed - Status: {$response->status()}, Body: " . substr($response->body(), 0, 200));
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
