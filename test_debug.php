<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\AiTools\UserSearchTool;
use App\Services\AiTools\TaskQueryTool;

$tools = [
     (new UserSearchTool())->toArray(),
     (new TaskQueryTool())->toArray()
];

file_put_contents('tools_dump.json', json_encode($tools, JSON_PRETTY_PRINT));

echo "Tools dumped to tools_dump.json\n";
