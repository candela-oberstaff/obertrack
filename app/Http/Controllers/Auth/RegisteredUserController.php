<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\File;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // return view('auth.register');
        $empleadores = User::where('tipo_usuario', 'empleador')
                      ->pluck('name', 'id'); // Cambia 'name' a 'id' para el segundo argumento de pluck
        return view('auth.register', compact('empleadores'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
   
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|min:8',
            'tipo_usuario' => 'required|string|in:empleador,empleado',
            'empleado_por_id' => 'required_if:tipo_usuario,empleado|nullable|exists:users,id',
            'job_title' => 'nullable|string|max:100',
            // Company specific fields
            'company_name' => 'required_if:tipo_usuario,empleador|nullable|string|max:255',
            'related_contact' => 'required_if:tipo_usuario,empleador|nullable|string|max:255',
            'phone_number' => 'required_if:tipo_usuario,empleador|nullable|string|max:20',
            'country' => 'required_if:tipo_usuario,empleador|nullable|string|max:100',
        ]);



        $avatar = null;
        $files = File::files(public_path('avatars'));
        if (count($files) > 0) {
            $randomFile = $files[array_rand($files)];
            $avatar = $randomFile->getFilename();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo_usuario' => $request->tipo_usuario,
            'empleador_id' => $request->tipo_usuario === 'empleado' ? $request->empleado_por_id : null,
            'job_title' => $request->tipo_usuario === 'empleado' ? $request->job_title : null,
            'company_name' => $request->tipo_usuario === 'empleador' ? $request->company_name : null,
            'related_contact' => $request->tipo_usuario === 'empleador' ? $request->related_contact : null,
            'phone_number' => $request->phone_number, // Can be common
            'country' => $request->country,
            'avatar' => $avatar,
        ]);

        Auth::login($user);

        // Redirección basada en el rol
        if ($user->tipo_usuario === 'empleador') {
            return redirect()->route('empleador.dashboard');
        } elseif ($user->tipo_usuario === 'empleado') {
            return redirect()->route('empleados.tasks.index'); // O la ruta principal para empleados
        }

        return redirect()->intended('dashboard');
    }



}
