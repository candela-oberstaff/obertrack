<?php

/**
 * ============================================================================
 * REPORTS ROUTES
 * ============================================================================
 * 
 * Routes for viewing professional reports and statistics
 */

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureUserIsEmployer;

// Professional Reports
Route::middleware(['auth', EnsureUserIsEmployer::class])->prefix('reportes')->name('reportes.')->group(function () {
    
    // GET /reportes - View list of all professionals with statistics
    Route::get('/', [WorkHoursController::class, 'reportsIndex'])
        ->name('index');
    
    // GET /reportes/profesional/{user} - View individual professional report detail
    Route::get('/profesional/{user}', [WorkHoursController::class, 'professionalReport'])
        ->name('show');
        
    // PDF Downloads
    Route::get('/profesional/{user}/download-weekly', [WorkHoursController::class, 'downloadWeeklyReport'])
        ->name('download.weekly');
        
    Route::get('/profesional/{user}/download-monthly', [WorkHoursController::class, 'downloadMonthlyReportPdf'])
        ->name('download.monthly');
});
