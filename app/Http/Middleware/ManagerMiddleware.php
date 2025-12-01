<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ManagerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user() && (auth()->user()->tipo_usuario === 'empleador' || auth()->user()->is_manager)) {
            return $next($request);
        }
        
        return redirect('/home')->with('error', 'No tienes permiso para acceder a esta página.');
    }



    
}
