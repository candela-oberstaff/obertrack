<?php

namespace App\Services;

use App\Models\User;
use Google\Client;
use Google\Service\Forms;
use Illuminate\Support\Facades\Log;

class GoogleFormsService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(route('google-forms.callback'));
        $this->client->addScope(Forms::FORMS_BODY_READONLY);
        $this->client->addScope('email');
        $this->client->addScope('profile');
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function authenticate($code, User $user)
    {
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
        
        if (isset($accessToken['error'])) {
            throw new \Exception('Google Auth Error: ' . ($accessToken['error_description'] ?? $accessToken['error']));
        }

        $email = $this->getFormsEmail();
        
        $user->update([
            'google_forms_token' => json_encode($accessToken),
            'google_forms_email' => $email,
        ]);

        return $accessToken;
    }

    protected function getFormsEmail()
    {
        $oauth2 = new \Google\Service\Oauth2($this->client);
        return $oauth2->userinfo->get()->email;
    }

    public function listForms(User $user)
    {
        if (!$user->google_forms_token) {
            return [];
        }

        try {
            $this->setAccessToken($user);
            // Note: Google Forms API list forms is not directly available via Google\Service\Forms
            // usually you'd search in Drive for files with mimeType 'application/vnd.google-apps.form'
            $driveService = new \Google\Service\Drive($this->client);
            $optParams = [
                'pageSize' => 20,
                'q' => "mimeType='application/vnd.google-apps.form'",
                'fields' => 'nextPageToken, files(id, name, webViewLink)'
            ];
            $results = $driveService->files->listFiles($optParams);
            return $results->getFiles();
        } catch (\Exception $e) {
            Log::error('Google Forms Error: ' . $e->getMessage());
            return [];
        }
    }

    protected function setAccessToken(User $user)
    {
        $token = json_decode($user->google_forms_token, true);
        $this->client->setAccessToken($token);

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                $user->update(['google_forms_token' => json_encode($newToken)]);
            } else {
                throw new \Exception('Token expired and no refresh token available.');
            }
        }
    }
}
