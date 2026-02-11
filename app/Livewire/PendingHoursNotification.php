<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class PendingHoursNotification extends Component
{
    public $pendingCount = 0;

    protected $listeners = [
        'hours-approved' => 'updatePendingCount',
        'refresh-navigation-menu' => '$refresh'
    ];

    public function mount()
    {
        $this->updatePendingCount();
    }

    public function updatePendingCount()
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Only relevant for Managers/Employers
            if ($user->tipo_usuario === 'empleador' || $user->is_manager) {
                // Determine the "company" user
                $company = $user->tipo_usuario === 'empleador' ? $user : $user->empleador; // Adjust relationship if needed
                
                // Fallback if relationship is different (e.g. empleador_id)
                if (!$company && $user->empleador_id) {
                     $company = \App\Models\User::find($user->empleador_id);
                }
                
                if ($company) {
                    $notificationService = app(NotificationService::class);
                    // Force refresh cache? Or relying on service to be accurate?
                    // Service uses cache. We might want to clear it if we suspect staleness, 
                    // but for now let's trust the service.
                    $data = $notificationService->getPendingHoursForCompany($company);
                    $this->pendingCount = $data['pending_count'];
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.pending-hours-notification');
    }
}
