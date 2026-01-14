<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;  
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    // public function edit(Request $request): View
    // {
    //     return view('profile.edit', [
    //         'user' => $request->user(),
    //     ]);
    // }


    public function toggleSuperAdmin(User $user)
{
    // Verificar si el usuario es un manager
    if (!$user->is_manager) {
        return back()->with('error', 'Solo los managers pueden ser promovidos a SuperAdmin.');
    }

    // Cambiar el estado de is_superadmin
    $newValue = !$user->is_superadmin;
    $user->update(['is_superadmin' => $newValue ? DB::raw('true') : DB::raw('false')]);

    $action = $user->is_superadmin ? 'promovido a' : 'degradado de';
    return back()->with('status', "El usuario ha sido {$action} SuperAdmin exitosamente.");
}



    public function edit(Request $request): View
    {
        $user = $request->user();
        $empleados = [];
        
        if ($user->tipo_usuario === 'empleador') {
            $empleados = $user->empleados;
        }

        return view('profile.edit', [
            'user' => $user,
            'empleados' => $empleados,
        ]);
    }

    public function promoverAManager(User $user)
    {
        if ($user->tipo_usuario === 'empleado') {
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $user->id)
                ->update(['is_manager' => \Illuminate\Support\Facades\DB::raw('true')]);

            $freshStatus = \Illuminate\Support\Facades\DB::table('users')->where('id', $user->id)->value('is_manager');
            $statusText = $freshStatus ? 'Manager' : 'Profesional';
            return redirect()->back()->with('success', "Usuario promovido a manager exitosamente. Estado actual en DB: {$statusText}");
        }
        return redirect()->back()->with('error', 'No se pudo promover al usuario a manager.');
    }

    public function degradarDeManager(User $user)
    {
        if ($user->tipo_usuario === 'empleado') {
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $user->id)
                ->update(['is_manager' => \Illuminate\Support\Facades\DB::raw('false')]);

            $freshStatus = \Illuminate\Support\Facades\DB::table('users')->where('id', $user->id)->value('is_manager');
            $statusText = $freshStatus ? 'Manager' : 'Profesional';
            return redirect()->back()->with('success', "Manager degradado exitosamente. Estado actual en DB: {$statusText}");
        }
        return redirect()->back()->with('error', 'No se pudo degradar al manager.');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($request->hasFile('avatar')) {
            // Delete old avatar if it's a file (not a URL)
            if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                $oldPath = public_path('avatars/' . $user->avatar);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('avatar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('avatars'), $filename);
            $user->avatar = $filename;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($user->tipo_usuario === 'empleado' && 
            !empty($user->phone_number) && 
            !empty($user->location) && 
            !empty($user->country) && 
            !empty($user->city)) {
            return Redirect::route('empleado.registrar-horas')->with('success', 'Perfil completado exitosamente. Ahora puedes registrar tus horas.');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }


    public function eliminarEmpleado(User $empleado)
    {
        try {
            DB::beginTransaction();
    
            Log::info("Intentando eliminar empleado ID: " . $empleado->id);
    
            // 1. Manejar empleados asociados
            $empleadosAsociados = $empleado->empleados;
            foreach ($empleadosAsociados as $empleadoAsociado) {
                $empleadoAsociado->empleador_id = null;
                $empleadoAsociado->save();
            }
            Log::info("Empleados desasociados: " . $empleadosAsociados->count());
    
            // 2. Manejar tareas creadas
            $tareasCreadas = $empleado->createdTasks;
            foreach ($tareasCreadas as $tarea) {
                // Puedes elegir eliminar las tareas o reasignarlas a otro usuario
                $tarea->delete(); // O $tarea->created_by = $otroUsuarioId; $tarea->save();
            }
            Log::info("Tareas creadas manejadas: " . $tareasCreadas->count());
    
            // 3. Manejar tareas asignadas
            $tareasAsignadas = $empleado->assignedTasks;
            foreach ($tareasAsignadas as $tarea) {
                // Puedes elegir eliminar las tareas o reasignarlas a otro usuario
                $tarea->visible_para = null; // O $tarea->visible_para = $otroUsuarioId;
                $tarea->save();
            }
            Log::info("Tareas asignadas manejadas: " . $tareasAsignadas->count());
    
            // 4. Manejar horas de trabajo
            $empleado->workHours()->delete();
            Log::info("Horas de trabajo eliminadas");
    
            // 5. Eliminar firma del usuario
            if ($empleado->signature) {
                $empleado->signature->delete();
                Log::info("Firma del usuario eliminada");
            }
    
            // 6. Finalmente, eliminar al usuario
            $userDeleted = $empleado->delete();
            Log::info("Usuario eliminado: " . ($userDeleted ? 'Sí' : 'No'));
    
            if (!$userDeleted) {
                throw new \Exception("No se pudo eliminar al empleado por razones desconocidas.");
            }
    
            DB::commit();
            return redirect()->back()->with('status', 'Empleado y sus datos asociados eliminados con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar empleado: " . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo eliminar al empleado. Error: ' . $e->getMessage());
        }
    }


    public function sendPasswordCode(Request $request)
    {
        $user = $request->user();
        $code = rand(100000, 999999);
        
        // Store code in cache for 15 minutes
        \Illuminate\Support\Facades\Cache::put('password_reset_code_' . $user->id, $code, now()->addMinutes(15));
        
        // Send email via Brevo Service
        try {
            $brevoService = app(\App\Services\BrevoEmailService::class);
            $sent = $brevoService->sendPasswordResetCode($user->email, $user->name, $code);

            if (!$sent) {
                // Determine if we should fail or just log (fail is better for user feedback)
                return response()->json(['message' => 'No se pudo enviar el correo de verificación due to Brevo error.'], 500);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('OTP Send Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error interno al enviar correo.'], 500);
        }
        
        return response()->json(['message' => 'Código enviado correctamente']);
    }

    public function updatePasswordWithCode(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = $request->user();
        $cachedCode = \Illuminate\Support\Facades\Cache::get('password_reset_code_' . $user->id);
        
        if (!$cachedCode || $cachedCode != $request->code) {
            return back()->withErrors(['code' => 'El código es inválido o ha expirado.']);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);
        
        // Clear the code
        \Illuminate\Support\Facades\Cache::forget('password_reset_code_' . $user->id);

        return back()->with('status', 'password-updated');
    }
}
