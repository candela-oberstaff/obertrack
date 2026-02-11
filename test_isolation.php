<?php

use App\Services\GroqService;
use App\Services\AiTools\UserSearchTool;
use App\Services\AiTools\TaskQueryTool;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$groq = new GroqService();

echo "1. Testing UserSearchTool ONLY...\n";
try {
    $groq->chat([['role' => 'user', 'content' => 'Find Juan']], [(new UserSearchTool())->toArray()]);
    echo "UserSearchTool: Success\n";
} catch (\Exception $e) {
    echo "UserSearchTool: Failed (400)\n";
}

echo "\n2. Testing TaskQueryTool ONLY...\n";
try {
    $groq->chat([['role' => 'user', 'content' => 'Show pending tasks']], [(new TaskQueryTool())->toArray()]);
    echo "TaskQueryTool: Success\n";
} catch (\Exception $e) {
    echo "TaskQueryTool: Failed (400)\n";
}
