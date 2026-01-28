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
use App\Livewire\Chat;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// GET / - Welcome page (redirects to dashboard if authenticated)
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route(Auth::user()->getDashboardRoute());
    }
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
    
    // Check if the user is already on their correct dashboard route
    $targetRoute = $user->getDashboardRoute();
    
    if (Route::currentRouteName() === $targetRoute) {
        return view('dashboard-professional');
    }

    return redirect()->route($targetRoute);
})->name('dashboard');

// Admin / Analyst Routes
Route::middleware(['auth', 'superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/mass-email', [\App\Http\Controllers\AdminDashboardController::class, 'showMassEmail'])->name('mass-email.show');
    Route::post('/mass-email', [\App\Http\Controllers\AdminDashboardController::class, 'sendMassEmail'])->name('mass-email');
    Route::post('/mass-whatsapp', [\App\Http\Controllers\AdminDashboardController::class, 'sendMassWhatsapp'])->name('mass-whatsapp');
    Route::get('/whatsapp/status', [\App\Http\Controllers\AdminDashboardController::class, 'getWhatsappStatus'])->name('whatsapp.status');
    Route::post('/whatsapp/start', [\App\Http\Controllers\AdminDashboardController::class, 'startWhatsappSession'])->name('whatsapp.start');
    Route::get('/companies', [\App\Http\Controllers\AdminDashboardController::class, 'companies'])->name('companies');
    Route::get('/professionals', [\App\Http\Controllers\AdminDashboardController::class, 'professionals'])->name('professionals');
    Route::post('/assign-professional', [\App\Http\Controllers\AdminDashboardController::class, 'assignProfessional'])->name('assign-professional');
    Route::delete('/unlink-professional/{id}', [\App\Http\Controllers\AdminDashboardController::class, 'unlinkProfessional'])->name('unlink-professional');
    
    // Email Templates
    Route::post('/email-templates/upload-image', [\App\Http\Controllers\EmailTemplateController::class, 'uploadImage'])->name('email-templates.upload-image');
    Route::resource('email-templates', \App\Http\Controllers\EmailTemplateController::class);
        
    // Detailed Views
    Route::get('/companies/{id}', [\App\Http\Controllers\AdminDashboardController::class, 'showCompany'])->name('companies.show');
    Route::get('/professionals/{id}/details', [\App\Http\Controllers\AdminDashboardController::class, 'showProfessional'])->name('professionals.show-details');
});

// Chat Route
Route::middleware(['auth'])->get('/chat/{userId?}', Chat::class)->name('chat');
Route::middleware(['auth'])->get('/whatsapp', \App\Livewire\WhatsappChat::class)->name('whatsapp.chat');
Route::middleware(['auth'])->get('/whatsapp/session-status', function(\Illuminate\Http\Request $request) {
    $waha = app(\App\Services\WahaService::class);
    $statusData = $waha->getSessionStatus('default');
    $status = $statusData['status'] ?? 'STOPPED';
    
    $qr = null;
    if ($request->query('with_qr') && $status === 'SCAN_QR_CODE') {
        $qr = $waha->getQrCode('default');
    }
    
    return response()->json(['status' => $status, 'qr' => $qr]);
})->name('whatsapp.session-status');


// Contacto Route
Route::view('/contacto', 'contacto')->name('contacto');

// Notification Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
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

// Recovery hours management routes
require __DIR__.'/recovery-hours.php';
