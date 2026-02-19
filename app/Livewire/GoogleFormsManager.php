<?php

namespace App\Livewire;

use App\Services\GoogleFormsService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class GoogleFormsManager extends Component
{
    public $isConnected = false;
    public $forms = [];
    public $email = '';

    public function mount(GoogleFormsService $formsService)
    {
        $user = Auth::user();
        $this->isConnected = $user->isGoogleFormsConnected();
        $this->email = $user->google_forms_email;

        if ($this->isConnected) {
            $this->forms = $formsService->listForms($user);
        }
    }

    public function render()
    {
        return view('livewire.google-forms-manager')
            ->layout('layouts.app');
    }
}
