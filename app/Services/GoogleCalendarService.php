<?php

namespace App\Services;

use App\Models\User;
use Google\Client;
use Google\Service\Calendar;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use App\Jobs\RefreshGoogleCalendarToken;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(route('google-calendar.callback'));
        $this->client->addScope(Calendar::CALENDAR_EVENTS_READONLY);
        $this->client->addScope('email');
        $this->client->addScope('profile');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    public function getAuthUrl()
    {
        if (!$this->client) {
            throw new \Exception('La integración con Google Calendar se está inicializando. Por favor, intenta de nuevo en unos minutos.');
        }
        return $this->client->createAuthUrl();
    }

    public function authenticate($code, User $user)
    {
        if (!$this->client) {
            \Illuminate\Support\Facades\Log::error('Google Calendar Service client is null during authentication');
            throw new \Exception('La integración con Google Calendar no está disponible todavía.');
        }

        \Illuminate\Support\Facades\Log::info('Fetching Google Access Token with code for user ' . $user->id);
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
        
        if (isset($accessToken['error'])) {
            \Illuminate\Support\Facades\Log::error('Google Access Token Fetch Error: ' . json_encode($accessToken));
            throw new \Exception('Google Auth Error: ' . ($accessToken['error_description'] ?? $accessToken['error']));
        }

        \Illuminate\Support\Facades\Log::info('Token obtained. Updating user fields for user ' . $user->id);
        $email = $this->getCalendarEmail();
        
        $updated = $user->update([
            'google_calendar_token' => json_encode($accessToken),
            'google_calendar_email' => $email,
        ]);

        \Illuminate\Support\Facades\Log::info('User update result: ' . ($updated ? 'SUCCESS' : 'FAILURE'));
        if (!$updated) {
            \Illuminate\Support\Facades\Log::error('Failed to update user model with Google Calendar tokens.');
        }

        return $accessToken;
    }

    public function getTodayMeetings(User $user)
    {
        if (!$user->google_calendar_token) {
            return ['error' => 'not_connected'];
        }

        // Rate limiting: max 10 requests per minute per user
        $rateLimitKey = 'calendar_api_' . $user->id;
        
        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            Log::warning('Rate limit exceeded for user ' . $user->id . ', retry in ' . $seconds . 's');
            return ['error' => 'rate_limit', 'retry_after' => $seconds];
        }

        // Cache key unique per user and day
        $cacheKey = 'calendar_meetings_' . $user->id . '_' . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($user, $rateLimitKey) {
            // Increment rate limit counter
            RateLimiter::hit($rateLimitKey, 60);
            
            try {
                $this->setAccessToken($user);
                
                // Explicitly specify timezone
                $timezone = config('app.timezone', 'America/Argentina/Buenos_Aires');
                $timeMin = now()->timezone($timezone)->startOfDay()->toRfc3339String();
                $timeMax = now()->timezone($timezone)->endOfDay()->toRfc3339String();
                
                if (config('app.debug')) {
                    Log::info('Fetching Google Calendar events for user ' . $user->id, [
                        'timeMin' => $timeMin,
                        'timeMax' => $timeMax,
                        'timezone' => $timezone
                    ]);
                }

                $calendarService = new Calendar($this->client);
                $optParams = [
                    'maxResults' => 10,
                    'orderBy' => 'startTime',
                    'singleEvents' => true,
                    'timeMin' => $timeMin,
                    'timeMax' => $timeMax,
                    'timeZone' => $timezone, // Explicitly set timezone
                ];

                $results = $calendarService->events->listEvents('primary', $optParams);
                $events = $results->getItems();
                
                if (config('app.debug')) {
                    Log::info('Google API response for user ' . $user->id, [
                        'count' => count($events)
                    ]);
                }

                return collect($events)->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'summary' => $event->getSummary(),
                        'start' => $event->getStart()->getDateTime() ?: $event->getStart()->getDate(),
                        'end' => $event->getEnd()->getDateTime() ?: $event->getEnd()->getDate(),
                        'link' => $event->getHangoutLink() ?: $event->getHtmlLink(),
                        'status' => $event->status,
                    ];
                })->toArray();
            } catch (\Google\Service\Exception $e) {
                // Google API specific errors
                $errorData = json_decode($e->getMessage(), true);
                $errorCode = $errorData['error']['code'] ?? 0;
                
                if ($errorCode === 401) {
                    Log::warning('Google Calendar token expired for user ' . $user->id);
                    // Clear the invalid token
                    $user->update([
                        'google_calendar_token' => null,
                        'google_calendar_email' => null,
                    ]);
                    return ['error' => 'token_expired'];
                } elseif ($errorCode === 403) {
                    Log::error('Google Calendar API access denied for user ' . $user->id);
                    return ['error' => 'access_denied'];
                } elseif ($errorCode === 429) {
                    // Google API quota exceeded
                    Log::error('Google Calendar API quota exceeded for user ' . $user->id);
                    return ['error' => 'quota_exceeded'];
                } else {
                    Log::error('Google Calendar API error for user ' . $user->id . ': ' . $e->getMessage());
                    return ['error' => 'api_error'];
                }
            } catch (\Exception $e) {
                Log::error('Google Calendar Error for user ' . $user->id . ': ' . $e->getMessage());
                return ['error' => 'unknown'];
            }
        });
    }

    protected function setAccessToken(User $user)
    {
        $token = json_decode($user->google_calendar_token, true);
        $this->client->setAccessToken($token);

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                try {
                    // Try synchronous refresh first for immediate requests
                    $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                    
                    if (isset($newToken['error'])) {
                        throw new \Exception('Failed to refresh token: ' . $newToken['error']);
                    }
                    
                    $user->update(['google_calendar_token' => json_encode($newToken)]);
                    
                    // Dispatch background job to proactively refresh in 45 minutes
                    // (Google tokens typically expire in 1 hour)
                    RefreshGoogleCalendarToken::dispatch($user)->delay(now()->addMinutes(45));
                } catch (\Exception $e) {
                    Log::error('Failed to refresh Google Calendar token for user ' . $user->id . ': ' . $e->getMessage());
                    throw new \Exception('Google Calendar token refresh failed. Please reconnect your calendar.');
                }
            } else {
                throw new \Exception('Google Calendar token expired and no refresh token available.');
            }
        }
    }
    
    /**
     * Clear cached meetings for a user
     */
    public function clearCache(User $user)
    {
        $cacheKey = 'calendar_meetings_' . $user->id . '_' . now()->format('Y-m-d');
        Cache::forget($cacheKey);
    }

    protected function getCalendarEmail()
    {
        $oauth2 = new \Google\Service\Oauth2($this->client);
        return $oauth2->userinfo->get()->email;
    }
}
