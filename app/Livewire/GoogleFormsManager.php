<?php

namespace App\Livewire;

use App\Services\GoogleFormsService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class GoogleFormsManager extends Component
{
    public $newFormTitle = '';
    public $showCreateModal = false;

    protected $rules = [
        'newFormTitle' => 'required|string|min:3|max:255',
    ];

    public function mount(GoogleFormsService $formsService)
    {
        $user = Auth::user();
        $this->isConnected = $user->isGoogleFormsConnected();
        $this->email = $user->google_forms_email;

        if ($this->isConnected) {
            $this->loadForms($formsService, $user);
        }
    }

    public function loadForms(GoogleFormsService $formsService, $user)
    {
        try {
            $this->forms = $formsService->listForms($user);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar los formularios: ' . $e->getMessage());
        }
    }

    public function createForm(GoogleFormsService $formsService)
    {
        $this->validate();

        try {
            $user = Auth::user();
            $formsService->createForm($user, $this->newFormTitle);
            
            $this->newFormTitle = '';
            $this->showCreateModal = false;
            $this->loadForms($formsService, $user);
            
            session()->flash('success', 'Formulario creado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el formulario: ' . $e->getMessage());
        }
    }

    public function deleteForm(GoogleFormsService $formsService, $formId)
    {
        try {
            $user = Auth::user();
            $formsService->deleteForm($user, $formId);
            
            $this->loadForms($formsService, $user);
            
            session()->flash('success', 'Formulario eliminado exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el formulario: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.google-forms-manager')
            ->layout('layouts.app');
    }
}
