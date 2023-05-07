<?php

namespace App\Http\Livewire;

use App\Models\Chat;
use App\Models\ChatGptPrompt;
use App\Models\ChatGptResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ChatInterface extends Component
{
    public $inputText = '';
    public $messages = [];
    public $charCount = 0;
    public $selectedChat = null;
    public $newChatName = null;
    public $chatList = [];

    public function mount()
    {
        $this->selectedChat = Chat::latest()->first();

        $this->loadChatList();
    }

    public function loadChatList()
    {
        $this->chatList = Chat::select('title')->get()->pluck('title')->toArray();
    }

    public function selectChat($chatTitle = null)
    {
        if ($chatTitle) {
            $this->selectedChat = Chat::where('title', $chatTitle)->firstOrCreate();
        } elseif ($this->newChatName) {
            $this->selectedChat = Chat::where('title', $this->newChatName)->firstOrCreate(['title' => $this->newChatName]);
            $this->newChatName = null;
        }

        $this->loadChatList();
        $this->loadMessages();
    }

    public function sendMessage()
    {
        if (trim($this->inputText) === '') {
            return;
        }

        $chat = $this->selectedChat ?: Chat::firstOrCreate(['title' => $this->selectedChat]);

        $chatGptPrompt = $chat->chatGptPrompts()->create([
            'prompt' => $this->inputText,
        ]);

        $this->messages[] = ['role' => 'user', 'content' => $this->inputText];
        $this->inputText = '';
        $this->loadMessages();

        $this->generateResponse($chatGptPrompt);
    }

    private function formatMessagesForApi(Chat $chat, ChatGptPrompt $chatGptPrompt)
    {
        $previousMessages = $chat->chatGptPrompts()->with('responses')->get();

        $messages = $previousMessages->flatMap(function ($previousPrompt) {
            $messages = [
                ['role' => 'user', 'content' => $previousPrompt->prompt],
            ];

            $assistantMessages = $previousPrompt->responses->map(function ($previousResponse) {
                return ['role' => 'assistant', 'content' => $previousResponse->response];
            })->toArray();

            return array_merge($messages, $assistantMessages);
        });

        $messages->push(['role' => 'user', 'content' => 'respond in markdown' . $chatGptPrompt->prompt]);

        return $this->limitMessageLength($messages)->toArray();
    }

    private function limitMessageLength(Collection $messages)
    {
        $totalLength = $messages->sum(function ($message) {
            return strlen($message['content']);
        });

        if ($totalLength > 4000) {
            $messages = $messages->reverse();

            $newMessages = collect();
            $length = 0;

            foreach ($messages as $message) {
                $messageLength = strlen($message['content']);

                if ($length + $messageLength <= 4000) {
                    $newMessages->push($message);
                    $length += $messageLength;
                } else {
                    break;
                }
            }

            $messages = $newMessages->reverse();
        }

        return $messages;
    }

    private function callOpenApi(ChatGptPrompt $chatGptPrompt, $messages)
    {
        $apiKey = env('OPENAI_API_KEY');
        $endpoint = 'https://api.openai.com/v1/chat/completions';
        $model = 'gpt-4';

        return Http::timeout(120)->withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.7,
        ]);
    }

    private function handleApiResponse(ChatGptPrompt $chatGptPrompt, $response)
    {
        if ($response->successful()) {
            $responseData = $response->json();
            $choice = $responseData['choices'][0];

            $chatGptPrompt->responses()->create([
                'response' => $choice['message']['content'],
                'response_id' => $responseData['id'],
                'object' => $responseData['object'],
                'created' => $responseData['created'],
                'model' => $responseData['model'],
                'prompt_tokens' => $responseData['usage']['prompt_tokens'],
                'completion_tokens' => $responseData['usage']['completion_tokens'],
                'total_tokens' => $responseData['usage']['total_tokens'],
                'finish_reason' => $choice['finish_reason'],
            ]);

            $this->messages[] = ['role' => 'assistant', 'content' => $choice['message']['content']];
        } else {
            $this->messages[] = ['role' => 'assistant', 'content' => 'An error occurred while fetching data from the API. Please check your API key and endpoint.'];
        }
    }

    public function generateResponse(ChatGptPrompt $chatGptPrompt)
    {
        $chat = $chatGptPrompt->chat;

        $messages = array_values($this->formatMessagesForApi($chat, $chatGptPrompt));

        try {
            $this->emit('loading', true);

            $response = $this->callOpenApi($chatGptPrompt, $messages);

            $this->handleApiResponse($chatGptPrompt, $response);
        } catch (\Exception $e) {
            $this->messages[] = ['role' => 'assistant', 'content' => 'An error occurred while fetching data from the API. Please check your API key and endpoint.'];
        } finally {
            $this->emit('loading', false);
        }

        $this->loadMessages();
    }

    public function loadMessages()
    {
        $chat = $this->selectedChat ?: Chat::where('title', $this->selectedChat)->first();

        if (!$chat) {
            return;
        }

        $this->messages = $chat->chatGptPrompts()->with('responses')->get()->flatMap(function ($chatGptPrompt) {
            $userMessage = ['role' => 'user', 'content' => $chatGptPrompt->prompt];
            $assistantMessages = $chatGptPrompt->responses->map(function ($chatGptResponse) {
                return ['role' => 'assistant', 'content' => $chatGptResponse->response];
            })->toArray();
            return array_merge([$userMessage], $assistantMessages);
        })->toArray();
    }

    public function render()
    {
        return view('livewire.chat-interface');
    }
}
