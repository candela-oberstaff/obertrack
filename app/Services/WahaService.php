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

    public function getSessionName($userId)
    {
        // WAHA Core only supports 'default' session name. 
        // We force 'default' unless they upgrade to Plus.
        return 'default';
    }

    /**
     * Start a new session for the user
     */
    public function startSession($sessionName, $force = false)
    {
        try {
            Log::info("WAHA: startSession called for [{$sessionName}] (force: " . ($force ? 'true' : 'false') . ")");
            
            // 1. Check current status
            $statusData = $this->getSessionStatus($sessionName);
            $currentStatus = $statusData['status'] ?? 'STOPPED';

            Log::info("WAHA: Status before start for [{$sessionName}] is [{$currentStatus}]");

            // If force is requested, or if it's stuck in a non-working state, delete first
            if ($force || in_array($currentStatus, ['STOPPED', 'FAILED', 'STARTING'])) {
                 Log::info("WAHA: Cleaning up session [{$sessionName}] before (re)start.");
                 $this->deleteSession($sessionName);
                 sleep(2);
            } elseif ($currentStatus !== 'SCAN_QR_CODE' && $currentStatus !== 'WORKING') {
                // If it's something else like AUTHENTICATING, we might want to wait, 
                // but let's be safe and just return current status if it's not "dead"
                return $statusData;
            }

            // 3. Create new session
            Log::info("WAHA: Creating session [{$sessionName}]");
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
                Log::error("WAHA: POST /sessions [{$sessionName}] FAILED ({$response->status()})", ['body' => $response->body()]);
                
                if ($response->status() === 422) {
                     return $this->getSessionStatus($sessionName);
                }
                return ['error' => "Error WAHA ({$response->status()}): " . $response->body()];
            }

            Log::info("WAHA: Session [{$sessionName}] creation request sent successfully.");
            return $response->json();
        } catch (\Exception $e) {
            Log::error("WAHA startSession Exception: " . $e->getMessage());
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
                ->timeout(2)
                ->get("{$this->baseUrl}/api/sessions/{$sessionName}");

            if ($response->successful()) {
                return $response->json();
            }
            
            // Explicitly verify if 404 (Session not found) -> STOPPED
            if ($response->status() === 404) {
                return ['status' => 'STOPPED'];
            }
            
            // If other error (e.g. 500, 422 on GET?), we assume STOPPED but log it
            \Log::warning("WAHA getSessionStatus error {$response->status()}: " . $response->body());
            return ['status' => 'STOPPED', 'errorCode' => $response->status(), 'body' => $response->body()];

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
                    
                    // WAHA format: {"mimetype":"image/png","data":"base64..."}
                    if (isset($data['data'])) {
                        $mimeType = $data['mimetype'] ?? 'image/png';
                        return 'data:' . $mimeType . ';base64,' . $data['data'];
                    }
                    
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

            Log::info("WAHA Sending Text", [
                'url' => "{$this->baseUrl}/api/sendText",
                'session' => $sessionName,
                'chatId' => $chatId,
                'message' => $message
            ]);

            $response = Http::withoutVerifying()
                ->withHeaders($this->getHeaders())
                ->post("{$this->baseUrl}/api/sendText", [
                    'session' => $sessionName,
                    'chatId' => $chatId,
                    'text' => $message
                ]);

            Log::info("WAHA Send Response Raw", ['status' => $response->status(), 'body' => $response->body()]);

            if ($response->failed()) {
                return [
                    'error' => true,
                    'status' => $response->status(),
                    'message' => $response->body() // Return raw body for debugging
                ];
            }

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

    public function getMimeType($url)
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

    /**
     * Check if number exists and get correct ID
     */
    public function checkNumberStatus($sessionName, $phone)
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders($this->getHeaders())
                ->get("{$this->baseUrl}/api/contacts/check-exists", [
                    'session' => $sessionName,
                    'phone' => $phone
                ]);

            if ($response->successful()) {
                return $response->json();
            }
            return null;
        } catch (\Exception $e) {
            Log::error("WAHA checkNumberStatus error: " . $e->getMessage());
            return null;
        }
    }
}
