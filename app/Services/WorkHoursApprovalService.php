<?php

namespace App\Services;

use App\Models\WorkHours;
use Illuminate\Support\Carbon;

class WorkHoursApprovalService
{
    public function __construct(
        private \App\Services\BrevoEmailService $emailService
    ) {}

    /**
     * Approve work hours for a specific week
     */
    public function approveWeek($employeeId, $weekStartDate)
    {
        $weekStart = Carbon::parse($weekStartDate)->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);

        $updated = WorkHours::where('user_id', $employeeId)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->update(['approved' => \Illuminate\Support\Facades\DB::raw('true')]);

        if ($updated) {
            $this->notifyUser($employeeId, [
                'type' => 'Semana',
                'period' => $weekStart->format('d/m/Y') . ' al ' . $weekEnd->format('d/m/Y')
            ]);
        }

        return $updated;
    }

    /**
     * Approve work hours for a week with a comment
     */
    public function approveWeekWithComment($employeeId, $weekStartDate, $comment)
    {
        $weekStart = Carbon::parse($weekStartDate)->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);

        $updated = WorkHours::where('user_id', $employeeId)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->update([
                'approved' => \Illuminate\Support\Facades\DB::raw('true'),
                'approval_comment' => $comment,
            ]);

        if ($updated) {
            $this->notifyUser($employeeId, [
                'type' => 'Semana',
                'period' => $weekStart->format('d/m/Y') . ' al ' . $weekEnd->format('d/m/Y'),
                'comment' => $comment
            ]);
        }

        return $updated;
    }

    /**
     * Approve specific work hours by date
     */
    public function approveDates($employeeId, array $dates, $comment = null)
    {
        $query = WorkHours::where('user_id', $employeeId)
            ->whereIn('work_date', $dates);

        $data = ['approved' => \Illuminate\Support\Facades\DB::raw('true')];
        
        if ($comment !== null) {
            $data['approval_comment'] = $comment;
        }

        $updated = $query->update($data);

        if ($updated) {
            $this->notifyUser($employeeId, [
                'type' => 'Días específicos',
                'period' => count($dates) . ' día(s)',
                'comment' => $comment
            ]);
        }

        return $updated;
    }

    public function approveMonth($userId, $month)
    {
        $updated = WorkHours::where('user_id', $userId)
            ->whereRaw("TO_CHAR(work_date, 'YYYY-MM') = ?", [$month])
            ->update(['approved' => \Illuminate\Support\Facades\DB::raw('true')]);

        if ($updated) {
            $this->notifyUser($userId, [
                'type' => 'Mes',
                'period' => $month
            ]);
        }

        return $updated;
    }

    /**
     * Internal helper to notify user
     */
    private function notifyUser($userId, $approvalData)
    {
        try {
            $user = \App\Models\User::find($userId);
            if ($user && $user->email) {
                $approvalData['approved_by'] = auth()->user()->name ?? 'Administrador';
                $this->emailService->sendWorkHoursApprovedNotification(
                    $user->email,
                    $user->name,
                    $approvalData
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error sending work hour approval notification: ' . $e->getMessage());
        }
    }
}
