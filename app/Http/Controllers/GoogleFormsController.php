<?php

namespace App\Http\Controllers;

use App\Services\GoogleFormsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoogleFormsController extends Controller
{
    protected $formsService;

    public function __construct(GoogleFormsService $formsService)
    {
        $this->formsService = $formsService;
    }

    public function connect()
    {
        return redirect()->away($this->formsService->getAuthUrl());
    }

    public function callback(Request $request)
    {
        if ($request->has('code')) {
            try {
                $user = Auth::user();
                $this->formsService->authenticate($request->code, $user);
                
                return redirect()->route('google-forms.manage')
                    ->with('success', 'Google Forms conectado exitosamente.');
            } catch (\Exception $e) {
                return redirect()->route('dashboard')
                    ->with('error', 'Error al conectar Google Forms: ' . $e->getMessage());
            }
        }

        return redirect()->route('dashboard')
            ->with('error', 'No se recibió el código de autorización.');
    }

    public function disconnect()
    {
        Auth::user()->update([
            'google_forms_token' => null,
            'google_forms_email' => null,
        ]);

        return back()->with('success', 'Google Forms desconectado.');
    }
}
