<?php

/**
 * ============================================================================
 * WORK HOURS ROUTES
 * ============================================================================
 * 
 * Routes for work hours management including:
 * - Registering work hours
 * - Approving work hours (weekly/monthly)
 * - Downloading work hours reports
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkHoursController;
use App\Http\Controllers\WorkHourApprovalController;

Route::middleware(['auth'])->prefix('work-hours')->name('work-hours.')->group(function () {
    
    // Work Hours Registration
    // POST /work-hours - Store work hours for a specific date
    Route::post('/', [WorkHoursController::class, 'store'])
        ->name('store');
    
    // POST /work-hours/approve-days - Approve specific work hours by date
    Route::post('/approve-days', [WorkHoursController::class, 'approveDays'])
        ->name('approve-days');
    
    // POST /work-hours/approve - Approve work hours (general approval endpoint)
    Route::post('/approve', [WorkHourApprovalController::class, 'approve'])
        ->name('approve');
    
    // POST /work-hours/approve-week - Approve work hours for a specific week
    Route::post('/approve-week', [WorkHoursController::class, 'approveWeek'])
        ->name('approve-week');
    
    // POST /work-hours/approve-week-with-comment - Approve week with a comment
    Route::post('/approve-week-with-comment', [WorkHoursController::class, 'approveWeekWithComment'])
        ->name('approve-week-with-comment');
    
    // POST /work-hours/approve-month - Approve work hours for an entire month
    Route::post('/approve-month', [WorkHoursController::class, 'approveMonth'])
        ->name('approve-month');

    // POST /work-hours/update-comment/{id} - Update comment for a specific work hour record
    Route::post('/update-comment/{id}', [WorkHoursController::class, 'updateComment'])
        ->name('update-comment');
    
    // Reports
    // GET /work-hours/download-monthly-report/{month} - Download monthly work hours report
    // Parameters: month (format: Y-m), employee_id (optional query parameter)
    Route::get('/download-monthly-report/{month}', [WorkHoursController::class, 'downloadMonthlyReport'])
        ->name('download-monthly-report');
});
