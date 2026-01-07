<?php

namespace App\Services;

use App\Models\WorkHours;
use Illuminate\Support\Carbon;

class WorkHoursApprovalService
{
    /**
     * Approve work hours for a specific week
     */
    public function approveWeek($employeeId, $weekStartDate)
    {
        $weekStart = Carbon::parse($weekStartDate)->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);

        return WorkHours::where('user_id', $employeeId)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->update(['approved' => \Illuminate\Support\Facades\DB::raw('true')]);
    }

    /**
     * Approve work hours for a week with a comment
     */
    public function approveWeekWithComment($employeeId, $weekStartDate, $comment)
    {
        $weekStart = Carbon::parse($weekStartDate)->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);

        return WorkHours::where('user_id', $employeeId)
            ->whereBetween('work_date', [$weekStart, $weekEnd])
            ->update([
                'approved' => \Illuminate\Support\Facades\DB::raw('true'),
                'approval_comment' => $comment,
            ]);
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

        return $query->update($data);
    }

    /**
     * Approve work hours for an entire month
     */
    public function approveMonth($userId, $month)
    {
        return WorkHours::where('user_id', $userId)
            ->whereRaw("TO_CHAR(work_date, 'YYYY-MM') = ?", [$month])
            ->update(['approved' => \Illuminate\Support\Facades\DB::raw('true')]);
    }
}
