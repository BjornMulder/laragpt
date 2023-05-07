<?php

namespace App\Http\Livewire;

use App\Models\ChatGptPrompt;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use App\Models\ChatGptResponse;

class ChatGPT extends Component
{
    public $inputText = '';
    public $responseText4 = '';
    public $responseText35 = '';

    public function generateResponse()
    {
        $apiKey = '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';
        $endpoint = 'https://api.openai.com/v1/chat/completions';

        $models = ['gpt-3.5-turbo', 'gpt-4'];
        $messages = [
            [
                'role' => 'user',
                'content' => $this->inputText,
            ],
        ];

        $chatGptPrompt = ChatGptPrompt::create([
            'prompt' => $this->inputText,
        ]);

        try {
            $this->emit('loading', true);

            foreach ($models as $model) {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Content-Type' => 'application/json',
                ])->post($endpoint, [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.7,
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    $choice = $responseData['choices'][0];

                    ChatGptResponse::create([
                        'chat_gpt_prompt_id' => $chatGptPrompt->id,
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

                    if ($model === 'gpt-3.5-turbo') {
                        $this->responseText35 = $responseData['choices'][0]['message']['content'];
                    } else {
                        $this->responseText4 = $responseData['choices'][0]['message']['content'];
                    }
                } else {
                    $this->responseText35 = 'An error occurred while fetching data from the API. Please check your API key and endpoint.';
                    $this->responseText4 = 'An error occurred while fetching data from the API. Please check your API key and endpoint.';
                }
            }

        } catch (\Exception $e) {
            dd($e);
            $this->responseText35 = 'An error occurred while fetching data from the API. Please check your API key and endpoint.';
            $this->responseText4 = 'An error occurred while fetching data from the API. Please check your API key and endpoint.';
        } finally {
            $this->emit('loading', false);
        }
    }

    public function render()
    {
        return view('livewire.chat-gpt');
    }
}
