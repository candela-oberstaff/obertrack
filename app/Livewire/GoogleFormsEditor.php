<?php

namespace App\Livewire;

use App\Services\GoogleFormsService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class GoogleFormsEditor extends Component
{
    public $formId;
    public $formTitle = '';
    public $responderUri = '';
    public $questions = [];
    public $currentTab = 'preguntas'; // 'preguntas' or 'respuestas'
    
    // New Question Input
    public $newQuestionTitle = '';
    public $newQuestionType = 'TEXT';
    public $newQuestionOptions = ['']; // For RADIO type

    // Media Input
    public $mediaTitle = '';
    public $mediaUri = '';
    public $mediaType = 'IMAGE'; // 'IMAGE' or 'VIDEO'

    // Responses
    public $responses = ['total' => 0, 'responses' => []];

    protected $rules = [
        'newQuestionTitle' => 'required_if:currentTab,preguntas|string|min:3|max:255',
        'newQuestionType' => 'required_if:currentTab,preguntas|in:TEXT,RADIO',
        'newQuestionOptions.*' => 'required_if:newQuestionType,RADIO|string',
    ];

    public function mount($formId, GoogleFormsService $formsService)
    {
        $this->formId = $formId;
        $this->loadForm($formsService);
    }

    public function loadForm(GoogleFormsService $formsService)
    {
        try {
            $user = Auth::user();
            $form = $formsService->getFormContent($user, $this->formId);
            $this->formTitle = $form['info']['title'];
            $this->responderUri = $form['responderUri'];
            $this->questions = $form['items'] ?? [];
            
            if ($this->currentTab === 'respuestas') {
                $this->loadResponses($formsService);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    public function loadResponses(GoogleFormsService $formsService)
    {
        try {
            $user = Auth::user();
            $this->responses = $formsService->getFormResponses($user, $this->formId);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar respuestas: ' . $e->getMessage());
        }
    }

    public function setTab($tab, GoogleFormsService $formsService)
    {
        $this->currentTab = $tab;
        if ($tab === 'respuestas') {
            $this->loadResponses($formsService);
        } else {
            $this->loadForm($formsService);
        }
    }

    public function addOption()
    {
        $this->newQuestionOptions[] = '';
    }

    public function removeOption($index)
    {
        unset($this->newQuestionOptions[$index]);
        $this->newQuestionOptions = array_values($this->newQuestionOptions);
    }

    public function addQuestion(GoogleFormsService $formsService)
    {
        $this->validate();

        try {
            $user = Auth::user();
            $data = [
                'title' => $this->newQuestionTitle,
                'type' => $this->newQuestionType,
                'required' => false
            ];

            if ($this->newQuestionType === 'RADIO') {
                $options = array_filter($this->newQuestionOptions);
                if (empty($options)) {
                    $this->addError('newQuestionOptions', 'Debes agregar al menos una opción.');
                    return;
                }
                $data['options'] = array_values($options);
            }

            $formsService->addQuestion($user, $this->formId, $data);
            
            $this->reset(['newQuestionTitle', 'newQuestionType', 'newQuestionOptions']);
            $this->newQuestionOptions = [''];
            
            $this->loadForm($formsService);
            session()->flash('success', 'Pregunta agregada exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al agregar la pregunta: ' . $e->getMessage());
        }
    }

    public function addMedia(GoogleFormsService $formsService)
    {
        $this->validate([
            'mediaTitle' => 'required|string|max:255',
            'mediaUri' => 'required|url',
        ]);

        try {
            $user = Auth::user();
            if ($this->mediaType === 'IMAGE') {
                $formsService->addImage($user, $this->formId, $this->mediaTitle, $this->mediaUri);
            } else {
                $formsService->addVideo($user, $this->formId, $this->mediaTitle, $this->mediaUri);
            }

            $this->reset(['mediaTitle', 'mediaUri']);
            $this->loadForm($formsService);
            session()->flash('success', ucfirst(strtolower($this->mediaType)) . ' agregado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al agregar multimedia: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.google-forms-editor')
            ->layout('layouts.app');
    }
}
