<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

use App\Services\BrevoEmailService;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PasswordResetLinkController extends Controller
{
    public function __construct(
        private BrevoEmailService $brevoEmailService
    ) {}
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password-code');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Return success even if user doesn't exist to prevent email enumeration
            return redirect()->route('password.verify-code.form', ['email' => $request->email]);
        }

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store in cache for 15 minutes
        Cache::put('password_reset_code_' . $user->email, $code, now()->addMinutes(15));

        $sent = $this->brevoEmailService->sendPasswordResetCode($user->email, $user->name, $code);

        if (!$sent) {
             return back()->withInput($request->only('email'))
                         ->withErrors(['email' => 'Error al enviar el código. Inténtalo más tarde.']);
        }

        return redirect()->route('password.verify-code.form', ['email' => $request->email]);
    }
}
