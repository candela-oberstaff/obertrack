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
        $this->client->addScope(Forms::FORMS_BODY); // Changed from READONLY to FULL ACCESS
        $this->client->addScope(Forms::DRIVE_FILE); // Access to files created by the app (for delete)
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
            $driveService = new \Google\Service\Drive($this->client);
            $optParams = [
                'pageSize' => 20,
                'q' => "mimeType='application/vnd.google-apps.form' and trashed=false",
                'fields' => 'nextPageToken, files(id, name, webViewLink, webContentLink)'
            ];
            $results = $driveService->files->listFiles($optParams);
            return $results->getFiles();
        } catch (\Exception $e) {
            Log::error('Google Forms List Error: ' . $e->getMessage());
            return [];
        }
    }

    public function createForm(User $user, string $title)
    {
        try {
            $this->setAccessToken($user);
            $formsService = new \Google\Service\Forms($this->client);
            
            $form = new \Google\Service\Forms\Form();
            $info = new \Google\Service\Forms\Info();
            $info->setTitle($title);
            $form->setInfo($info);
            
            $createdForm = $formsService->forms->create($form);
            return $createdForm;
        } catch (\Exception $e) {
            Log::error('Google Forms Create Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteForm(User $user, string $formId)
    {
        try {
            $this->setAccessToken($user);
            $driveService = new \Google\Service\Drive($this->client);
            // We use Drive API to delete the file
            $driveService->files->delete($formId);
            return true;
        } catch (\Exception $e) {
            Log::error('Google Forms Delete Error: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function setAccessToken(User $user)
    {
        $token = json_decode($user->google_forms_token, true);
        $this->client->setAccessToken($token);

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                // Merge with old token to keep refresh token if not returned
                $mergedToken = array_merge($token, $newToken);
                $user->update(['google_forms_token' => json_encode($mergedToken)]);
            } else {
                throw new \Exception('Token expired and no refresh token available.');
            }
        }
    }
}
