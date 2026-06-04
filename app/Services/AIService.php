<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('ai.drivers.openai.api_key');
        $this->model = config('ai.drivers.openai.model');
        $this->baseUrl = config('ai.drivers.openai.base_url');
    }

    public function generateOutline(string $topic): array
    {
        $prompt = str_replace('{topic}', $topic, config('ai.prompts.outline'));

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a professional book writer creating detailed outlines.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 2000,
            ])->throw();

            $content = $response->json('choices.0.message.content');

            // Try to parse JSON from the response
            $jsonMatch = preg_match('/\{.*\}/s', $content, $matches);
            if ($jsonMatch) {
                $parsed = json_decode($matches[0], true);
                if ($parsed && isset($parsed['chapters'])) {
                    return $parsed['chapters'];
                }
            }

            // Fallback: create basic structure from content
            return [
                [
                    'title' => 'Chapter 1: Introduction',
                    'description' => substr($content, 0, 200),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('AI outline generation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function generateContent(string $title): string
    {
        $prompt = str_replace('{title}', $title, config('ai.prompts.content'));

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a professional book writer. Write engaging and informative content.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 1500,
            ])->throw();

            return $response->json('choices.0.message.content');
        } catch (\Exception $e) {
            Log::error('AI content generation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
