<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::first();
Auth::login($user);

echo "Testing Dashboard Query for user: {$user->name} (ID: {$user->id})\n";

try {
    // Replicating the exact query from dashboard-professional.blade.php
    $nextDeadlineTask = auth()->user()->assignedTasks()
        ->whereRaw('tasks.completed IS FALSE')
        ->whereNotNull('tasks.end_date')
        ->where('tasks.end_date', '>=', now()->startOfDay())
        ->orderBy('tasks.end_date', 'asc')
        ->first();

    echo "Query Successful.\n";
    if ($nextDeadlineTask) {
        echo "Found task: {$nextDeadlineTask->title}\n";
    } else {
        echo "No next deadline task found.\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
