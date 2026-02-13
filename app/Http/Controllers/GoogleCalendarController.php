<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleCalendarController extends Controller
{
    protected $calendarService;

    public function __construct(GoogleCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function connect()
    {
        return redirect()->away($this->calendarService->getAuthUrl());
    }

    public function callback(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('Google Calendar Callback hit.', [
            'has_code' => $request->has('code'),
            'user_id' => Auth::id(),
            'session_has_user' => Auth::check(),
            'all_inputs' => $request->all()
        ]);

        if ($request->has('code')) {
            try {
                $user = Auth::user();
                if (!$user) {
                    \Illuminate\Support\Facades\Log::error('Callback reached but user is not authenticated in this session.');
                    return redirect()->route('login')->with('error', 'Sesión perdida durante la conexión con Google. Por favor, intenta de nuevo.');
                }

                $this->calendarService->authenticate($request->code, $user);
                \Illuminate\Support\Facades\Log::info('Google Calendar Authentication successful for user ' . $user->id);
                return redirect()->route($user->getDashboardRoute())
                    ->with('success', 'Google Calendar conectado exitosamente.');
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Google Calendar Callback Error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->route(Auth::check() ? Auth::user()->getDashboardRoute() : 'login')
                    ->with('error', 'Error al conectar Google Calendar: ' . $e->getMessage());
            }
        }

        return redirect()->route(Auth::user()->getDashboardRoute())
            ->with('error', 'No se recibió el código de autorización de Google.');
    }

    public function disconnect()
    {
        Auth::user()->update([
            'google_calendar_token' => null,
            'google_calendar_email' => null,
        ]);

        return back()->with('success', 'Google Calendar desconectado.');
    }
}
