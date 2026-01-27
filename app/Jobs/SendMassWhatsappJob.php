<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\WahaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMassWhatsappJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $message;
    protected $companyName;
    protected $sessionName;

    public function __construct($userId, $message, $companyName, $sessionName = 'default')
    {
        $this->userId = $userId;
        $this->message = $message;
        $this->companyName = $companyName;
        $this->sessionName = $sessionName;
    }

    public function handle(WahaService $waha)
    {
        $user = User::find($this->userId);

        if (!$user || !$user->phone_number) {
            Log::warning("SendMassWhatsappJob skipped: User {$this->userId} not found or has no phone number.");
            return;
        }

        $formattedMessage = "*{$this->companyName}*\n\n" . $this->message;
        $chatId = $waha->formatArgentinaNumber($user->phone_number);

        Log::info("Executing SendMassWhatsappJob for user {$user->name} ({$chatId}) using session {$this->sessionName}");

        $statusData = $waha->getSessionStatus($this->sessionName);
        Log::info("SendMassWhatsappJob: Current session status for [{$this->sessionName}] is [{$statusData['status']}]");

        if (($statusData['status'] ?? 'STOPPED') !== 'WORKING') {
            Log::error("SendMassWhatsappJob FAILED: Session [{$this->sessionName}] is not WORKING (Status: " . ($statusData['status'] ?? 'N/A') . "). Message to {$user->name} aborted.");
            return;
        }

        $result = $waha->sendMessage($this->sessionName, $chatId, $formattedMessage);

        if (isset($result['error']) && $result['error']) {
            Log::error("SendMassWhatsappJob failed for user {$user->name}: " . json_encode($result));
        } else {
            Log::info("SendMassWhatsappJob success for user {$user->name}");
        }
    }
}
