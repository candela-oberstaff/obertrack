<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmployerController extends Controller
{
    public function toggleManager(User $employee)
    {
        // Verify the authenticated user is the employer of this employee
        if (auth()->user()->id !== $employee->empleador_id) {
            abort(403, 'No tienes permiso para modificar este empleado.');
        }

        $employee->is_manager = !$employee->is_manager;
        $employee->save();

        $status = $employee->is_manager ? 'promovido a Manager' : 'removido de Manager';
        
        return back()->with('success', "El empleado {$employee->name} ha sido {$status}.");
    }
}
