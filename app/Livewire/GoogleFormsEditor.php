<?php

namespace App\Livewire;

use App\Services\GoogleFormsService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class GoogleFormsEditor extends Component
{
    public $formId;
    public $formTitle = '';
    public $questions = [];
    
    // New Question Input
    public $newQuestionTitle = '';
    public $newQuestionType = 'TEXT';
    public $newQuestionOptions = ['']; // For RADIO type

    protected $rules = [
        'newQuestionTitle' => 'required|string|min:3|max:255',
        'newQuestionType' => 'required|in:TEXT,RADIO',
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
            $this->formTitle = $form->info->title;
            // Provide a default empty array if no items exist
            $this->questions = $form->items ?? [];
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar el formulario: ' . $e->getMessage());
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
                // Filter empty options
                $options = array_filter($this->newQuestionOptions);
                if (empty($options)) {
                    $this->addError('newQuestionOptions', 'Debes agregar al menos una opción.');
                    return;
                }
                $data['options'] = array_values($options);
            }

            $formsService->addQuestion($user, $this->formId, $data);
            
            // Reset input
            $this->newQuestionTitle = '';
            $this->newQuestionType = 'TEXT';
            $this->newQuestionOptions = [''];
            
            // Reload form to show new question
            $this->loadForm($formsService);
            
            session()->flash('success', 'Pregunta agregada exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al agregar la pregunta: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.google-forms-editor')
            ->layout('layouts.app');
    }
}
