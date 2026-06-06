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
        $this->apiKey = config('ai.api_key');
        $this->model = config('ai.model');
        $this->baseUrl = config('ai.base_url');
    }

    public function generateOutline(string $topic): array
    {
        $prompt = str_replace('{topic}', $topic, config('ai.prompts.outline'));

        try {
            $response = Http::post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => "You are a professional book writer creating detailed outlines.\n\n" . $prompt]
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 2000,
                    'responseMimeType' => 'application/json',
                ],
            ])->throw();

            $content = $response->json('candidates.0.content.parts.0.text');

            if ($content) {
                $parsed = json_decode($content, true);
                if ($parsed && isset($parsed['chapters'])) {
                    return $parsed['chapters'];
                }
            }

            // Fallback: Try to parse JSON from the response if something went wrong with the JSON mode
            $jsonMatch = preg_match('/\{.*\}/s', $content, $matches);
            if ($jsonMatch) {
                $parsed = json_decode($matches[0], true);
                if ($parsed && isset($parsed['chapters'])) {
                    return $parsed['chapters'];
                }
            }

            return [
                [
                    'title' => 'Chapter 1: Introduction',
                    'description' => substr($content, 0, 200),
                ],
            ];
        } catch (\Exception $e) {
            Log::error('AI outline generation failed', [
                'error' => $e->getMessage(),
                'response' => isset($response) ? $response->body() : 'No response',
            ]);
            throw $e;
        }
    }

    public function generateContent(string $title): string
    {
        $prompt = str_replace('{title}', $title, config('ai.prompts.content'));

        try {
            $response = Http::post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => "You are a professional book writer. Write engaging and informative content.\n\n" . $prompt]
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1500,
                ],
            ])->throw();

            return $response->json('candidates.0.content.parts.0.text');
        } catch (\Exception $e) {
            Log::error('AI content generation failed', [
                'error' => $e->getMessage(),
                'response' => isset($response) ? $response->body() : 'No response',
            ]);
            throw $e;
        }
    }
}
