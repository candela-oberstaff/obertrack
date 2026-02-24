<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{
    /**
     * Disconnect all Google services (Calendar, Forms, etc.)
     */
    public function disconnect()
    {
        $user = Auth::user();
        
        $user->update([
            'google_calendar_token' => null,
            'google_calendar_email' => null,
            'google_forms_token' => null,
            'google_forms_email' => null,
            'google_id' => null, // Optional: if we want a complete Google unlink
        ]);

        return redirect()->route('google-forms.manage')->with('success', 'Cuenta de Google desconectada exitosamente.');
    }
}
