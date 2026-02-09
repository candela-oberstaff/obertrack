<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\OllamaService;
use App\Models\AiConversation;
use App\Models\AiMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiChat extends Component
{
    public string $userMessage = '';
    public array $messages = [];
    public bool $isTyping = false;
    
    // History Management
    public $conversations = [];
    public $currentConversationId = null;

    public function mount()
    {
        $this->loadConversations();
        
        // Optionally load most recent conversation, or start fresh
        // For now, let's start fresh unless instructed otherwise
        // if (count($this->conversations) > 0) {
        //    $this->loadConversation($this->conversations[0]->id);
        // } else {
            $this->newChat();
        // }
    }

    public function loadConversations()
    {
        $this->conversations = AiConversation::where('user_id', Auth::id())
            ->orderBy('updated_at', 'desc')
            ->take(50)
            ->get();
    }

    public function newChat()
    {
        $this->currentConversationId = null;
        $this->messages = [];
        // Initial greeting for UI only, not saved to DB until user interacts
        $this->messages[] = [
            'role' => 'assistant',
            'content' => '¡Hola! Soy tu asistente de IA. ¿En qué puedo ayudarte hoy?'
        ];
        $this->isTyping = false;
    }

    public function loadConversation($id)
    {
        $conversation = AiConversation::where('user_id', Auth::id())->find($id);
        
        if ($conversation) {
            $this->currentConversationId = $conversation->id;
            // Load messages from DB
            $this->messages = $conversation->messages()
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($msg) {
                    return [
                        'role' => $msg->role,
                        'content' => $msg->content
                    ];
                })
                ->toArray();
        }
    }

    public function deleteConversation($id)
    {
        $conversation = AiConversation::where('user_id', Auth::id())->find($id);
        if ($conversation) {
            $conversation->delete();
            $this->loadConversations();
            
            if ($this->currentConversationId == $id) {
                $this->newChat();
            }
        }
    }

    public function sendMessage(OllamaService $ollama)
    {
        if (empty(trim($this->userMessage))) {
            return;
        }

        $userText = $this->userMessage;
        $this->userMessage = ''; // Clear input immediately
        
        // 1. Create Conversation if not exists
        if (!$this->currentConversationId) {
            $conversation = AiConversation::create([
                'user_id' => Auth::id(),
                'title' => Str::limit($userText, 30, '...')
            ]);
            $this->currentConversationId = $conversation->id;
            $this->loadConversations(); // Refresh list to show new item
            
            // Remove the initial "fake" greeting if we are saving true history
            // Or keep it? Let's clear messages and reload from what we save to be consistent.
            $this->messages = []; 
        } else {
             $conversation = AiConversation::find($this->currentConversationId);
             $conversation->touch(); // Update timestamp
        }

        // 2. Save User Message to DB
        AiMessage::create([
            'ai_conversation_id' => $this->currentConversationId,
            'role' => 'user',
            'content' => $userText
        ]);

        // 3. Update UI state (append locally for speed)
        $this->messages[] = [
            'role' => 'user',
            'content' => $userText
        ];

        $context = array_slice($this->messages, -10);
        $this->isTyping = true;

        // Instead of calling Ollama synchronously, we return the conversation ID
        // The frontend will then initiate the SSE stream
        $this->dispatch('start-streaming', conversationId: $this->currentConversationId);
    }
    
    public function saveAiResponse($content)
    {
        if (!$this->currentConversationId || empty($content)) return;
        
        // Save the full response to DB after streaming is done
        AiMessage::create([
            'ai_conversation_id' => $this->currentConversationId,
            'role' => 'assistant',
            'content' => $content
        ]);
        
        // Also update local state to be in sync
        $this->messages[] = [
            'role' => 'assistant',
            'content' => $content
        ];
        
        $this->isTyping = false;
    }

    public function render()
    {
        return view('livewire.ai-chat')->layout('layouts.app');
    }
}
