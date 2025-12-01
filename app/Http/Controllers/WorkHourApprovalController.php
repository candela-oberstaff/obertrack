<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkHour;
use App\Models\UserSignature;
use App\Models\WorkHours;
use Carbon\Carbon;

class WorkHourApprovalController extends Controller
{
    public function approve(Request $request)
    {
        $user = auth()->user();
        $month = Carbon::parse($request->month);

        // Verificar si ya existe una firma para el usuario
        $signature = UserSignature::where('user_id', $user->id)->first();

        if (!$signature) {
            // Si no existe, crear una nueva firma
            $signature = new UserSignature();
            $signature->user_id = $user->id;
            $signature->signature = $request->signature;
            $signature->save();
        }

        // Aprobar todas las horas del mes para este usuario
        WorkHours::where('user_id', $user->id)
            ->whereYear('work_date', $month->year)
            ->whereMonth('work_date', $month->month)
            ->update([
       
                'approved_at' => now(),
                'approval_comment' => 'Aprobado por el Profesional'
            ]);

        return response()->json(['success' => true]);
    }
}