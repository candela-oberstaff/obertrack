<?php

/**
 * ============================================================================
 * PROFILE & USER MANAGEMENT ROUTES
 * ============================================================================
 * 
 * Routes for user profile management including:
 * - Profile editing and deletion
 * - Manager promotion/demotion
 * - Superadmin management
 * - Employee management
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::middleware(['auth'])->group(function () {
    
    // Profile Management
    // GET /profile - Show profile edit form
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    
    // PATCH /profile - Update profile information
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    // POST /profile/send-password-code - Send OTP for password reset
    Route::post('/profile/send-password-code', [ProfileController::class, 'sendPasswordCode'])
        ->name('profile.send-password-code');
    
    // PUT /profile/update-password-with-code - Update password using OTP
    Route::put('/profile/update-password-with-code', [ProfileController::class, 'updatePasswordWithCode'])
        ->name('profile.update-password-with-code');
    
    // DELETE /profile - Delete user account
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
    
    // Manager Management
    // PUT /profile/{user}/promover-manager - Promote user to manager
    Route::put('/profile/{user}/promover-manager', [ProfileController::class, 'promoverAManager'])
        ->name('profile.promover-manager');
    
    // PUT /profile/{user}/degradar-manager - Demote user from manager
    Route::put('/profile/{user}/degradar-manager', [ProfileController::class, 'degradarDeManager'])
        ->name('profile.degradar-manager');
    
    // Superadmin Management
    // PUT /profile/{user}/toggle-superadmin - Toggle superadmin status
    Route::put('/profile/{user}/toggle-superadmin', [ProfileController::class, 'toggleSuperAdmin'])
        ->name('profile.toggle-superadmin');
    
    // Employee Management
    // DELETE /profile/eliminar-empleado/{empleado} - Delete an employee
    Route::delete('/profile/eliminar-empleado/{empleado}', [ProfileController::class, 'eliminarEmpleado'])
        ->name('profile.eliminar-empleado');
    
    // GET /empleadores - Get list of employers (legacy endpoint)
    Route::get('/empleadores', [ProfileController::class, 'obtenerEmpleadores']);
});
