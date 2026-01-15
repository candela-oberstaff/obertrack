<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecoveryHoursController;

Route::middleware(['auth'])->prefix('recovery-hours')->name('recovery.')->group(function () {
    // POST /recovery-hours - Store a new recovery request
    Route::post('/', [RecoveryHoursController::class, 'store'])->name('store');
    
    // GET /recovery-hours - Get recovery history for the user
    Route::get('/', [RecoveryHoursController::class, 'index'])->name('index');
    
    // POST /recovery-hours/{id}/status - Approve/Reject (Employer)
    Route::post('/{id}/status', [RecoveryHoursController::class, 'updateStatus'])->name('update-status');
});
