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
            
            Log::info('Listing Google Forms for user ' . $user->id);
            $results = $driveService->files->listFiles($optParams);
            $files = $results->getFiles();
            Log::info('Google Forms found: ' . count($files));
            
            return array_map(function($file) {
                return [
                    'id' => $file->id,
                    'name' => $file->name,
                    'webViewLink' => $file->webViewLink,
                ];
            }, $files);
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

    public function getFormContent(User $user, string $formId)
    {
        try {
            $this->setAccessToken($user);
            $formsService = new \Google\Service\Forms($this->client);
            $form = $formsService->forms->get($formId);
            
            // Map the form object to an array for Livewire serialization
            $mappedItems = [];
            if ($form->items) {
                foreach ($form->items as $item) {
                    $mappedItem = [
                        'title' => $item->title,
                        'itemId' => $item->itemId,
                        'description' => $item->description,
                    ];
                    
                    if ($item->questionItem) {
                        $mappedItem['questionItem'] = [
                            'question' => [
                                'textQuestion' => $item->questionItem->question->textQuestion ? [
                                    'paragraph' => $item->questionItem->question->textQuestion->paragraph,
                                ] : null,
                                'choiceQuestion' => $item->questionItem->question->choiceQuestion ? [
                                    'type' => $item->questionItem->question->choiceQuestion->type,
                                    'options' => array_map(function($opt) {
                                        return ['value' => $opt->value];
                                    }, $item->questionItem->question->choiceQuestion->options ?? []),
                                ] : null,
                            ],
                        ];
                    }
                    
                    $mappedItems[] = $mappedItem;
                }
            }

            return [
                'formId' => $form->formId,
                'info' => [
                    'title' => $form->info->title,
                    'description' => $form->info->description,
                ],
                'items' => $mappedItems,
            ];
        } catch (\Exception $e) {
            Log::error('Google Forms Get Content Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function addQuestion(User $user, string $formId, array $questionData)
    {
        try {
            $this->setAccessToken($user);
            $formsService = new \Google\Service\Forms($this->client);

            $requests = [];
            $item = new \Google\Service\Forms\Item();
            $item->setTitle($questionData['title']);

            $question = new \Google\Service\Forms\Question();
            $question->setRequired($questionData['required'] ?? false);

            if ($questionData['type'] === 'TEXT') {
                $textQuestion = new \Google\Service\Forms\TextQuestion();
                $textQuestion->setParagraph(true); // True for Paragraph, False for Short Answer
                $question->setTextQuestion($textQuestion);
            } elseif ($questionData['type'] === 'RADIO') {
                $choiceQuestion = new \Google\Service\Forms\ChoiceQuestion();
                $choiceQuestion->setType('RADIO');
                
                $options = [];
                if (isset($questionData['options']) && is_array($questionData['options'])) {
                    foreach ($questionData['options'] as $optText) {
                        $option = new \Google\Service\Forms\Option();
                        $option->setValue($optText);
                        $options[] = $option;
                    }
                } else {
                    // Default option if none provided
                    $option = new \Google\Service\Forms\Option();
                    $option->setValue('Opción 1');
                    $options[] = $option;
                }
                
                $choiceQuestion->setOptions($options);
                $question->setChoiceQuestion($choiceQuestion);
            }

            $questionItem = new \Google\Service\Forms\QuestionItem();
            $questionItem->setQuestion($question);
            
            $item->setQuestionItem($questionItem);
            
            $createItemRequest = new \Google\Service\Forms\CreateItemRequest();
            $createItemRequest->setItem($item);
            $createItemRequest->setLocation(['index' => 0]); // Add to top, or handle index logic

            $request = new \Google\Service\Forms\Request();
            $request->setCreateItem($createItemRequest);
            $requests[] = $request;

            $batchRequest = new \Google\Service\Forms\BatchUpdateFormRequest();
            $batchRequest->setRequests($requests);

            return $formsService->forms->batchUpdate($formId, $batchRequest);
        } catch (\Exception $e) {
            Log::error('Google Forms Add Question Error: ' . $e->getMessage());
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
