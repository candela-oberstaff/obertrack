<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get ALL tasks for the current user
$tasks = App\Models\Task::all();

foreach ($tasks as $task) {
    echo "ID: " . $task->id . " | Title: " . substr($task->title, 0, 20) . " | Status: " . $task->status . " | Completed: " . ($task->completed ? 'true' : 'false') . PHP_EOL;
}
