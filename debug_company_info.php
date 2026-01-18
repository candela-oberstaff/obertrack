<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::first(); // Assuming 'Hector' is the first
Auth::login($user);

echo "User: {$user->name} (ID: {$user->id})\n";
echo "My Company Name: {$user->company_name}\n";
echo "Empleador ID: {$user->empleador_id}\n";

if ($user->empleador) {
    echo "Empleador Name: " . $user->empleador->name . "\n";
    echo "Empleador Company: " . $user->empleador->company_name . "\n";
} else {
    echo "No empleador found.\n";
}
