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
    }

}
