<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$employer = \App\Models\User::where('email', 'info@oberstaff.com')->first();
$manager = \App\Models\User::where('email', 'hector@oberstaff.com')->first();
$pro = \App\Models\User::where('email', 'arquidev@gmail.com')->first();

if (!$employer) {
    echo "Employer 'info@oberstaff.com' NOT FOUND. Cannot proceed." . PHP_EOL;
    exit(1);
}

echo "Employer Found: {$employer->name} (ID: {$employer->id})" . PHP_EOL;

if ($manager) {
    $manager->empleador_id = $employer->id;
    $manager->save();
    echo "UPDATED Manager 'hector@oberstaff.com' to have empleador_id = {$employer->id}" . PHP_EOL;
} else {
    echo "Manager NOT FOUND." . PHP_EOL;
}

if ($pro) {
    if ($pro->empleador_id != $employer->id) {
        $pro->empleador_id = $employer->id;
        $pro->save();
        echo "UPDATED Pro 'arquidev@gmail.com' to have empleador_id = {$employer->id}" . PHP_EOL;
    } else {
        echo "Pro already has correct empleador_id ({$pro->empleador_id})." . PHP_EOL;
    }
} else {
    echo "Pro NOT FOUND." . PHP_EOL;
}
