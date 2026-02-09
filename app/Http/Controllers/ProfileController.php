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
    public function __construct(
        private \App\Services\ProfessionalDataService $professionalDataService
    ) {}

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
        $profesionales = $this->professionalDataService->getProfessionalsForUser($user);

        return view('profile.edit', [
            'user' => $user,
            'profesionales' => $profesionales,
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
            
            // Use Storage facade for better permission handling
            // This stores in storage/app/public/avatars
            \Illuminate\Support\Facades\Storage::disk('public')->putFileAs('avatars', $file, $filename);
            
            $user->avatar = $filename;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($user->tipo_usuario === 'empleado') {
            return Redirect::route('profesional.registrar-horas')->with('success', 'Perfil actualizado exitosamente.');
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


    public function eliminarProfesional(User $usuario)
    {
        try {
            DB::beginTransaction();
    
            Log::info("Intentando eliminar usuario ID: " . $usuario->id);
    
            // 1. Manejar profesionales asociados
            $profesionalesAsociados = $usuario->profesionales;
            foreach ($profesionalesAsociados as $profesionalAsociado) {
                $profesionalAsociado->empleador_id = null;
                $profesionalAsociado->save();
            }
            Log::info("Profesionales desasociados: " . $profesionalesAsociados->count());
    
            // 2. Manejar tareas creadas
            $tareasCreadas = $usuario->createdTasks;
            foreach ($tareasCreadas as $tarea) {
                // Puedes elegir eliminar las tareas o reasignarlas a otro usuario
                $tarea->delete(); // O $tarea->created_by = $otroUsuarioId; $tarea->save();
            }
            Log::info("Tareas creadas manejadas: " . $tareasCreadas->count());
    
            // 3. Manejar tareas asignadas
            $tareasAsignadas = $usuario->assignedTasks;
            foreach ($tareasAsignadas as $tarea) {
                // Puedes elegir elegir eliminar las tareas o reasignarlas a otro usuario
                $tarea->visible_para = null; // O $tarea->visible_para = $otroUsuarioId;
                $tarea->save();
            }
            Log::info("Tareas asignadas manejadas: " . $tareasAsignadas->count());
    
            // 4. Manejar horas de trabajo
            $usuario->workHours()->delete();
            Log::info("Horas de trabajo eliminadas");
    
            // 5. Eliminar firma del usuario
            if ($usuario->signature) {
                $usuario->signature->delete();
                Log::info("Firma del usuario eliminada");
            }
    
            // 6. Finalmente, eliminar al usuario
            $userDeleted = $usuario->delete();
            Log::info("Usuario eliminado: " . ($userDeleted ? 'Sí' : 'No'));
    
            if (!$userDeleted) {
                throw new \Exception("No se pudo eliminar al usuario por razones desconocidas.");
            }
    
            DB::commit();
            return redirect()->back()->with('status', 'Profesional y sus datos asociados eliminados con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error al eliminar profesional: " . $e->getMessage());
            return redirect()->back()->with('error', 'No se pudo eliminar al profesional. Error: ' . $e->getMessage());
        }
    }


    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $request->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
