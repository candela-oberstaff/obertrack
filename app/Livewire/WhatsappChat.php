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
    
    // Polling control
    public $pollInterval = 3; 

    public function boot(WahaService $wahaService)
    {
        $this->wahaService = $wahaService;
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
        $newStatus = $statusData['status'] ?? 'STOPPED';

        // Anti-flicker: If we are STARTING, ignore STOPPED for a few seconds
        // to allow WAHA specific time to register the session.
        if ($this->sessionStatus === 'STARTING' && $newStatus === 'STOPPED') {
            if (time() - $this->startTime < 15) {
                return;
            }
        }

        $this->sessionStatus = $newStatus;

        if ($this->sessionStatus === 'SCAN_QR_CODE') {
            $this->qrCodeBase64 = $this->wahaService->getQrCode($this->getSessionName());
        } elseif ($this->sessionStatus === 'WORKING') {
            // Clear QR code to trigger UI update
            $this->qrCodeBase64 = null;
            $this->loadContacts();
        } else {
            // Clear QR for any other status
            $this->qrCodeBase64 = null;
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
        $this->wahaService->logout($this->getSessionName());
        $this->sessionStatus = 'STOPPED';
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
                    ->where('is_manager', false)
                    ->where('id', '!=', $user->id)
                    ->whereNotNull('phone_number')
                    ->get();
                foreach($professionals as $p) $this->contacts->push($p);
            } else {
                // Professionals see all Managers of the same company
                $managers = User::where('empleador_id', $user->empleador_id)
                    ->where('tipo_usuario', 'empleado')
                    ->where('is_manager', true)
                    ->where('id', '!=', $user->id)
                    ->whereNotNull('phone_number')
                    ->get();
                foreach($managers as $m) $this->contacts->push($m);
            }
        }
    }

    public function selectContact($contactId)
    {
        $contact = $this->contacts->firstWhere('id', $contactId);
        if (!$contact) return;

        $this->selectedContactId = $contactId;
        // Format phone number for WAHA (Assuming stored with country code or needing prefix)
        // Ideally, phone_number should be E.164. If not, we might need a helper.
        // For Argentina: 549 + area + number.
        // Let's assume the user stored it correctly or we try to clean it.
        $this->selectedPhone = preg_replace('/[^0-9]/', '', $contact->phone_number);
        
        $this->loadMessages();
    }

    public function loadMessages()
    {
        if (!$this->selectedPhone) return;

        $history = $this->wahaService->getChatHistory($this->getSessionName(), $this->selectedPhone);
        // Reverse because usually API returns newest first, we want oldest at top for chat view
        $this->messages = array_reverse($history);
    }

    public function sendMessage()
    {
        if (!$this->selectedPhone) return;
        if (empty($this->messageText) && !$this->attachment) return;

        if ($this->attachment) {
            // TODO: Upload temp file and send URL or Base64
            // For now, text only implementation for speed, attachment later or simple URL
            $url = $this->attachment->temporaryUrl(); 
            // note: temporaryUrl only works if file is accessible by WAHA (public driver). 
            // If local, WAHA in docker might not see 'localhost'.
            // For production with S3/Cloudinary it's fine.
            // For local, creating a base64 might be safer if WAHA supports it.
            
            // Revisit: sending attachment needs careful handling.
        }

        if (!empty($this->messageText)) {
            $this->wahaService->sendMessage($this->getSessionName(), $this->selectedPhone, $this->messageText);
        }

        $this->messageText = '';
        $this->loadMessages(); // Refresh
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
