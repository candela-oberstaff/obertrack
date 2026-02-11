<?php

use App\Services\GroqService;
use App\Services\AiTools\UserSearchTool;
use App\Services\AiTools\TaskQueryTool;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$groq = new GroqService();

echo "Testing Groq Connection...\n";

// 1. Simple Test
try {
    $response = $groq->chat([
        ['role' => 'user', 'content' => 'Hello']
    ]);
    echo "Simple Chat: Success\n";
} catch (\Exception $e) {
    echo "Simple Chat: Failed - " . $e->getMessage() . "\n";
}

// 2. Tool Test
echo "\nTesting Tool Definition...\n";
$tools = [
     (new UserSearchTool())->toArray(),
     (new TaskQueryTool())->toArray()
];

try {
    $response = $groq->chat([
        ['role' => 'user', 'content' => 'Find user named Juan']
    ], $tools);
    echo "Tool Chat: Success\n";
} catch (\Exception $e) {
    echo "Tool Chat: Failed - " . $e->getMessage() . "\n";
}
