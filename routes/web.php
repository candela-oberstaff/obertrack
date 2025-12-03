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
    $nombreUsuario = $user ? $user->name : 'Invitado';
    return view('dashboard', ['nombreUsuario' => $nombreUsuario]);
})->name('dashboard');

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
