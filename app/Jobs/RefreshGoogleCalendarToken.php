<?php

namespace App\Jobs;

use App\Models\User;
use Google\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RefreshGoogleCalendarToken implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->user->google_calendar_token) {
            if (config('app.debug')) {
                Log::info('RefreshGoogleCalendarToken: User has no token, skipping.');
            }
            return;
        }

        $token = json_decode($this->user->google_calendar_token, true);
        
        $client = new Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessToken($token);

        if ($client->isAccessTokenExpired()) {
            if ($client->getRefreshToken()) {
                try {
                    $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    
                    if (isset($newToken['error'])) {
                        Log::error('RefreshGoogleCalendarToken: Failed to refresh token for user ' . $this->user->id . ': ' . $newToken['error']);
                        
                        // If refresh token is invalid, clear the connection
                        $this->user->update([
                            'google_calendar_token' => null,
                            'google_calendar_email' => null,
                        ]);
                        
                        return;
                    }
                    
                    $this->user->update(['google_calendar_token' => json_encode($newToken)]);
                    
                    if (config('app.debug')) {
                        Log::info('RefreshGoogleCalendarToken: Successfully refreshed token for user ' . $this->user->id);
                    }
                } catch (\Exception $e) {
                    Log::error('RefreshGoogleCalendarToken: Exception for user ' . $this->user->id . ': ' . $e->getMessage());
                    throw $e; // Re-throw to trigger retry
                }
            } else {
                if (config('app.debug')) {
                    Log::warning('RefreshGoogleCalendarToken: No refresh token available for user ' . $this->user->id);
                }
                
                // Clear the expired token
                $this->user->update([
                    'google_calendar_token' => null,
                    'google_calendar_email' => null,
                ]);
            }
        } else {
            if (config('app.debug')) {
                Log::info('RefreshGoogleCalendarToken: Token not expired for user ' . $this->user->id);
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('RefreshGoogleCalendarToken: Job failed after all retries for user ' . $this->user->id . ': ' . $exception->getMessage());
        
        // Optionally notify user or admin
        // You could dispatch a notification here
    }
}
