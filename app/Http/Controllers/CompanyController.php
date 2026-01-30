<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompanyController extends Controller
{
    public function toggleManager(User $professional)
    {
        // Verify the authenticated user is the company of this professional
        if (auth()->user()->id !== $professional->empleador_id) {
            abort(403, 'No tienes permiso para modificar este profesional.');
        }

        $professional->is_manager = !$professional->is_manager;
        $professional->save();

        $status = $professional->is_manager ? 'promovido a Manager' : 'removido de Manager';
        
        return back()->with('success', "El profesional {$professional->name} ha sido {$status}.");
    }
}
