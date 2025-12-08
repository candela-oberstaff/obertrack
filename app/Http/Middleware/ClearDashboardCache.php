<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ClearDashboardCache
{
    /**
     * Handle an incoming request.
     * Clear dashboard cache when tasks or work hours are modified
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Clear cache only for POST, PUT, PATCH, DELETE requests
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $user = auth()->user();
            
            if ($user) {
                // Clear all dashboard cache for this user
                $pattern = 'dashboard_' . $user->id . '_*';
                
                // Get all cache keys matching the pattern and delete them
                $keys = Cache::get('cache_keys_' . $user->id, []);
                foreach ($keys as $key) {
                    Cache::forget($key);
                }
                Cache::forget('cache_keys_' . $user->id);
            }
        }

        return $response;
    }
}
