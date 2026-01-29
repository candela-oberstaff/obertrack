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

    // PATCH /tareas/{taskId}/status - Update task status (Kanban Drag & Drop)
    Route::patch('/tareas/{taskId}/status', [TaskController::class, 'updateStatus'])
        ->name('tareas.update-status');
    
    // DELETE /tareas/{taskId} - Delete a task
    Route::delete('/tareas/{taskId}', [TaskController::class, 'destroy'])
        ->name('tareas.destroy');
    
    // Task Completion
    // POST /tasks/{taskId}/toggle-completion - Toggle task completion status
    Route::post('/tasks/{taskId}/toggle-completion', [TaskController::class, 'toggleCompletion']);
    
    // POST /tasks/{task}/toggle-completion - Toggle task completion status (named route)
    Route::post('/tasks/{task}/toggle-completion', [TaskController::class, 'toggleCompletion'])
        ->name('tasks.toggle-completion');
    
    // File Attachments
    // GET /tasks/attachments/{attachment}/download - Download a task attachment
    Route::get('/tasks/attachments/{attachment}/download', [TaskController::class, 'downloadAttachment'])
        ->name('tasks.attachments.download');

    // DELETE /tasks/attachments/{attachment} - Delete a task attachment
    Route::delete('/tasks/attachments/{attachment}', [TaskController::class, 'deleteAttachment'])
        ->name('tasks.attachments.destroy');
    
    // Task Details
    Route::get('/tasks/{task}/details', [TaskController::class, 'getDetails'])
        ->name('tasks.details');

    // GET /tasks/{task} - Show specific task (JSON or View)
    Route::get('/tasks/{task}', [TaskController::class, 'show'])
        ->name('tasks.show');

    // Comments Management
    // GET /tasks/{taskId}/comments - Get all comments for a task
    Route::get('/tasks/{taskId}/comments', [CommentController::class, 'index'])
        ->name('comments.index');
    
    // POST /tasks/{task}/comments - Create a new comment (Alias for store, requires task_id in body or we can merge it)
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store'])
        ->name('tasks.comments.store');

    // PUT /tasks/comments/{id} - Update a comment
    Route::put('/tasks/comments/{id}', [CommentController::class, 'update'])
        ->name('tasks.comments.update');
        
    // DELETE /tasks/comments/{comment} - Delete a comment
    Route::delete('/tasks/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('tasks.comments.destroy');

    // Attachments
    Route::post('/tasks/{task}/attachments', [TaskController::class, 'uploadAttachment'])
        ->name('tasks.attachments.store');
    
    // Legacy / Generic Comment Routes (Keep for compatibility if used elsewhere)
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{id}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});
