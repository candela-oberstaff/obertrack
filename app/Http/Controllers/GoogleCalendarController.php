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
        if ($request->has('code')) {
            try {
                $this->calendarService->authenticate($request->code, Auth::user());
                return redirect()->route(Auth::user()->getDashboardRoute())
                    ->with('success', 'Google Calendar conectado exitosamente.');
            } catch (\Exception $e) {
                return redirect()->route(Auth::user()->getDashboardRoute())
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
