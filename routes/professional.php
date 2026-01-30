<?php

/**
 * ============================================================================
 * EMPLOYEE ROUTES
 * ============================================================================
 * 
 * Routes for employee-specific functionality including:
 * - Viewing assigned tasks
 * - Managing task completion status
 * - Adding comments to tasks
 * - Registering work hours
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfessionalTaskController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\TaskController;

Route::middleware(['auth'])->prefix('profesionales')->name('profesionales.')->group(function () {
    
    // Task Management
    // GET /profesionales/tareas - View all tasks assigned to the professional
    Route::get('/tareas', [ProfessionalTaskController::class, 'index'])
        ->name('tasks.index');
    
    // GET /profesionales/tareas/{task} - View details of a specific task
    Route::get('/tareas/{task}', [ProfessionalTaskController::class, 'show'])
        ->name('tasks.show');
    
    // POST /profesionales/tareas/{task}/toggle-completion - Toggle task completion status
    Route::post('/tareas/{task}/toggle-completion', [ProfessionalTaskController::class, 'toggleCompletion'])
        ->name('tasks.toggle-completion');
    
    // Comments Management
    // POST /profesionales/tareas/{task}/comment - Add a comment to a task
    Route::post('/tareas/{task}/comment', [ProfessionalTaskController::class, 'addComment'])
        ->name('tasks.comment');
    
    // PUT /profesionales/tareas/comment/{comment} - Update a comment
    Route::put('/tareas/comment/{comment}', [ProfessionalTaskController::class, 'updateComment'])
        ->name('tasks.comment.update');
    
    // DELETE /profesionales/tareas/comment/{comment} - Delete a comment
    Route::delete('/tareas/comment/{comment}', [ProfessionalTaskController::class, 'deleteComment'])
        ->name('tasks.comment.delete');
    
    // POST /profesionales/tareas/{task}/files - Upload a file
    Route::post('/tareas/{task}/files', [ProfessionalTaskController::class, 'uploadFile'])
        ->name('tasks.files.upload');
    
    // Legacy Routes
    // GET /profesionales/editar-tareas - Edit tasks view (legacy)
    Route::get('/editar-tareas', [TaskController::class, 'index'])
        ->name('editar-tareas');
    
    // GET /profesionales/crear-tarea - Show form to create a task (legacy)
    Route::get('/crear-tarea', [ProfessionalController::class, 'create'])
        ->name('crear-tarea');
});

// Professional Work Hours
// GET /profesional/registrar-horas - Show form to register work hours
Route::middleware(['auth'])->get('/profesional/registrar-horas', [ProfessionalController::class, 'registrarHoras'])
    ->name('profesional.registrar-horas');
