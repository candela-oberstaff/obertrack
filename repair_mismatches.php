<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tasks = App\Models\Task::all();
$repairedCount = 0;

echo "Repairing Tasks with Mismatched Status/Completed flags...\n";
echo "------------------------------------------------------------\n";

foreach ($tasks as $task) {
    $isCompletedFn = $task->completed;
    $isStatusFinished = $task->status === App\Models\Task::STATUS_COMPLETED;
    
    // Logic: Trust 'completed' boolean as the primary source of truth for "Finished" state?
    // Or trust 'status'?
    // Given the bugs seen (counter correct, dropdown wrong), 'completed' seems to be the intended source of truth for "Done".
    
    if ($isCompletedFn && !$isStatusFinished) {
        $task->status = App\Models\Task::STATUS_COMPLETED;
        $task->save();
        echo "[REPAIRED] ID: {$task->id} | Set Status to 'finalizado' (was '{$task->getOriginal('status')}') because Completed=TRUE\n";
        $repairedCount++;
    } elseif (!$isCompletedFn && $isStatusFinished) {
        // If status is 'finalizado' but completed=false, we should probably set completed=true to match status.
        $task->completed = true;
        $task->save();
        echo "[REPAIRED] ID: {$task->id} | Set Completed to TRUE because Status='finalizado'\n";
        $repairedCount++;
    }
}

echo "------------------------------------------------------------\n";
echo "Repaired {$repairedCount} tasks.\n";
