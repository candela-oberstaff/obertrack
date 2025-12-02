<?php

/**
 * ============================================================================
 * MANAGER ROUTES
 * ============================================================================
 * 
 * Routes for manager-specific functionality including:
 * - Creating and managing tasks for team members
 * - Managing comments on manager-created tasks
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManagerTaskController;

Route::middleware(['auth'])->prefix('manager')->name('manager.')->group(function () {
    
    // Task Management
    // GET /manager/tasks - View all tasks created by the manager
    Route::get('/tasks', [ManagerTaskController::class, 'index'])
        ->name('tasks.index');
    
    // GET /manager/tasks/create - Show form to create a new task
    Route::get('/tasks/create', [ManagerTaskController::class, 'create'])
        ->name('tasks.create');
    
    // POST /manager/tasks - Store a new task
    Route::post('/tasks', [ManagerTaskController::class, 'store'])
        ->name('tasks.store');
    
    // GET /manager/tasks/{task}/edit - Show form to edit a task
    Route::get('/tasks/{task}/edit', [ManagerTaskController::class, 'edit'])
        ->name('tasks.edit');
    
    // PUT /manager/tasks/{task} - Update an existing task
    Route::put('/tasks/{task}', [ManagerTaskController::class, 'update'])
        ->name('tasks.update');
    
    // DELETE /manager/tasks/{task} - Delete a task
    Route::delete('/tasks/{task}', [ManagerTaskController::class, 'destroy'])
        ->name('tasks.destroy');
    
    // Comments Management
    // POST /manager/tasks/{task}/comment - Add a comment to a task
    Route::post('/tasks/{task}/comment', [ManagerTaskController::class, 'addComment'])
        ->name('tasks.comment');
    
    // PUT /manager/comments/{comment} - Update a comment
    Route::put('/comments/{comment}', [ManagerTaskController::class, 'updateComment'])
        ->name('tasks.comment.update');
    
    // DELETE /manager/comments/{comment} - Delete a comment
    Route::delete('/comments/{comment}', [ManagerTaskController::class, 'deleteComment'])
        ->name('tasks.comment.delete');
});
