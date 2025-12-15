<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tasks.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        // Todos los usuarios autenticados pueden ver la lista de tareas
        return true;
    }

    /**
     * Determine whether the user can view the task.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    // public function view(User $user, Task $task)
    // {
    //     // El usuario puede ver la tarea si es el creador o si la tarea está asignada a él
    //     return $user->id === $task->created_by || $user->id === $task->visible_para;
    // }
    public function view(User $user, Task $task)
    {
        return $user->id === $task->created_by || $task->assignees->contains($user->id) || $user->isEmpleadorOrSuperAdmin();
    }

    /**
     * Determine whether the user can create tasks.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->is_manager || $user->isEmpleadorOrSuperAdmin();
    }

    /**
     * Determine whether the user can update the task.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function update(User $user, Task $task)
    {
        return $user->id === $task->created_by || $user->isEmpleadorOrSuperAdmin();
    }

    /**
     * Determine whether the user can delete the task.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function delete(User $user, Task $task)
    {
        return $user->id === $task->created_by || $user->isEmpleadorOrSuperAdmin();
    }

    /**
     * Determine whether the user can restore the task.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function restore(User $user, Task $task)
    {
        return $user->id === $task->created_by || $user->isEmpleadorOrSuperAdmin();
    }

    /**
     * Determine whether the user can permanently delete the task.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function forceDelete(User $user, Task $task)
    {
        return $user->id === $task->created_by || $user->isEmpleadorOrSuperAdmin();
    }


    /**
     * Determine whether the user can update the task status.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function updateStatus(User $user, Task $task)
    {
        return $user->id === $task->created_by || $task->assignees->contains($user->id) || $user->isEmpleadorOrSuperAdmin();
    }
}