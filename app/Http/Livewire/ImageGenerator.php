<?php

namespace App\Http\Livewire;

use App\Models\ImagePrompt;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ImageGenerator extends Component
{
    public $prompt;
    public $imageUrl;

    public function generateImage()
    {
        $apiKey = env('OPENAI_API_KEY');
        $endpoint = 'https://api.openai.com/v1/images/generations';

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'model' => 'image-alpha-001',
            'prompt' => $this->prompt,
            'num_images' => 1,
            'size' => '1024x1024',
            'response_format' => 'url',
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            $this->imageUrl = $responseData['data'][0]['url'];

            // Save the image URL and prompt in the database
            ImagePrompt::create([
                'prompt' => $this->prompt,
                'image_url' => $this->imageUrl,
            ]);
        } else {
            $this->imageUrl = 'An error occurred while fetching data from the API. Please check your API key and endpoint.';
        }
    }

    public function render()
    {
        return view('livewire.image-generator');
    }
}
