<?php

namespace App\Services;

use App\Models\User;
use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Services\GroqService;
use Illuminate\Support\Facades\Log;

class AiAgentService
{
    protected GroqService $groq;
    protected array $tools = [];

    public function __construct(GroqService $groq)
    {
        $this->groq = $groq;
        $this->registerTools();
    }

    /**
     * Register all available AI tools
     */
    protected function registerTools()
    {
        $this->tools = [
            new \App\Services\AiTools\UserSearchTool(),
            new \App\Services\AiTools\TaskQueryTool(),
            new \App\Services\AiTools\TaskCreateTool(),
            new \App\Services\AiTools\TaskUpdateTool(),
            new \App\Services\AiTools\TaskDeleteTool(),
            new \App\Services\AiTools\TaskCommentTool(),
            new \App\Services\AiTools\WorkLogTool(),
            new \App\Services\AiTools\WorkQueryTool(),
            new \App\Services\AiTools\RecoveryRequestTool(),
            new \App\Services\AiTools\RecoveryApprovalTool(),
            new \App\Services\AiTools\InboxMessageTool(),
        ];
    }

    /**
     * Main entry point to send a message to the agent
     */
    public function sendMessage(string $userMessage, AiConversation $conversation, User $user)
    {
        // 1. Build Message History (already includes the latest user message from DB)
        $messages = $this->buildContext($conversation, $user);
        
        // Note: We do NOT append $userMessage here because buildContext() already
        // loads it from the conversation history (it was saved by AiChat::sendMessage
        // before the streaming event was dispatched).

        // 3. Prepare Tools
        $formattedTools = array_map(fn($t) => $t->toArray(), $this->tools);

        // 4. Initial Call to LLM
        $response = $this->groq->chat($messages, $formattedTools);
        $assistantMessage = $response['choices'][0]['message'];

        // 5. Check for Tool Calls (The Agent Loop)
        // We limit the loop to 5 turns to prevent infinite loops
        $loops = 0;
        while (isset($assistantMessage['tool_calls']) && $loops < 5) {
            $loops++;
            
            // Append assistant's decision to call tools to history
            $messages[] = $assistantMessage;
            
            $toolCalls = $assistantMessage['tool_calls'];
            
            foreach ($toolCalls as $toolCall) {
                $functionName = $toolCall['function']['name'];
                $arguments = json_decode($toolCall['function']['arguments'], true);
                
                Log::info("Agent executing tool: $functionName", $arguments);

                $result = $this->executeTool($functionName, $arguments, $user);

                // Append tool result to history
                $messages[] = [
                    'tool_call_id' => $toolCall['id'],
                    'role' => 'tool',
                    'name' => $functionName,
                    'content' => json_encode($result)
                ];
            }

            // Call LLM again with the tool results
            $response = $this->groq->chat($messages, $formattedTools);
            $assistantMessage = $response['choices'][0]['message'];
        }

        return $assistantMessage['content'];
    }

    protected function executeTool(string $name, array $arguments, User $user)
    {
        foreach ($this->tools as $tool) {
            if ($tool->name() === $name) {
                try {
                    return $tool->execute($arguments, $user);
                } catch (\Exception $e) {
                    Log::error("Tool execution failed: " . $e->getMessage());
                    return "Error executing tool: " . $e->getMessage();
                }
            }
        }
        return "Tool not found: $name";
    }

    protected function buildContext(AiConversation $conversation, User $user)
    {
        // Start with the existing system prompt from config
        $systemPrompt = config('ai.system_prompt', 'Eres un asistente inteligente.');
        
        // Replace context placeholders
        $systemPrompt = str_replace(':user_name', $user->name, $systemPrompt);
        $systemPrompt = str_replace(':user_role', $user->tipo_usuario, $systemPrompt);
        
        // Add agent capabilities context
        $systemPrompt .= "\n\nCONTEXTO ADICIONAL:\n";
        $systemPrompt .= "- Fecha actual: " . now()->format('Y-m-d H:i') . "\n";
        $systemPrompt .= "- Usuario: {$user->name} (ID: {$user->id})\n";
        $systemPrompt .= "- Rol: {$user->tipo_usuario}\n";
        
        if ($user->empleador_id) {
            $systemPrompt .= "- Empresa ID: {$user->empleador_id}\n";
        }

        $systemPrompt .= "\nREGLAS CRÍTICAS PARA USO DE HERRAMIENTAS:\n";
        $systemPrompt .= "1. BUSCAR ANTES DE EDITAR/ELIMINAR: Para update_task, delete_task o add_task_comment, PRIMERO usa get_tasks para encontrar el task_id. Nunca asumas IDs.\n";
        $systemPrompt .= "2. DESAMBIGUACIÓN: Si get_tasks devuelve VARIAS tareas con nombres similares, NO elijas una al azar. PREGÚNTALE al usuario cuál quiere, mostrándole las opciones con su ID, fecha, asignados y estado. Ejemplo: '¿Cuál de estas tareas te refieres? 1) Tarea X (ID: 45, asignada a Juan, en proceso) 2) Tarea X (ID: 67, asignada a María, por hacer)'\n";
        $systemPrompt .= "3. CREAR ES SIEMPRE VÁLIDO: Cuando el usuario pide explícitamente CREAR una tarea nueva, créala. Es normal tener tareas con el mismo nombre.\n";
        $systemPrompt .= "4. NUNCA ASUMAS IDs: Los IDs de tareas y usuarios deben obtenerse de las herramientas, no inventados.\n";
        $systemPrompt .= "5. CONTEXTO DE CONVERSACIÓN: Si la conversación previa menciona una tarea que se acaba de crear (ej: 'La tarea X ha sido creada con ID 45'), usa ese ID directamente sin buscar de nuevo.\n";
        $systemPrompt .= "6. MÚLTIPLES ACCIONES: Si el usuario pide varias cosas (ej: 'cambia el estatus y agrega un comentario'), ejecuta TODAS las herramientas necesarias.\n";
        $systemPrompt .= "7. ESTADOS DE TAREA: Los estados válidos son: por_hacer, en_proceso, finalizado.\n";
        $systemPrompt .= "8. Responde SIEMPRE en Español.\n";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Load recent history (last 10 messages)
        // We might need to optimize this to not exceed context if history is huge
        $history = $conversation->messages()
            ->orderBy('created_at', 'desc') // Get latest first
            ->take(10)
            ->get()
            ->reverse(); // Put back in chronological order

        foreach ($history as $msg) {
            // We only load simple text messages for now to keep context clean
            // Ideally we should persist tool calls too, but for MVP let's stick to text
            if ($msg->content) {
                $messages[] = ['role' => $msg->role, 'content' => $msg->content];
            }
        }

        return $messages;
    }
}
