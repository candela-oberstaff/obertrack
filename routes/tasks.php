<?php

/**
 * ============================================================================
 * GENERAL TASK ROUTES
 * ============================================================================
 * 
 * Routes for general task management and comments that don't fit
 * into specific user role categories (employer/manager/employee)
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;

Route::middleware(['auth'])->group(function () {
    
    // Task Management
    // POST /tareas - Create a new task
    Route::post('/tareas', [TaskController::class, 'store'])
        ->name('tareas.store');
    
    // PUT /tareas/{taskId} - Update a task
    Route::put('/tareas/{taskId}', [TaskController::class, 'update'])
        ->name('tareas.update');
    
    // DELETE /tareas/{taskId} - Delete a task
    Route::delete('/tareas/{taskId}', [TaskController::class, 'destroy'])
        ->name('tareas.destroy');
    
    // Task Completion
    // POST /tasks/{taskId}/toggle-completion - Toggle task completion status
    Route::post('/tasks/{taskId}/toggle-completion', [TaskController::class, 'toggleCompletion']);
    
    // POST /tasks/{task}/toggle-completion - Toggle task completion status (named route)
    Route::post('/tasks/{task}/toggle-completion', [TaskController::class, 'toggleCompletion'])
        ->name('tasks.toggle-completion');
    
    // Comments Management
    // GET /tasks/{taskId}/comments - Get all comments for a task
    Route::get('/tasks/{taskId}/comments', [CommentController::class, 'index'])
        ->name('comments.index');
    
    // POST /comments - Create a new comment
    Route::post('/comments', [CommentController::class, 'store'])
        ->name('comments.store');
    
    // PUT /comments/{id} - Update a comment
    Route::put('/comments/{id}', [CommentController::class, 'update'])
        ->name('comments.update');
    
    // DELETE /comments/{comment} - Delete a comment
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');
});
