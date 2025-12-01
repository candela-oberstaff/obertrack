<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkHoursController;
use App\Http\Controllers\WorkHourApprovalController;
use App\Http\Controllers\TaskController; // Asegúrate de importar TaskController
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EmployerTaskController;
use App\Http\Controllers\ManagerTaskController;
use App\Http\Controllers\EmployeeTaskController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// FORCE LOGIN FOR TESTING - TEMPORARY
if (app()->environment('local')) {
    try {
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'tipo_usuario' => 'empleador',
            ]);
        }
        Auth::login($user);
    } catch (\Exception $e) {
        // Ignore database errors if migration hasn't run
    }
}


Route::get('/', function () {
    return view('welcome');
});

// MIDDLEWARE TEMPORALMENTE DESACTIVADO
Route::/*middleware('auth')->*/get('/empleadores', [ProfileController::class, 'obtenerEmpleadores']);


// Route::get('/tareas', [TaskController::class, 'index'])->name('tareas.index');
Route::get('/empleados/editar-tareas', [TaskController::class, 'index'])->name('tareas.edit');
Route::post('/tareas', [TaskController::class, 'store'])->name('tareas.store');
Route::post('/tasks/{taskId}/toggle-completion', [TaskController::class, 'toggleCompletion']);

Route::put('/tareas/{taskId}', [TaskController::class, 'update'])->name('tareas.update');
Route::delete('/tareas/{taskId}', [TaskController::class, 'destroy'])->name('tareas.destroy');


Route::get('/empleadores/tareas-asignadas', [DashboardController::class, 'verTareasEmpleados'])/*->middleware(['auth'])*/->name('empleadores.tareas-asignadas');
Route::get('/grafico-tareas', [DashboardController::class, 'verTareasEmpleados']);


Route::post('/empleador/crear-tarea', [DashboardController::class, 'crearTareaParaEmpleado'])->name('empleador.crear-tarea');



Route::group([], function () { // middleware(['auth']) temporarily disabled
    // Rutas para tareas de empleador

    Route::get('/empleador/tareas', [DashboardController::class, 'verTareasEmpleados'])->name('empleador.tareas.index');
    Route::get('/empleador/tareas/crear', [TaskController::class, 'createForEmployee'])->name('empleador.tareas.create');
    Route::post('/empleador/tareas', [TaskController::class, 'storeForEmployee'])->name('empleador.tareas.store');
    Route::get('/empleador/tareas/{task}/editar', [TaskController::class, 'edit'])->name('empleador.tareas.edit');
    Route::put('/empleador/tareas/{task}', [TaskController::class, 'update'])->name('empleador.tareas.update');
    Route::delete('/empleador/tareas/{task}', [TaskController::class, 'destroy'])->name('empleador.tareas.destroy');



    // Rutas para toggle completion y comentarios del empleador
    Route::post('/empleador/tareas/{taskId}/toggle-completion', [TaskController::class, 'toggleEmployerTaskCompletion'])
        ->name('empleador.tareas.toggle-completion');
    Route::post('/empleador/comments', [CommentController::class, 'storeEmployerComment'])->name('empleador.comments.store');
    Route::put('/empleador/comments/{id}', [CommentController::class, 'updateEmployerComment'])->name('empleador.comments.update');
    Route::delete('/empleador/comments/{id}', [CommentController::class, 'destroyEmployerComment'])->name('empleador.comments.destroy');



Route::post('/empleador/tareas/{taskId}/comments', [EmployerTaskController::class, 'addComment'])->name('empleador.tareas.comments.add');
Route::put('/empleador/tareas/{taskId}/comments/{commentId}', [EmployerTaskController::class, 'updateComment'])->name('empleador.tareas.comments.update');
Route::delete('/empleador/tareas/{taskId}/comments/{commentId}', [EmployerTaskController::class, 'deleteComment'])->name('empleador.tareas.comments.delete');

});




//MANAGERS - MIDDLEWARE TEMPORALMENTE DESACTIVADO
Route::group([], function () { // middleware(['auth']) temporarily disabled
    Route::get('/manager/tasks', [ManagerTaskController::class, 'index'])->name('manager.tasks.index');
    Route::get('/manager/tasks/create', [ManagerTaskController::class, 'create'])->name('manager.tasks.create');
    Route::post('/manager/tasks', [ManagerTaskController::class, 'store'])->name('manager.tasks.store');


      // Añade estas nuevas rutas
      Route::get('/manager/tasks/{task}/edit', [ManagerTaskController::class, 'edit'])->name('manager.tasks.edit');
      Route::put('/manager/tasks/{task}', [ManagerTaskController::class, 'update'])->name('manager.tasks.update');
      Route::delete('/manager/tasks/{task}', [ManagerTaskController::class, 'destroy'])->name('manager.tasks.destroy');




      Route::post('/manager/tasks/{task}/comment', [ManagerTaskController::class, 'addComment'])->name('manager.tasks.comment');
      Route::put('/manager/comments/{comment}', [ManagerTaskController::class, 'updateComment'])->name('manager.tasks.comment.update');
      Route::delete('/manager/comments/{comment}', [ManagerTaskController::class, 'deleteComment'])->name('manager.tasks.comment.delete');
});



//PARA TAREAS CREADAS POR LOS MANAGERS MANEJA LA VISTA DE ESA TAREA DESDE EL EMPLEADO COMUN - MIDDLEWARE TEMPORALMENTE DESACTIVADO
Route::group([], function () { // middleware(['auth']) temporarily disabled
    Route::get('/empleados/tareas', [EmployeeTaskController::class, 'index'])->name('empleados.tasks.index');
    Route::get('/empleados/tareas/{task}', [EmployeeTaskController::class, 'show'])->name('empleados.tasks.show');
    Route::post('/empleados/tareas/{task}/comment', [EmployeeTaskController::class, 'addComment'])->name('empleados.tasks.comment');
    Route::put('/empleados/tareas/comment/{comment}', [EmployeeTaskController::class, 'updateComment'])->name('empleados.tasks.comment.update');
    Route::delete('/empleados/tareas/comment/{comment}', [EmployeeTaskController::class, 'deleteComment'])->name('empleados.tasks.comment.delete');
    Route::post('/empleados/tareas/{task}/toggle-completion', [EmployeeTaskController::class, 'toggleCompletion'])->name('empleados.tasks.toggle-completion');
});



// WORK HOURS - MIDDLEWARE TEMPORALMENTE DESACTIVADO
Route::group([], function () { // middleware(['auth']) temporarily disabled
    Route::get('/empleado/registrar-horas', [EmpleadoController::class, 'registrarHoras'])->name('empleado.registrar-horas');
    Route::post('/work-hours', [WorkHoursController::class, 'store'])->name('work-hours.store');
    Route::post('/work-hours/approve-week', [WorkHoursController::class, 'approveWeek'])->name('work-hours.approve-week');
    Route::get('/empleador/dashboard', [DashboardController::class, 'empleadorDashboard'])->name('empleador.dashboard');
    Route::post('/work-hours/approve-week-with-comment', [WorkHoursController::class, 'approveWeekWithComment'])->name('work-hours.approve-week-with-comment');
    Route::post('/work-hours/approve-month', [WorkHoursController::class, 'approveMonth'])->name('work-hours.approve-month');
    Route::post('/work-hours/approve', [WorkHourApprovalController::class, 'approve'])->name('work-hours.approve');
    Route::get('/work-hours/download-monthly-report/{month}', [WorkHoursController::class, 'downloadMonthlyReport'])->name('work-hours.download-monthly-report');
});



// SUPERADMIN - MIDDLEWARE TEMPORALMENTE DESACTIVADO
Route::group([], function () { // middleware(['auth']) temporarily disabled
    Route::put('/profile/{user}/toggle-superadmin', [ProfileController::class, 'toggleSuperAdmin'])->name('profile.toggle-superadmin');
});


// COMMENTS - MIDDLEWARE TEMPORALMENTE DESACTIVADO
Route::group([], function () { // middleware(['auth']) temporarily disabled
    // ... otras rutas ...

    // Rutas para comentarios
    Route::get('/tasks/{taskId}/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{id}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Ruta para toggle completion
    Route::post('/tasks/{task}/toggle-completion', [TaskController::class, 'toggleCompletion'])->name('tasks.toggle-completion');
});
    


// Route::get('/tareas/{taskId}', [TaskController::class, 'show'])->name('tareas.show'); // Ruta para mostrar detalles de una tarea
// Route::post('/tareas/{taskId}/comentar', [TaskController::class, 'addComment'])->name('tareas.addComment'); // Ruta para agregar un comentario a una tarea
// Route::get('/tareas/{taskId}/comentarios', [TaskController::class, 'showComments'])->name('tareas.showComments'); // Ruta para mostrar los comentarios de una tarea
// // Route::get('/tareas/{taskId}/comentarios/{commentId}/editar', [TaskController::class, 'updateComment'])->name('tareas.comment.edit');
// Route::put('/tareas/{taskId}/comentarios/{commentId}', [TaskController::class, 'updateComment'])->name('tareas.comment.update');
// Route::delete('/tareas/{taskId}/comentarios/{commentId}', [TaskController::class, 'deleteComment'])->name('tareas.comment.delete');


Route::get('/dashboard', function () {
    $user = Auth::user();
    $nombreUsuario = $user? $user->name : 'Invitado';
    return view('dashboard', ['nombreUsuario' => $nombreUsuario]);
})/*->middleware(['auth', 'verified'])*/->name('dashboard');


// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// PROFILE - MIDDLEWARE TEMPORALMENTE DESACTIVADO
Route::group([], function () { // middleware('auth') temporarily disabled
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/empleados/crear-tarea', [EmpleadoController::class, 'create'])->name('empleados.crear-tarea');



    Route::put('/profile/{user}/promover-manager', [ProfileController::class, 'promoverAManager'])->name('profile.promover-manager');
    Route::put('/profile/{user}/degradar-manager', [ProfileController::class, 'degradarDeManager'])->name('profile.degradar-manager');

    Route::delete('/profile/eliminar-empleado/{empleado}', [ProfileController::class, 'eliminarEmpleado'])->name('profile.eliminar-empleado');
});

require __DIR__.'/auth.php';
