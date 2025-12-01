
<?php
namespace App\Providers;
use Illuminate\Support\Facades\Broadcast;

public function boot(): void
{
    Broadcast::routes();

    Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
        return (int) $user->id === (int) $id;
    });
}