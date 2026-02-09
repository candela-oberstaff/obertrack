<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

use App\Services\BrevoEmailService;

class CodeBasedPasswordResetController extends Controller
{
    public function __construct(
        private BrevoEmailService $brevoEmailService
    ) {}

    public function showLinkRequestForm()
    {
        return view('auth.forgot-password-code');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

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
             return back()->withErrors(['email' => 'Error al enviar el código. Inténtalo más tarde.']);
        }

        return redirect()->route('password.verify-code.form', ['email' => $request->email]);
    }

    public function showVerifyCodeForm(Request $request)
    {
        return view('auth.verify-code', ['email' => $request->email]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $cachedCode = Cache::get('password_reset_code_' . $request->email);

        if (!$cachedCode || $cachedCode !== $request->code) {
            return back()->withErrors(['code' => 'El código es incorrecto o ha expirado.']);
        }

        // Code is valid, allow password reset
        // We can use a signed URL or a temporary token to allow the reset step
        // For simplicity, we'll store a "verified" flag in cache for a short time
        Cache::put('password_reset_verified_' . $request->email, true, now()->addMinutes(15));
        
        return redirect()->route('password.reset.form', ['email' => $request->email]);
    }

    public function showResetForm(Request $request)
    {
        if (!Cache::has('password_reset_verified_' . $request->email)) {
            return redirect()->route('password.verify-code.form', ['email' => $request->email])
                ->withErrors(['code' => 'Debes verificar el código primero.']);
        }

        return view('auth.reset-password-code', ['email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (!Cache::has('password_reset_verified_' . $request->email)) {
            return back()->withErrors(['email' => 'Sesión de restablecimiento expirada.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Usuario no encontrado.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // Clear cache
        Cache::forget('password_reset_code_' . $request->email);
        Cache::forget('password_reset_verified_' . $request->email);

        return redirect()->route('login')->with('status', '¡Tu contraseña ha sido restablecida!');
    }
}
