<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TourController extends Controller
{
    public function complete(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->tour_completed_at = now();
            $user->save();
        }

        return response()->json(['success' => true]);
    }
}
