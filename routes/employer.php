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
use App\Http\Controllers\EmployerTaskController;

Route::middleware(['auth'])->prefix('empleador')->name('empleador.')->group(function () {
    
    // Dashboard
    // GET /empleador/dashboard - View employer dashboard with employee work hours summary
    Route::get('/dashboard', [DashboardController::class, 'empleadorDashboard'])
        ->name('dashboard');
    
    // Task Management
    // GET /empleador/tareas - View all employee tasks with filters and charts
    Route::get('/tareas', [DashboardController::class, 'verTareasEmpleados'])
        ->name('tareas.index');
    
    // GET /empleador/tareas/crear - Show form to create a task for an employee
    Route::get('/tareas/crear', [TaskController::class, 'createForEmployee'])
        ->name('tareas.create');
    
    // POST /empleador/tareas - Store a new task for an employee
    Route::post('/tareas', [TaskController::class, 'storeForEmployee'])
        ->name('tareas.store');
    
    // POST /empleador/crear-tarea - Alternative endpoint to create task for employee
    Route::post('/crear-tarea', [DashboardController::class, 'crearTareaParaEmpleado'])
        ->name('crear-tarea');
    
    // GET /empleador/tareas/{task}/editar - Show form to edit a task
    Route::get('/tareas/{task}/editar', [TaskController::class, 'edit'])
        ->name('tareas.edit');
    
    // PUT /empleador/tareas/{task} - Update an existing task
    Route::put('/tareas/{task}', [TaskController::class, 'update'])
        ->name('tareas.update');
    
    // DELETE /empleador/tareas/{task} - Delete a task
    Route::delete('/tareas/{task}', [TaskController::class, 'destroy'])
        ->name('tareas.destroy');
    
    // POST /empleador/tareas/{taskId}/toggle-completion - Toggle task completion status
    Route::post('/tareas/{taskId}/toggle-completion', [TaskController::class, 'toggleEmployerTaskCompletion'])
        ->name('tareas.toggle-completion');
    
    // Comments Management (via CommentController)
    // POST /empleador/comments - Add a comment to a task
    Route::post('/comments', [CommentController::class, 'storeEmployerComment'])
        ->name('comments.store');
    
    // PUT /empleador/comments/{id} - Update a comment
    Route::put('/comments/{id}', [CommentController::class, 'updateEmployerComment'])
        ->name('comments.update');
    
    // DELETE /empleador/comments/{id} - Delete a comment
    Route::delete('/comments/{id}', [CommentController::class, 'destroyEmployerComment'])
        ->name('comments.destroy');
    
    // Comments Management (via EmployerTaskController)
    // POST /empleador/tareas/{taskId}/comments - Add a comment to a specific task
    Route::post('/tareas/{taskId}/comments', [EmployerTaskController::class, 'addComment'])
        ->name('tareas.comments.add');
    
    // PUT /empleador/tareas/{taskId}/comments/{commentId} - Update a task comment
    Route::put('/tareas/{taskId}/comments/{commentId}', [EmployerTaskController::class, 'updateComment'])
        ->name('tareas.comments.update');
    
    // DELETE /empleador/tareas/{taskId}/comments/{commentId} - Delete a task comment
    Route::delete('/tareas/{taskId}/comments/{commentId}', [EmployerTaskController::class, 'deleteComment'])
        ->name('tareas.comments.delete');

    // Employee Management
    // POST /empleador/empleados/{employee}/toggle-manager - Toggle manager status
    Route::post('/empleados/{employee}/toggle-manager', [\App\Http\Controllers\EmployerController::class, 'toggleManager'])
        ->name('empleados.toggle-manager');
});


// Legacy route - kept for backward compatibility
// GET /empleadores/tareas-asignadas - View assigned tasks to employees
Route::middleware(['auth'])->get('/empleadores/tareas-asignadas', [DashboardController::class, 'verTareasEmpleados'])
    ->name('empleadores.tareas-asignadas');

// Legacy route - kept for backward compatibility
// GET /grafico-tareas - View tasks chart
Route::middleware(['auth'])->get('/grafico-tareas', [DashboardController::class, 'verTareasEmpleados']);
