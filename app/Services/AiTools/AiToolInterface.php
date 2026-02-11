<?php

namespace App\Services\AiTools;

use App\Models\User;

interface AiToolInterface
{
    /**
     * identifying name of the tool (e.g. 'create_task')
     */
    public function name(): string;

    /**
     * Description for the LLM explaining what this tool does
     */
    public function description(): string;

    /**
     * JSON Schema for the parameters
     */
    public function parameters(): array;

    /**
     * Execute the tool logic
     * @param array $input The arguments provided by the LLM
     * @param User $user The user invoking the tool (for authorization)
     * @return mixed string or array result
     */
    public function execute(array $input, User $user): mixed;
}
