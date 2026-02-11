<?php

namespace App\Services\AiTools;

use App\Models\User;

abstract class AbstractAiTool implements AiToolInterface
{
    protected string $name;
    protected string $description;
    
    public function name(): string 
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $this->name(),
                'description' => $this->description(),
                'parameters' => $this->parameters(),
            ]
        ];
    }

    abstract public function execute(array $input, User $user): mixed;
    abstract public function parameters(): array;
}
