<?php

namespace App\Http\Livewire;

use App\Models\Chat;
use App\Models\ChatGptPrompt;
use App\Models\ChatGptResponse;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Compare extends Component
{
    public $inputText = '';
    public $responses = [];

    private function fetchAvailableModels()
    {
        $apiKey = env('OPENAI_API_KEY');
        $endpoint = 'https://api.openai.com/v1/models';

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
        ])->get($endpoint);

        if ($response->successful()) {
            return collect($response->json()['data'])->pluck('id');
        } else {
            return collect([]);
        }
    }

    public function generateResponse()
    {
        $messages = [
            [
                'role' => 'user',
                'content' => $this->inputText,
            ],
        ];

        $chat = Chat::firstOrCreate(['session_id' => session()->getId()]);

        $chatGptPrompt = $chat->chatGptPrompts()->create([
            'prompt' => $this->inputText,
        ]);

        $models = $this->fetchAvailableModels();

        try {
            $this->emit('loading', true);

            foreach ($models as $model) {
                $response = $this->callOpenApi($model, $messages);

                if ($response !== null) {
                    $chatGptResponse = ChatGptResponse::create([
                        'chat_gpt_prompt_id' => $chatGptPrompt->id,
                        'response' => $response['response_text'],
                        'response_id' => $response['response_data']['id'],
                        'object' => $response['response_data']['object'],
                        'created' => $response['response_data']['created'],
                        'model' => $response['response_data']['model'],
                        'prompt_tokens' => $response['response_data']['usage']['prompt_tokens'],
                        'completion_tokens' => $response['response_data']['usage']['completion_tokens'],
                        'total_tokens' => $response['response_data']['usage']['total_tokens'],
                        'finish_reason' => $response['finish_reason'],
                    ]);

                    $this->responses[] = [
                        'model' => $model,
                        'response' => $chatGptResponse->response,
                    ];
                }
            }

        } catch (\Exception $e) {
            foreach ($models as $model) {
                $this->responses[] = [
                    'model' => $model,
                    'response' => 'An error occurred while fetching data from the API. Please check your API key and endpoint.',
                ];
            }
        } finally {
            $this->emit('loading', false);
        }
    }

private function callOpenApi($model, $messages)
{
    $apiKey = env('OPENAI_API_KEY');

    if (in_array($model, ['gpt-4', 'gpt-4-0314', 'gpt-4-32k', 'gpt-4-32k-0314', 'gpt-3.5-turbo', 'gpt-3.5-turbo-0301'])) {
        $endpoint = 'https://api.openai.com/v1/chat/completions';
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.7,
        ];
    } elseif (in_array($model, ['text-davinci-003', 'text-davinci-002', 'text-curie-001', 'text-babbage-001', 'text-ada-001'])) {
        $endpoint = 'https://api.openai.com/v1/completions';
        $payload = [
            'model' => $model,
            'prompt' => end($messages)['content'],
            'temperature' => 0.7,
            'max_tokens' => 50, // Adjust as needed
        ];
    } else {
        return null;
    }

    $response = Http::withHeaders([
        'Authorization' => "Bearer {$apiKey}",
        'Content-Type' => 'application/json',
    ])->post($endpoint, $payload);

    if ($response->successful()) {
        $responseData = $response->json();
        $choice = $responseData['choices'][0];

        return [
            'response_data' => $responseData,
            'response_text' => $endpoint === 'https://api.openai.com/v1/chat/completions' ? $choice['message']['content'] : $choice['text'],
            'finish_reason' => $choice['finish_reason'],
        ];
    } else {
        return null;
    }
}

    public function render()
    {
        return view('livewire.compare');
    }
}
