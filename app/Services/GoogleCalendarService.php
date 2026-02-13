<?php

namespace App\Services;

use App\Models\User;
use Google\Client;
use Google\Service\Calendar;
use Illuminate\Support\Facades\Log;

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
            return [];
        }

        try {
            $this->setAccessToken($user);

            $calendarService = new Calendar($this->client);
            $optParams = [
                'maxResults' => 10,
                'orderBy' => 'startTime',
                'singleEvents' => true,
                'timeMin' => now()->startOfDay()->toRfc3339String(),
                'timeMax' => now()->endOfDay()->toRfc3339String(),
            ];

            $results = $calendarService->events->listEvents('primary', $optParams);
            $events = $results->getItems();

            return collect($events)->map(function ($event) {
                return [
                    'id' => $event->id,
                    'summary' => $event->getSummary(),
                    'start' => $event->getStart()->getDateTime() ?: $event->getStart()->getDate(),
                    'end' => $event->getEnd()->getDateTime() ?: $event->getEnd()->getDate(),
                    'link' => $event->getHangoutLink() ?: $event->getHtmlLink(),
                    'status' => $event->status,
                ];
            });
        } catch (\Exception $e) {
            Log::error('Google Calendar Error: ' . $e->getMessage());
            return [];
        }
    }

    protected function setAccessToken(User $user)
    {
        $token = json_decode($user->google_calendar_token, true);
        $this->client->setAccessToken($token);

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                $user->update(['google_calendar_token' => json_encode($newToken)]);
            } else {
                throw new \Exception('Google Calendar token expired and no refresh token available.');
            }
        }
    }

    protected function getCalendarEmail()
    {
        $oauth2 = new \Google\Service\Oauth2($this->client);
        return $oauth2->userinfo->get()->email;
    }
}
