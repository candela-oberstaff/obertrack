<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkHours;

class WorkHoursPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function approve(User $user, WorkHours $workHours)
{
    return $user->id === $workHours->user->empleador_id;
}

public function approveAll(User $user)
{
    return $user->tipo_usuario === 'empleador';
}
}
