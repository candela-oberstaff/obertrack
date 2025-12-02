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
use App\Http\Controllers\EmployeeTaskController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\TaskController;

Route::middleware(['auth'])->prefix('empleados')->name('empleados.')->group(function () {
    
    // Task Management
    // GET /empleados/tareas - View all tasks assigned to the employee
    Route::get('/tareas', [EmployeeTaskController::class, 'index'])
        ->name('tasks.index');
    
    // GET /empleados/tareas/{task} - View details of a specific task
    Route::get('/tareas/{task}', [EmployeeTaskController::class, 'show'])
        ->name('tasks.show');
    
    // POST /empleados/tareas/{task}/toggle-completion - Toggle task completion status
    Route::post('/tareas/{task}/toggle-completion', [EmployeeTaskController::class, 'toggleCompletion'])
        ->name('tasks.toggle-completion');
    
    // Comments Management
    // POST /empleados/tareas/{task}/comment - Add a comment to a task
    Route::post('/tareas/{task}/comment', [EmployeeTaskController::class, 'addComment'])
        ->name('tasks.comment');
    
    // PUT /empleados/tareas/comment/{comment} - Update a comment
    Route::put('/tareas/comment/{comment}', [EmployeeTaskController::class, 'updateComment'])
        ->name('tasks.comment.update');
    
    // DELETE /empleados/tareas/comment/{comment} - Delete a comment
    Route::delete('/tareas/comment/{comment}', [EmployeeTaskController::class, 'deleteComment'])
        ->name('tasks.comment.delete');
    
    // Legacy Routes
    // GET /empleados/editar-tareas - Edit tasks view (legacy)
    Route::get('/editar-tareas', [TaskController::class, 'index'])
        ->name('editar-tareas');
    
    // GET /empleados/crear-tarea - Show form to create a task (legacy)
    Route::get('/crear-tarea', [EmpleadoController::class, 'create'])
        ->name('crear-tarea');
});

// Employee Work Hours
// GET /empleado/registrar-horas - Show form to register work hours
Route::middleware(['auth'])->get('/empleado/registrar-horas', [EmpleadoController::class, 'registrarHoras'])
    ->name('empleado.registrar-horas');
