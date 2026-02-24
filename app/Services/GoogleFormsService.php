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

        // Fix for cURL error 77 in local development (Laragon/Windows SSL issues)
        if (app()->environment('local')) {
            $this->client->setHttpClient(new \GuzzleHttp\Client([
                'verify' => false,
            ]));
        }
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
            
            $settings = new \Google\Service\Forms\FormSettings();
            $settings->setEmailCollectionType('DO_NOT_COLLECT');
            $form->setSettings($settings);
            
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
                    
                    if ($item->getQuestionItem()) {
                        $mappedItem['type'] = 'QUESTION';
                        $question = $item->getQuestionItem()->getQuestion();
                        $mappedItem['questionItem'] = [
                            'question' => [
                                'textQuestion' => $question->getTextQuestion() ? [
                                    'paragraph' => $question->getTextQuestion()->getParagraph(),
                                ] : null,
                                'choiceQuestion' => $question->getChoiceQuestion() ? [
                                    'type' => $question->getChoiceQuestion()->getType(),
                                    'options' => array_map(function($opt) {
                                        return ['value' => $opt->getValue()];
                                    }, $question->getChoiceQuestion()->getOptions() ?? []),
                                ] : null,
                            ],
                        ];
                    } elseif ($item->getImageItem()) {
                        $mappedItem['type'] = 'IMAGE';
                        $image = $item->getImageItem()->getImage();
                        $mappedItem['image'] = [
                            'contentUri' => $image->getContentUri(),
                            'altText' => $image->getAltText(),
                        ];
                    } elseif ($item->getVideoItem()) {
                        $mappedItem['type'] = 'VIDEO';
                        $video = $item->getVideoItem()->getVideo();
                        $mappedItem['video'] = [
                            'youtubeUri' => $video->getYoutubeUri(),
                        ];
                        $mappedItem['caption'] = $item->getVideoItem()->getCaption();
                    } elseif ($item->getTextItem()) {
                        $mappedItem['type'] = 'TEXT';
                    } elseif ($item->getPageBreakItem()) {
                        $mappedItem['type'] = 'PAGE_BREAK';
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
                'responderUri' => $form->responderUri,
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
            
            // Get current count to append at the end
            $currentForm = $formsService->forms->get($formId);
            $nextIndex = count($currentForm->getItems() ?? []);

            $requests = [];
            $item = new \Google\Service\Forms\Item();
            $item->setTitle($questionData['title']);

            $question = new \Google\Service\Forms\Question();
            $question->setRequired($questionData['required'] ?? false);

            if ($questionData['type'] === 'TEXT') {
                $textQuestion = new \Google\Service\Forms\TextQuestion();
                $textQuestion->setParagraph(true);
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
            $location = new \Google\Service\Forms\Location();
            $location->setIndex($nextIndex);
            $createItemRequest->setLocation($location);

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

    public function addVideo(User $user, string $formId, string $title, string $youtubeUri)
    {
        try {
            $this->setAccessToken($user);
            $formsService = new \Google\Service\Forms($this->client);

            // Get current count to append at the end
            $currentForm = $formsService->forms->get($formId);
            $nextIndex = count($currentForm->getItems() ?? []);

            $video = new \Google\Service\Forms\Video();
            $video->setYoutubeUri($this->normalizeYouTubeUrl($youtubeUri));

            $videoItem = new \Google\Service\Forms\VideoItem();
            $videoItem->setVideo($video);

            $item = new \Google\Service\Forms\Item();
            $item->setTitle($title);
            $item->setVideoItem($videoItem);

            $createItemRequest = new \Google\Service\Forms\CreateItemRequest();
            $createItemRequest->setItem($item);
            $location = new \Google\Service\Forms\Location();
            $location->setIndex($nextIndex);
            $createItemRequest->setLocation($location);

            $request = new \Google\Service\Forms\Request();
            $request->setCreateItem($createItemRequest);

            $batchRequest = new \Google\Service\Forms\BatchUpdateFormRequest();
            $batchRequest->setRequests([$request]);

            return $formsService->forms->batchUpdate($formId, $batchRequest);
        } catch (\Exception $e) {
            Log::error('Google Forms Add Video Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function addImage(User $user, string $formId, string $title, string $imageUri)
    {
        try {
            $this->setAccessToken($user);
            $formsService = new \Google\Service\Forms($this->client);

            // Get current count to append at the end
            $currentForm = $formsService->forms->get($formId);
            $nextIndex = count($currentForm->getItems() ?? []);

            $image = new \Google\Service\Forms\Image();
            $image->setSourceUri($imageUri);

            $imageItem = new \Google\Service\Forms\ImageItem();
            $imageItem->setImage($image);

            $item = new \Google\Service\Forms\Item();
            $item->setTitle($title);
            $item->setImageItem($imageItem);

            $createItemRequest = new \Google\Service\Forms\CreateItemRequest();
            $createItemRequest->setItem($item);
            $location = new \Google\Service\Forms\Location();
            $location->setIndex($nextIndex);
            $createItemRequest->setLocation($location);

            $request = new \Google\Service\Forms\Request();
            $request->setCreateItem($createItemRequest);

            $batchRequest = new \Google\Service\Forms\BatchUpdateFormRequest();
            $batchRequest->setRequests([$request]);

            return $formsService->forms->batchUpdate($formId, $batchRequest);
        } catch (\Exception $e) {
            Log::error('Google Forms Add Image Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateFormSettings(User $user, string $formId, array $settingsData)
    {
        try {
            $this->setAccessToken($user);
            $formsService = new \Google\Service\Forms($this->client);

            $settings = new \Google\Service\Forms\FormSettings();
            if (isset($settingsData['emailCollectionType'])) {
                $settings->setEmailCollectionType($settingsData['emailCollectionType']);
            } else {
                $settings->setEmailCollectionType('DO_NOT_COLLECT');
            }

            $updateSettingsRequest = new \Google\Service\Forms\UpdateSettingsRequest();
            $updateSettingsRequest->setSettings($settings);
            $updateSettingsRequest->setUpdateMask('emailCollectionType');

            $request = new \Google\Service\Forms\Request();
            $request->setUpdateSettings($updateSettingsRequest);

            $batchRequest = new \Google\Service\Forms\BatchUpdateFormRequest();
            $batchRequest->setRequests([$request]);

            return $formsService->forms->batchUpdate($formId, $batchRequest);
        } catch (\Exception $e) {
            Log::error('Google Forms Update Settings Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getFormResponses(User $user, string $formId)
    {
        try {
            $this->setAccessToken($user);
            $formsService = new \Google\Service\Forms($this->client);
            $responses = $formsService->forms_responses->listFormsResponses($formId);
            
            $summary = [
                'total' => count($responses->getResponses()),
                'responses' => []
            ];

            foreach ($responses->getResponses() as $response) {
                $answers = [];
                foreach ($response->getAnswers() as $questionId => $answer) {
                    $val = '';
                    if ($answer->getTextAnswers()) {
                        $val = implode(', ', array_map(fn($t) => $t->getValue(), $answer->getTextAnswers()->getAnswers()));
                    }
                    $answers[$questionId] = $val;
                }
                $summary['responses'][] = [
                    'responseId' => $response->getResponseId(),
                    'createTime' => $response->getCreateTime(),
                    'answers' => $answers
                ];
            }

            return $summary;
        } catch (\Exception $e) {
            Log::error('Google Forms Get Responses Error: ' . $e->getMessage());
            return ['total' => 0, 'responses' => []];
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

    private function normalizeYouTubeUrl(string $url): string
    {
        $videoId = '';
        // Extract video ID from various YouTube URL formats
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            $videoId = $match[1];
        }
        return $videoId ? "https://www.youtube.com/watch?v=" . $videoId : $url;
    }
}
