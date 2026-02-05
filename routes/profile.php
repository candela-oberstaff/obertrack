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
 * - Professional management
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

    // PUT /profile/password - Update password
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');
    
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
    
    // Professional Management
    // DELETE /profile/eliminar-profesional/{profesional} - Delete a professional
    Route::delete('/profile/eliminar-profesional/{profesional}', [ProfileController::class, 'eliminarProfesional'])
        ->name('profile.eliminar-profesional');
    
    // GET /empresas - Get list of companies (legacy endpoint)
    Route::get('/empresas', [ProfileController::class, 'obtenerEmpresas']);
});
