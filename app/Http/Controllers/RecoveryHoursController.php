<?php

namespace App\Http\Controllers;

use App\Models\RecoveryHour;
use App\Models\User;
use App\Models\WorkHours;
use App\Services\BrevoEmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecoveryHoursController extends Controller
{
    public function __construct(
        private BrevoEmailService $emailService
    ) {}

    /**
     * Store a new recovery hour record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'hours' => 'required|numeric|min:0.5|max:12',
            'activities' => 'required|string|min:5',
        ]);

        $user = auth()->user();
        $today = now()->toDateString();

        // Create recovery record
        try {
            $recovery = RecoveryHour::create([
                'user_id' => $user->id,
                'recovery_date' => $today,
                'hours_recovered' => $request->hours,
                'activities' => strip_tags($request->activities),
                'approved' => null, // Pending approval (null)
            ]);

            // Notify company (empresa)
            $empresa = $user->empleador_id ? User::find($user->empleador_id) : null;
            if ($empresa) {
                try {
                    $this->emailService->sendRecoveryRequestNotification(
                        $empresa->email,
                        $empresa->name,
                        [
                            'professional_name' => $user->name,
                            'date' => now()->format('d/m/Y'),
                            'hours' => $request->hours,
                            'activities' => $request->activities
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error('Error sending recovery notification: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Solicitud de recuperación registrada correctamente. Pendiente de aprobación.',
                'recovery' => $recovery
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving recovery hours: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la recuperación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recovery history for the authenticated professional.
     */
    public function index()
    {
        $user = auth()->user();
        $recoveries = RecoveryHour::where('user_id', $user->id)
            ->orderBy('recovery_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'recoveries' => $recoveries
        ]);
    }

    /**
     * Approve or reject a recovery request (Company/Admin).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'approved' => 'required|boolean'
        ]);

        $recovery = RecoveryHour::findOrFail($id);
        
        // Authorization check
        $currentUser = auth()->user();
        $recoveryUser = User::find($recovery->user_id);
        
        $isAuthorized = $currentUser->is_superadmin || 
                        ($currentUser->tipo_usuario === 'empleador' && $recoveryUser->empleador_id === $currentUser->id) ||
                        ($currentUser->is_manager && $recoveryUser->empleador_id === $currentUser->empleador_id);

        if (!$isAuthorized) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $recovery->update([
            'approved' => $request->approved ? DB::raw('true') : DB::raw('false'),
            'approved_at' => $request->approved ? now() : null
        ]);

        // Notify professional
        try {
            $user = User::find($recovery->user_id);
            if ($user && $user->email) {
                $this->emailService->sendRecoveryStatusNotification(
                    $user->email,
                    $user->name,
                    [
                        'hours' => $recovery->hours_recovered,
                        'date' => Carbon::parse($recovery->recovery_date)->format('d/m/Y'),
                        'approved' => (bool)$request->approved,
                        'approved_by' => auth()->user()->name ?? 'Administrador',
                        'comment' => null // We don't have a comment field in RecoveryHour currently, but we could add it if needed
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error('Error sending recovery approval notification: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $request->approved ? 'Recuperación aprobada' : 'Recuperación rechazada'
        ]);
    }

    /**
     * Helper to get total debt vs recovered for multiple users in bulk.
     */
    public static function getDebtSummaries(array $userIds)
    {
        // Debt comes from WorkHours records with absence
        $debts = \App\Models\WorkHours::whereIn('user_id', $userIds)
            ->where('absence_hours', '>', 0)
            ->selectRaw('user_id, SUM(absence_hours) as total_debt')
            ->groupBy('user_id')
            ->get()
            ->pluck('total_debt', 'user_id');

        // Recovered comes from the new RecoveryHour table (Approved)
        $recoveries = \App\Models\RecoveryHour::whereIn('user_id', $userIds)
            ->whereRaw('approved IS TRUE')
            ->selectRaw('user_id, SUM(hours_recovered) as total_recovered')
            ->groupBy('user_id')
            ->get()
            ->pluck('total_recovered', 'user_id');

        // Pending Approval
        $pending = \App\Models\RecoveryHour::whereIn('user_id', $userIds)
            ->whereNull('approved')
            ->selectRaw('user_id, SUM(hours_recovered) as total_pending')
            ->groupBy('user_id')
            ->get()
            ->pluck('total_pending', 'user_id');

        $summaries = [];
        foreach ($userIds as $userId) {
            $totalDebt = (float)($debts[$userId] ?? 0);
            $totalRecovered = (float)($recoveries[$userId] ?? 0);
            $pendingApproval = (float)($pending[$userId] ?? 0);

            $summaries[$userId] = [
                'total_debt' => $totalDebt,
                'total_recovered' => $totalRecovered,
                'pending_approval' => $pendingApproval,
                'remaining_debt' => (float)max(0, $totalDebt - $totalRecovered)
            ];
        }

        return $summaries;
    }

    /**
     * Helper to get total debt vs recovered for a user.
     */
    public static function getDebtSummary($userId)
    {
        // Debt comes from WorkHours records with absence
        $totalDebt = \App\Models\WorkHours::where('user_id', $userId)
            ->where('absence_hours', '>', 0)
            ->sum('absence_hours');

        // Recovered comes from the new RecoveryHour table
        $totalRecovered = \App\Models\RecoveryHour::where('user_id', $userId)
            ->whereRaw('approved IS TRUE')
            ->sum('hours_recovered');
            
        $pendingApproval = \App\Models\RecoveryHour::where('user_id', $userId)
            ->whereNull('approved')
            ->sum('hours_recovered');

        return [
            'total_debt' => (float)$totalDebt,
            'total_recovered' => (float)$totalRecovered,
            'pending_approval' => (float)$pendingApproval,
            'remaining_debt' => (float)max(0, $totalDebt - $totalRecovered)
        ];
    }
}
