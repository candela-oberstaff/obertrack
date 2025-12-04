<?php

/**
 * ============================================================================
 * MAIN WEB ROUTES
 * ============================================================================
 * 
 * This file contains the core application routes and includes modularized
 * route files for better organization and maintainability.
 * 
 * Route Organization:
 * - auth.php: Authentication routes (login, register, password reset, etc.)
 * - profile.php: User profile and account management
 * - employer.php: Employer-specific functionality
 * - manager.php: Manager-specific functionality
 * - employee.php: Employee-specific functionality
 * - tasks.php: General task management
 * - work-hours.php: Work hours registration and approval
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// GET / - Welcome page
Route::get('/', function () {
    return view('welcome');
});



/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

// GET /dashboard - Main dashboard (redirects based on user role)
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = Auth::user();
    
    // Employers see the original dashboard with action cards
    if ($user->tipo_usuario === 'empleador') {
        return view('dashboard', ['nombreUsuario' => $user->name]);
    }
    
    // Professionals (employees and managers) see their specific dashboard
    return view('dashboard-professional');
})->name('dashboard');

// Mock Chat Route
Route::middleware(['auth'])->get('/chat', function () {
    return view('chat.mock');
})->name('chat');

// Notification Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/tasks/{task}/mark-read', [App\Http\Controllers\NotificationController::class, 'markTaskAsRead'])->name('notifications.mark-task-read');
});


/*
|--------------------------------------------------------------------------
| Modularized Route Files
|--------------------------------------------------------------------------
| 
| The following files contain organized routes by functionality:
*/

// Authentication routes (login, register, logout, password reset, etc.)
require __DIR__.'/auth.php';

// Profile and user management routes
require __DIR__.'/profile.php';

// Employer-specific routes
require __DIR__.'/employer.php';

// Manager-specific routes
require __DIR__.'/manager.php';

// Employee-specific routes
require __DIR__.'/employee.php';

// General task management routes
require __DIR__.'/tasks.php';

// Work hours management routes
require __DIR__.'/work-hours.php';
