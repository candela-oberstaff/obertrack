<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::first();
Auth::login($user);

echo "Attempting to render dashboard view...\n";

try {
    $html = view('dashboard-professional')->render();
    echo "View Rendered Successfully (Length: " . strlen($html) . ")\n";
} catch (\Exception $e) {
    echo "RENDER ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
