<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tasks = App\Models\Task::all();
$mismatchCount = 0;

echo "Checking for Tasks with Mismatched Status/Completed flags...\n";
echo "------------------------------------------------------------\n";

foreach ($tasks as $task) {
    $isCompletedFn = $task->completed;
    $isStatusFinished = $task->status === App\Models\Task::STATUS_COMPLETED; // 'finalizado'
    
    // Mismatch conditions:
    // 1. Completed=true but Status != 'finalizado'
    // 2. Completed=false but Status == 'finalizado'
    
    if ($isCompletedFn && !$isStatusFinished) {
        echo "[MISMATCH] ID: {$task->id} | Title: {$task->title} | Completed: TRUE | Status: '{$task->status}' (Should be 'finalizado')\n";
        $mismatchCount++;
    } elseif (!$isCompletedFn && $isStatusFinished) {
         echo "[MISMATCH] ID: {$task->id} | Title: {$task->title} | Completed: FALSE | Status: 'finalizado' (Should be completed=true)\n";
         $mismatchCount++;
    }
}

if ($mismatchCount === 0) {
    echo "No mismatches found.\n";
} else {
    echo "------------------------------------------------------------\n";
    echo "Found {$mismatchCount} mismatches.\n";
}
