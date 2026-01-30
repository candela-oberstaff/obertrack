<?php

/**
 * ============================================================================
 * EMPLOYER ROUTES
 * ============================================================================
 * 
 * Routes for employer-specific functionality including:
 * - Viewing employee tasks and dashboard
 * - Creating and managing tasks for employees
 * - Managing comments on employer-created tasks
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CompanyTaskController;

Route::middleware(['auth'])->prefix('empresa')->name('empresa.')->group(function () {
    
    // Dashboard
    // GET /empresa/dashboard - View company dashboard with professional work hours summary
    Route::get('/dashboard', [DashboardController::class, 'empresaDashboard'])
        ->name('dashboard');

    // GET /empresa/detalle-diario/{date} - View full details of a specific day for all professionals
    Route::get('/detalle-diario/{date}', [DashboardController::class, 'dailyDetail'])
        ->name('detalle-diario');

    // API: GET /empresa/api/day-details/{date} - Get JSON details for real-time updates
    Route::get('/api/day-details/{date}', [DashboardController::class, 'getDayDetailsJson'])
        ->name('api.day-details');
    
    // Task Management
    // GET /empresa/tareas - View all professional tasks with filters and charts
    Route::get('/tareas', [CompanyTaskController::class, 'index'])
        ->name('tareas.index');
    
    // GET /empresa/tareas/crear - Show form to create a task for a professional
    Route::get('/tareas/crear', [TaskController::class, 'createForProfessional'])
        ->name('tareas.create');
    
    // POST /empresa/tareas - Store a new task for a professional
    Route::post('/tareas', [CompanyTaskController::class, 'store'])
        ->name('tareas.store');
    
    // POST /empresa/crear-tarea - Alternative endpoint to create task for professional
    Route::post('/crear-tarea', [DashboardController::class, 'crearTareaParaProfesional'])
        ->name('crear-tarea');
    
    // GET /empresa/tareas/{task}/editar - Show form to edit a task
    Route::get('/tareas/{task}/editar', [TaskController::class, 'edit'])
        ->name('tareas.edit');
    
    // PUT /empresa/tareas/{task} - Update an existing task
    Route::put('/tareas/{task}', [TaskController::class, 'update'])
        ->name('tareas.update');
    
    // DELETE /empresa/tareas/{task} - Delete a task
    Route::delete('/tareas/{task}', [TaskController::class, 'destroy'])
        ->name('tareas.destroy');
    
    // POST /empresa/tareas/{taskId}/toggle-completion - Toggle task completion status
    Route::post('/tareas/{taskId}/toggle-completion', [TaskController::class, 'toggleCompanyTaskCompletion'])
        ->name('tareas.toggle-completion');
    
    // Comments Management (via CommentController)
    // POST /empresa/comments - Add a comment to a task
    Route::post('/comments', [CommentController::class, 'storeEmployerComment'])
        ->name('comments.store');
    
    // PUT /empresa/comments/{id} - Update a comment
    Route::put('/comments/{id}', [CommentController::class, 'updateEmployerComment'])
        ->name('comments.update');
    
    // DELETE /empresa/comments/{id} - Delete a comment
    Route::delete('/comments/{id}', [CommentController::class, 'destroyEmployerComment'])
        ->name('comments.destroy');
    
    // Comments Management (via CompanyTaskController)
    // POST /empresa/tareas/{taskId}/comments - Add a comment to a specific task
    Route::post('/tareas/{taskId}/comments', [CompanyTaskController::class, 'addComment'])
        ->name('tareas.comments.add');
    
    // PUT /empresa/tareas/{taskId}/comments/{commentId} - Update a task comment
    Route::put('/tareas/{taskId}/comments/{commentId}', [CompanyTaskController::class, 'updateComment'])
        ->name('tareas.comments.update');
    
    // DELETE /empresa/tareas/{taskId}/comments/{commentId} - Delete a task comment
    Route::delete('/tareas/{taskId}/comments/{commentId}', [CompanyTaskController::class, 'deleteComment'])
        ->name('tareas.comments.delete');
    
    // POST /empresa/tareas/{taskId}/files - Upload a file to a task
    Route::post('/tareas/{taskId}/files', [CompanyTaskController::class, 'uploadFile'])
        ->name('tareas.files.upload');

    // Professional Management
    // POST /empresa/profesionales/{professional}/toggle-manager - Toggle manager status
    // Mass Communication
    Route::get('/comunicaciones/email', [DashboardController::class, 'showMassEmailForm'])
        ->name('emails.create');

    Route::post('/mass-email', [DashboardController::class, 'sendMassEmail'])
        ->name('mass-email');

    Route::post('/mass-whatsapp', [DashboardController::class, 'sendMassWhatsapp'])
        ->name('mass-whatsapp');

    Route::get('/whatsapp/status', [DashboardController::class, 'getWhatsappStatus'])
        ->name('whatsapp.status');

    Route::post('/whatsapp/start', [DashboardController::class, 'startWhatsappSession'])
        ->name('whatsapp.start');
});


// Legacy route - kept for backward compatibility
// GET /empresas/tareas-asignadas - View assigned tasks to professionals
Route::middleware(['auth'])->get('/empresas/tareas-asignadas', [DashboardController::class, 'verTareasProfesionales'])
    ->name('empresas.tareas-asignadas');

// Legacy route - kept for backward compatibility
// GET /grafico-tareas - View tasks chart
Route::middleware(['auth'])->get('/grafico-tareas', [DashboardController::class, 'verTareasProfesionales']);
