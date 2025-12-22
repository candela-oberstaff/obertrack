<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WorkHours;
use App\Services\ProfessionalActivityService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function __construct(
        private ProfessionalActivityService $activityService
    ) {}

    public function index()
    {
        // Only superadmins (Analyst) can access
        // Temporarily allowing candela@oberstaff.com to fix the 403
        if (!auth()->user()->is_superadmin && auth()->user()->email !== 'candela@oberstaff.com') {
            abort(403);
        }

        $professionals = $this->activityService->getProfessionalsStatus();
        
        $stats = [
            'total_professionals' => User::where('tipo_usuario', 'empleado')->count(),
            'total_companies' => User::where('tipo_usuario', 'empleador')->count(),
            'yellow_alerts' => $professionals->where('status', 'yellow')->count(),
            'red_alerts' => $professionals->where('status', 'red')->count(),
        ];

        return view('admin.dashboard', compact('professionals', 'stats'));
    }

    public function sendMassEmail(Request $request, \App\Services\BrevoEmailService $emailService)
    {
        $request->validate([
            'segment' => 'required|in:all_professionals,all_companies,red_alerts,yellow_alerts',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $users = collect();

        switch ($request->segment) {
            case 'all_professionals':
                $users = User::where('tipo_usuario', 'empleado')->get();
                break;
            case 'all_companies':
                $users = User::where('tipo_usuario', 'empleador')->get();
                break;
            case 'red_alerts':
                $professionals = $this->activityService->getProfessionalsStatus();
                $users = $professionals->where('status', 'red')->pluck('user');
                break;
            case 'yellow_alerts':
                $professionals = $this->activityService->getProfessionalsStatus();
                $users = $professionals->where('status', 'yellow')->pluck('user');
                break;
        }

        $count = 0;
        foreach ($users as $user) {
            if ($user->email) {
                // Using generic sendEmail method
                $emailService->sendEmail($user->email, $user->name, $request->subject, nl2br(e($request->message)));
                $count++;
            }
        }

        return back()->with('status', "Comunicación enviada a {$count} destinatarios con éxito.");
    }
}
