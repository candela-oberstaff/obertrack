<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; 
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    protected $policies = [
        Comment::class => CommentPolicy::class,
        
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Establecer el locale en español
        Carbon::setLocale('es');
        Paginator::defaultView('pagination::tailwind');
        
        // Force HTTPS and correct root URL in production (for apps behind proxies like Coolify)
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            \Illuminate\Support\Facades\URL::forceRootUrl('https://obertrack.com');
            
            // Explicitly set the Google redirect URI to use HTTPS to avoid redirect_uri_mismatch
            config(['services.google.redirect' => 'https://obertrack.com/auth/google/callback']);
        }
    }

}
