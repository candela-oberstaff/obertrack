<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$task = App\Models\Task::find(41); // Using the ID seen in screenshot/debug

if (!$task) {
    echo "Task 41 not found. Using first task.\n";
    $task = App\Models\Task::first();
}

echo "Current State: Status={$task->status}, Completed=" . ($task->completed ? 'true' : 'false') . "\n";

// Attempt 1: Standard Boolean Update (Laravel default)
echo "Attempting standard update to completed=false...\n";
$task->update([
    'status' => App\Models\Task::STATUS_IN_PROGRESS,
    'completed' => false
]);

$task->refresh();
echo "After Standard Update: Status={$task->status}, Completed=" . ($task->completed ? 'true' : 'false') . "\n";

// Reset for next test if needed
$task->update(['completed' => true, 'status' => App\Models\Task::STATUS_COMPLETED]);
echo "Reset to Completed.\n";

// Attempt 2: DB::raw Update (Current Implementation)
echo "Attempting DB::raw('false') update...\n";
$task->update([
    'status' => App\Models\Task::STATUS_IN_PROGRESS,
    'completed' => \Illuminate\Support\Facades\DB::raw('false')
]);

$task->refresh();
echo "After DB::raw Update: Status={$task->status}, Completed=" . ($task->completed ? 'TRUE' : 'FALSE') . "\n";
