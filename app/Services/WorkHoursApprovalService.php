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
            ->update(['approved' => true]);
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
                'approved' => true,
                'approval_comment' => $comment,
            ]);
    }

    /**
     * Approve work hours for an entire month
     */
    public function approveMonth($userId, $month)
    {
        return WorkHours::where('user_id', $userId)
            ->whereRaw("DATE_FORMAT(work_date, '%Y-%m') = ?", [$month])
            ->update(['approved' => true]);
    }
}
