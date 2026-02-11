<?php

namespace App\Services\AiTools;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserSearchTool extends AbstractAiTool
{
    protected string $name = 'search_users';
    protected string $description = 'Search for users/colleagues in the organization by name to get their IDs. Useful when needing to assign tasks or send messages.';

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => [
                    'type' => 'string',
                    'description' => 'Name or partial name to search for (e.g. "Juan", "Maria")'
                ]
            ],
            'required' => ['query']
        ];
    }

    public function execute(array $input, User $user): mixed
    {
        $query = $input['query'];
        
        $usersQuery = User::query();

        // Security Scoping
        if ($user->is_superadmin) {
            // No strict filter
        } elseif ($user->tipo_usuario === 'empleador') {
            // Company sees their employees
            $usersQuery->where('empleador_id', $user->id);
        } else {
            // Employee sees colleagues in same company
            $usersQuery->where('empleador_id', $user->empleador_id);
        }

        $results = $usersQuery->where('name', 'ilike', '%' . $query . '%')
            ->select('id', 'name', 'email', 'job_title')
            ->limit(5)
            ->get();

        if ($results->isEmpty()) {
            return "No users found matching '$query'.";
        }

        return $results->toArray();
    }
}
