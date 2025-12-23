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

    public function companies()
    {
        $companies = User::where('tipo_usuario', 'empleador')
            ->withCount('empleados')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.companies.index', compact('companies'));
    }

    public function professionals(Request $request)
    {
        $professionalsData = $this->activityService->getProfessionalsStatus();
        
        $companyId = $request->query('company_id');
        if ($companyId) {
            $professionalsData = $professionalsData->filter(function($p) use ($companyId) {
                return $p['user']->empleador_id == $companyId;
            });
        }

        // Paginate manually since it's a collection from service
        $page = $request->get('page', 1);
        $perPage = 15;
        $professionals = new \Illuminate\Pagination\LengthAwarePaginator(
            $professionalsData->forPage($page, $perPage),
            $professionalsData->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $companies = User::where('tipo_usuario', 'empleador')->get();
        $selectedCompany = $companyId ? User::find($companyId) : null;

        return view('admin.professionals.index', compact('professionals', 'companies', 'selectedCompany'));
    }

    public function assignProfessional(Request $request)
    {
        $request->validate([
            'professional_id' => 'required|exists:users,id',
            'company_id' => 'nullable|exists:users,id',
        ]);

        $professional = User::findOrFail($request->professional_id);
        $professional->empleador_id = $request->company_id;
        $professional->save();

        return back()->with('status', 'Relación actualizada correctamente.');
    }

    public function unlinkProfessional($id)
    {
        $professional = User::findOrFail($id);
        $professional->empleador_id = null;
        $professional->save();

        return back()->with('status', 'Profesional desvinculado correctamente.');
    }
}
