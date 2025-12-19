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

// Fallback Livewire Asset Route (Bypasses public folder issues)
// Fallback Livewire Asset Route (Bypasses public folder and Nginx static rules)
Route::get('/livewire-script', function () {
    $path = base_path('vendor/livewire/livewire/dist/livewire.js');
    if (!file_exists($path)) {
        return response("Livewire asset not found at path: " . $path, 404, ['Content-Type' => 'text/plain']);
    }
    return response()->file($path, [
        'Content-Type' => 'application/javascript',
        'Cache-Control' => 'no-store, no-cache, must-revalidate',
    ]);
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
        return redirect()->route('empleador.dashboard');
    }
    
    // Professionals (employees and managers) see their specific dashboard
    return view('dashboard-professional');
})->name('dashboard');

// Chat Route
use App\Livewire\Chat;
Route::middleware(['auth'])->get('/chat', Chat::class)->name('chat');


// Contacto Route
Route::view('/contacto', 'contacto')->name('contacto');

// Notification Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/tasks/{task}/mark-read', [App\Http\Controllers\NotificationController::class, 'markTaskAsRead'])->name('notifications.mark-task-read');
    Route::post('/notifications/tasks/{task}/mark-read', [App\Http\Controllers\NotificationController::class, 'markTaskAsRead'])->name('notifications.mark-task-read');
    
    // Tour Route
    Route::post('/user/tour-completed', [App\Http\Controllers\TourController::class, 'complete'])->name('tour.complete');
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

// Professional reports routes
require __DIR__.'/reports.php';
