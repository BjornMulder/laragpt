<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatGPTController extends Controller
{
    public function generateResponse(Request $request)
    {
        $endpoint = 'https://api.openai.com/v1/chat/completions';
        $apiKey = env('OPENAI_API_KEY');

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$apiKey}",
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'prompt' => $request->input('prompt'),
            'max_tokens' => 150,
        ]);

        return response()->json($response->json());
    }
}
