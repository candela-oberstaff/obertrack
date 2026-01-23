<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\WahaService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class WhatsappChat extends Component
{
    use WithFileUploads;

    public $sessionStatus = 'STOPPED'; // STOPPED, SCAN_QR_CODE, WORKING, STARTING
    public $qrCodeBase64 = null;
    public $contacts = [];
    public $selectedContactId = null;
    public $selectedPhone = null;
    public $messages = [];
    public $messageText = '';
    public $attachment;
    public $startTime = 0;
    public $qrScanned = false;
    
    // UI States
    public $isLoadingContact = false;
    
    // Polling control
    public $pollInterval = 3; 

    public function boot(WahaService $wahaService)
    {
        $this->wahaService = $wahaService;
    }

    public function updatedMessageText($value)
    {
        \Illuminate\Support\Facades\Log::info('Property Updated: messageText', ['value' => $value]);
    }

    public function mount(WahaService $wahaService)
    {
        // Block access for employers - WhatsApp is only for employees
        $user = Auth::user();
        if ($user->tipo_usuario === 'empleador' || $user->is_superadmin) {
            abort(403, 'WhatsApp solo está disponible para profesionales.');
        }
        
        $this->checkSessionStatus();
    }

    public function getSessionName()
    {
        // Use WAHA's default session instead of creating per-user sessions
        // This works for single-user WhatsApp connections
        return 'default';
    }

    public function checkSessionStatus()
    {
        $statusData = $this->wahaService->getSessionStatus($this->getSessionName());
        $newStatus = $statusData['status'] ?? null;

        // If WAHA returns an error code (e.g. 500), don't immediately drop to STOPPED
        // unless it's a 404 (meaning session definitely doesn't exist).
        if (isset($statusData['errorCode']) && $statusData['errorCode'] !== 404) {
            \Log::warning("WAHA status check failed with {$statusData['errorCode']}: " . json_encode($statusData));
            return;
        }

        if (!$newStatus) {
            $newStatus = 'STOPPED';
        }

        // Anti-flicker: If we are STARTING, ignore STOPPED for a few seconds
        // to allow WAHA specific time to register the session.
        if ($this->sessionStatus === 'STARTING' && $newStatus === 'STOPPED') {
            if (time() - $this->startTime < 15) {
                return;
            }
        }

        $this->sessionStatus = $newStatus;

        if ($this->sessionStatus === 'SCAN_QR_CODE') {
            $newQr = $this->wahaService->getQrCode($this->getSessionName());
            if ($newQr) {
                $this->qrCodeBase64 = $newQr;
                $this->qrScanned = false;
            } else {
                // If status is SCAN_QR_CODE but we have no QR, it might be scanned
                // or just loading. If we had a QR before, then it's scanned.
                if ($this->qrCodeBase64) {
                    $this->qrCodeBase64 = null;
                    $this->qrScanned = true;
                    \Log::info("WAHA: QR Code disappeared while in SCAN_QR_CODE status. Marking as scanned.");
                }
            }
        } elseif ($this->sessionStatus === 'WORKING') {
            // Clear QR code to trigger UI update
            $this->qrCodeBase64 = null;
            $this->qrScanned = false;
            $this->loadContacts();
        } elseif ($this->sessionStatus === 'AUTHENTICATING') {
            // QR was scanned, now authenticating
            $this->qrCodeBase64 = null;
            $this->qrScanned = true;
            \Log::info("WAHA: Status is AUTHENTICATING. Marking as scanned.");
        } else {
            // Clear QR for any other status (STOPPED, STARTING, FAILED)
            $this->qrCodeBase64 = null;
            $this->qrScanned = false;
        }
    }

    public function startSession()
    {
        $this->startTime = time();
        $this->sessionStatus = 'STARTING';
        
        // Service now handles deleting old session first
        $response = $this->wahaService->startSession($this->getSessionName());
        
        if (isset($response['error'])) {
            // Even with cleanup, if it fails, show error.
            $this->addError('session', 'Error al iniciar sesión: ' . $response['error']);
             // Reset status on error so user can try again
            $this->sessionStatus = 'STOPPED';
            return;
        }

        // If response has status, utilize it (e.g. reused session)
        if (isset($response['status'])) {
            $this->sessionStatus = $response['status'];
        }
        
        // Poll will pick up the next state
    }

    public function logout()
    {
        \Log::info("WAHA: Performing aggressive logout (Delete Session) for " . $this->getSessionName());
        $this->wahaService->deleteSession($this->getSessionName());
        $this->sessionStatus = 'STOPPED';
        $this->qrCodeBase64 = null;
        $this->qrScanned = false;
        $this->contacts = [];
        $this->selectedContactId = null;
        $this->messages = [];
    }

    public function loadContacts()
    {
        $user = Auth::user();
        $this->contacts = collect();

        // 1. Add Customer Success (Virtual User object-like structure)
        $csContact = (object)[
            'id' => 'customer_success',
            'name' => 'Customer Success / OberTrack',
            'phone_number' => '+34930289966',
            'job_title' => 'Soporte Técnico',
            'avatar' => null
        ];
        $this->contacts->push($csContact);


        if ($user->tipo_usuario === 'empleado') {
            // 2. Add Company
            if ($user->empleador_id) {
                $employer = User::find($user->empleador_id);
                if ($employer && $employer->phone_number) {
                    $this->contacts->push($employer);
                }
            }

            // 3. Add other employees based on role
            if ($user->is_manager) {
                // Managers see all Professionals (non-managers) of the same company
                $professionals = User::where('empleador_id', $user->empleador_id)
                    ->where('tipo_usuario', 'empleado')
                    ->whereRaw('is_manager IS NOT TRUE') // Handles false and null safely, or IS FALSE. using IS NOT TRUE covers false/null defaults.
                    ->where('id', '!=', $user->id)
                    ->whereNotNull('phone_number')
                    ->get();
                foreach($professionals as $p) $this->contacts->push($p);
            } else {
                // Professionals see all Managers of the same company
                $managers = User::where('empleador_id', $user->empleador_id)
                    ->where('tipo_usuario', 'empleado')
                    ->whereRaw('is_manager IS TRUE')
                    ->where('id', '!=', $user->id)
                    ->whereNotNull('phone_number')
                    ->get();
                foreach($managers as $m) $this->contacts->push($m);
            }
        }
    }

    public function selectContact($contactId)
    {
        // Refresh session status before doing anything
        $this->checkSessionStatus();
        
        if ($this->sessionStatus !== 'WORKING') {
            $this->addError('session', 'La sesión de WhatsApp no está activa. Por favor, escanea el código QR de nuevo.');
            return;
        }

        $this->isLoadingContact = true;
        
        $contact = $this->contacts->firstWhere('id', $contactId);
        if (!$contact) {
            $this->isLoadingContact = false;
            return;
        }

        $this->selectedContactId = $contactId;
        
        // Clean number
        $phone = preg_replace('/[^0-9]/', '', $contact->phone_number);

        // Resolve correct WhatsApp ID using WAHA check-exists
        // Preliminary check
        $phone = $this->wahaService->formatArgentinaNumber($phone);
        $checkData = $this->wahaService->checkNumberStatus($this->getSessionName(), $phone);
        $isValid = $checkData && (isset($checkData['id']['_serialized']) || isset($checkData['id']));
        
        // --- Argentina (54) Special Logic: Double Check ---
        if (!$isValid && str_starts_with($phone, '54')) {
            \Log::info("WAHA: Attempting Argentina-specific variant for {$phone}");
            
            // Try the opposite variant (with or without 9) if the helper's first guess failed
            if (str_starts_with($phone, '549')) {
                $variant = '54' . substr($phone, 3);
            } else {
                $variant = '549' . substr($phone, 2);
            }
            
            \Log::info("WAHA: Trying variant -> {$variant}");
            $variantData = $this->wahaService->checkNumberStatus($this->getSessionName(), $variant);
            $isVariantValid = $variantData && (isset($variantData['id']['_serialized']) || isset($variantData['id']));
            
            if ($isVariantValid) {
                \Log::info("WAHA: Argentina variant SUCCEEDED -> {$variant}");
                $phone = $variant;
                $checkData = $variantData;
                $isValid = true;
            }
        }

        if ($isValid) {
            // Use the ID returned by WhatsApp
            $waId = $checkData['id']['_serialized'] ?? $checkData['id'];
            // Remove @c.us for internal consistency
            $phone = str_replace('@c.us', '', $waId);
            \Log::info("Contact Resolved via WAHA: {$contact->phone_number} -> {$phone}");
        } else {
            \Log::warning("Contact Check Failed for {$phone}. Applying international heuristics.");
            
            // International Heuristics (if WAHA verification fails)
            
            // Special handling for USA/PR/Canada if user entered 787... without '1'
            if ((str_starts_with($phone, '787') || str_starts_with($phone, '939')) && strlen($phone) === 10) {
                $phone = '1' . $phone;
                \Log::info("International heuristic: Added '1' for Puerto Rico -> {$phone}");
            }
            
            // Special handling for Spain if user entered a local number without 34
            if (str_starts_with($phone, '6') && strlen($phone) === 9) {
                $phone = '34' . $phone;
                \Log::info("International heuristic: Added '34' for Spain -> {$phone}");
            }
        }

        $this->selectedPhone = $phone;
        
        $this->loadMessages();
        $this->isLoadingContact = false;
    }

    public function loadMessages()
    {
        if (!$this->selectedPhone) return;

        $history = $this->wahaService->getChatHistory($this->getSessionName(), $this->selectedPhone);
        // Reverse because usually API returns newest first, we want oldest at top for chat view
        $this->messages = array_reverse($history);
    }

    public function sendMessage($text = null)
    {
        \Illuminate\Support\Facades\Log::error('DEBUG: sendMessage called', [
            'phone' => $this->selectedPhone,
            'text' => $text,
            'propertyText' => $this->messageText
        ]);

        // If text is passed as argument (e.g. from Alpine), use it. 
        // Otherwise use the property (fallback).
        if ($text !== null) {
            $this->messageText = $text;
        }

        \Illuminate\Support\Facades\Log::info('Attempting to send message', [
            'phone' => $this->selectedPhone,
            'text' => $this->messageText,
            'len' => strlen($this->messageText)
        ]);

        if (!$this->selectedPhone) {
            $this->addError('messageText', 'Error interno: No hay teléfono seleccionado.');
            return;
        }

        if (empty($this->messageText) && !$this->attachment) {
            $this->addError('messageText', 'El mensaje no puede estar vacío.');
            return; 
        }

        if ($this->attachment) {
            // ... attachment logic ...
        }

        if (!empty($this->messageText)) {
            $response = $this->wahaService->sendMessage($this->getSessionName(), $this->selectedPhone, $this->messageText);
            
            \Illuminate\Support\Facades\Log::error('DEBUG: WAHA Send Response', ['response' => $response]);

            if (!$response) {
                $this->addError('messageText', 'Error crítico: Sin respuesta del servicio de WhatsApp.');
                return false;
            }

            if (isset($response['error']) || (isset($response['status']) && $response['status'] !== 200 && $response['status'] !== 201)) {
                // If it's our new structured error or a WAHA error
                $errorMsg = $response['message'] ?? ($response['error'] ?? 'Error desconocido al enviar mensaje.');
                
                // If message is JSON string, try to decode specific field
                if (is_string($errorMsg) && str_starts_with($errorMsg, '{')) {
                     $decoded = json_decode($errorMsg, true);
                     $errorMsg = $decoded['message'] ?? $errorMsg; // Try to extract 'message' property from WAHA error JSON
                }

                if (is_string($errorMsg) && str_contains($errorMsg, 'No LID for user')) {
                    $errorMsg = 'El contacto no está sincronizado o el número es incorrecto. Espera unos segundos e intenta nuevamente.';
                }

                $this->addError('messageText', "Error: $errorMsg");
                return false;
            }
        }

        $this->messageText = '';
        $this->loadMessages(); 
        return true;
    }

    public function render()
    {
        // Periodic check for status / messages
        if ($this->sessionStatus !== 'WORKING') {
            // Check status if not working (e.g. waiting for QR scan)
             $this->checkSessionStatus();
        } else if ($this->selectedContactId) {
            // If working and chat open, refresh messages (polling)
            // Just simple polling for now
            // $this->loadMessages(); // Too heavy? Maybe only check every X seconds
        }

        return view('livewire.whatsapp-chat');
    }
}
