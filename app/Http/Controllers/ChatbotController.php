<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    // public function message(Request $request)
    // {
    //     $userMessage = $request->input('message');

    //     // Call OpenAI API (replace with your actual OpenAI API key)
    //     $response = Http::withHeaders([
    //         'Authorization' => 'Bearer sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
    //     ])->post('https://api.openai.com/v1/chat/completions', [
    //         'model' => 'gpt-3.5-turbo',
    //         'messages' => [
    //             ['role' => 'system', 'content' => 'You are an agriculture assistant for AgriEcom.'],
    //             ['role' => 'user', 'content' => $userMessage],
    //         ],
    //         'max_tokens' => 100,
    //     ]);

    //     // Optional: Log the response for debugging
    //     // \Log::info($response->body());

    //     $aiReply = $response->json('choices.0.message.content') ?? 'Sorry, I could not process your request.';

    //     return response()->json(['reply' => $aiReply]);
    // }
public function message(Request $request)
{
    $userMessage = $request->input('message');

    $response = Http::withHeaders([
        'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
    ])->post('https://api.openai.com/v1/chat/completions', [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            ['role' => 'system', 'content' => 'You are an agriculture assistant for AgriEcom.'],
            ['role' => 'user', 'content' => $userMessage],
        ],
        'max_tokens' => 100,
    ]);

    if (!$response->successful()) {
        return response()->json(['reply' => 'Sorry, the AI service is unavailable.']);
    }

    $aiReply = $response->json('choices.0.message.content') ?? 'Sorry, I could not process your request.';

    return response()->json(['reply' => $aiReply]);
}
}