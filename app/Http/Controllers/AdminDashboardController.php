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

        $allProfessionals = User::where('tipo_usuario', 'empleado')->orderBy('name')->get();
        $allCompanies = User::where('tipo_usuario', 'empleador')->orderBy('name')->get();

        // Mass Email Statistics (Personalized for the current admin)
        $emailLogs = \App\Models\MassEmailLog::where('user_id', auth()->id())->get();
        $emailStats = [
            'total_sessions' => $emailLogs->count(),
            'total_recipients' => $emailLogs->sum('recipient_count'),
            'by_segment' => [
                'professionals' => $emailLogs->filter(fn($l) => in_array($l->segment, ['all_professionals', 'red_alerts', 'yellow_alerts', 'individual_professional']))->sum('recipient_count'),
                'companies' => $emailLogs->filter(fn($l) => in_array($l->segment, ['all_companies', 'individual_company']))->sum('recipient_count'),
                'individuals' => $emailLogs->filter(fn($l) => in_array($l->segment, ['individual_professional', 'individual_company']))->count(),
            ],
            'recent_logs' => $emailLogs->sortByDesc('created_at')->take(5)
        ];

        return view('admin.dashboard', compact('professionals', 'stats', 'allProfessionals', 'allCompanies', 'emailStats'));
    }

    public function sendMassEmail(Request $request, \App\Services\BrevoEmailService $emailService)
    {
        $request->validate([
            'segment' => 'required|in:all_professionals,all_companies,red_alerts,yellow_alerts,individual_professional,individual_company',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'individual_id' => 'required_if:segment,individual_professional,individual_company|nullable|exists:users,id',
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
            case 'individual_professional':
            case 'individual_company':
                if ($request->individual_id) {
                    $users = User::where('id', $request->individual_id)->get();
                }
                break;
        }

        $count = 0;
        foreach ($users as $user) {
            if ($user->email) {
                // Render the professional template
                $htmlContent = view('emails.mass-email-template', [
                    'subject' => $request->subject,
                    'content' => $request->message // Quill content (HTML)
                ])->render();

                // Using generic sendEmail method with rendered HTML
                $emailService->sendEmail($user->email, $user->name, $request->subject, $htmlContent);
                $count++;
            }
        }

        // Log the mass email
        if ($count > 0) {
            \App\Models\MassEmailLog::create([
                'user_id' => auth()->id(),
                'segment' => $request->segment,
                'subject' => $request->subject,
                'recipient_count' => $count,
                'target_user_id' => in_array($request->segment, ['individual_professional', 'individual_company']) ? $request->individual_id : null,
            ]);
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

    public function showCompany($id)
    {
        $company = User::where('tipo_usuario', 'empleador')->with('empleados')->findOrFail($id);
        
        // Stats
        $totalEmployees = $company->empleados->count();
        $employeeIds = $company->empleados->pluck('id');
        $totalTasks = \App\Models\Task::where(function($q) use ($employeeIds, $company) {
            $q->whereIn('created_by', $employeeIds->push($company->id))
              ->orWhereHas('assignees', function($sub) use ($employeeIds, $company) {
                  $sub->whereIn('users.id', $employeeIds);
              });
        })->count();
        
        // Current Employees
        $currentEmployees = $this->activityService->getStatusesForUsers($company->empleados)
            ->map(function ($p) {
                $p['relation'] = 'active'; // Current employee
                return $p;
            });

        // Disconnected Employees (Past History via Tasks)
        // Find users who have tasks created by this company, but are NOT in current employees list
        $taskAssigneeIds = \DB::table('task_user')
            ->join('tasks', 'task_user.task_id', '=', 'tasks.id')
            ->where('tasks.created_by', $company->id)
            ->pluck('task_user.user_id')
            ->unique();
            
        $disconnectedUserIds = $taskAssigneeIds->diff($company->empleados->pluck('id'));
        
        $disconnectedEmployees = collect();
        if ($disconnectedUserIds->isNotEmpty()) {
            $disconnectedUsers = User::whereIn('id', $disconnectedUserIds)->get();
            $disconnectedEmployees = $this->activityService->getStatusesForUsers($disconnectedUsers)
                ->map(function ($p) {
                    $p['relation'] = 'disconnected'; // Past employee
                    return $p;
                });
        }

        // Merge both lists (convert to base Collection since we're working with arrays, not models)
        $employeesWithStatus = collect($currentEmployees)->merge(collect($disconnectedEmployees));

        return view('admin.companies.show', compact('company', 'totalEmployees', 'totalTasks', 'employeesWithStatus'));
    }

    public function showProfessional($id)
    {
        $professional = User::where('tipo_usuario', 'empleado')->with('empleador')->findOrFail($id);
        
        // Punctuality Logic
        // Calculate workable days since created_at or last 30 days
        $startTracking = $professional->created_at->startOfDay();
        $today = Carbon::now()->startOfDay();
        
        $totalWorkDays = 0;
        $daysWithHours = 0;

        // Iterate days (simplified logic, ideally use a helper or cleaner loop)
        $current = $startTracking->copy();
        while ($current->lte($today)) {
            if (!$current->isWeekend()) {
                $totalWorkDays++;
                // Check if has hours
                $hasHours = WorkHours::where('user_id', $professional->id)
                    ->whereDate('work_date', $current->format('Y-m-d'))
                    ->exists();
                if ($hasHours) $daysWithHours++;
            }
            $current->addDay();
        }

        $punctualityScore = $totalWorkDays > 0 ? round(($daysWithHours / $totalWorkDays) * 100) : 0;

        // Task Stats
        $tasks = $professional->assignedTasks;
        $totalTasks = $tasks->count();
        $completedTasks = $tasks->where('status', 'finalizado')->count();
        $pendingTasks = $tasks->where('status', '!=', 'finalizado')->count();
        
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // On Time (Approximation: completed tasks where updated_at <= end_date)
        $onTimeTasks = $tasks->filter(function($task) {
            return $task->status === 'finalizado' && $task->end_date && $task->updated_at->lte($task->end_date->endOfDay());
        })->count();

        return view('admin.professionals.show', compact(
            'professional', 
            'punctualityScore', 
            'totalWorkDays', 
            'daysWithHours',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'completionRate',
            'onTimeTasks'
        ));
    }
}
