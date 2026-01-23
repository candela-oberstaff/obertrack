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

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $message, $companyName, $sessionName = 'default')
    {
        $this->userId = $userId;
        $this->message = $message;
        $this->companyName = $companyName;
        $this->sessionName = $sessionName;
    }

    /**
     * Execute the job.
     */
    public function handle(WahaService $waha)
    {
        $user = User::find($this->userId);

        if (!$user || !$user->phone_number) {
            Log::warning("SendMassWhatsappJob skipped: User {$this->userId} not found or has no phone number.");
            return;
        }

        // WhatsApp formatting (Bold company name)
        $formattedMessage = "*{$this->companyName}*\n\n" . $this->message;

        $chatId = $user->phone_number;
        // Basic cleaning (remove + and ensure it doesn't have @c.us for service call)
        $chatId = str_replace(['+', ' '], '', $chatId);

        Log::info("Executing SendMassWhatsappJob for user {$user->name} ({$chatId}) using session {$this->sessionName}");

        $result = $waha->sendMessage($this->sessionName, $chatId, $formattedMessage);

        if (isset($result['error']) && $result['error']) {
            Log::error("SendMassWhatsappJob failed for user {$user->name}: " . json_encode($result));
            
            // Optionally: throw exception to trigger job retry
            // throw new \Exception("WhatsApp delivery failed: " . json_encode($result));
        } else {
            Log::info("SendMassWhatsappJob success for user {$user->name}");
        }
    }
}
