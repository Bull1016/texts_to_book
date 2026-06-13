<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->apiKey = config('ai.api_key');
        $this->model = config('ai.model');
        $this->baseUrl = config('ai.base_url');
        $this->timeout = (int) config('ai.timeout', 120);
    }

    public function generateAnalysis(string $title, string $subject, string $language = 'fr'): array
    {
        $prompt = str_replace(
            ['{title}', '{subject}', '{language}'],
            [$title, $subject, $language],
            config('ai.prompts.analysis')
        );

        try {
            $response = Http::timeout($this->timeout)->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt]
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 8192,
                    'responseMimeType' => 'application/json',
                ],
            ])->throw();

            $content = $response->json('candidates.0.content.parts.0.text');

            if ($content) {
                $parsed = json_decode($content, true);
                if ($parsed && isset($parsed['chapters'])) {
                    return $parsed;
                }
            }

            throw new \Exception("Invalid AI response for analysis: " . substr($content, 0, 100));
        } catch (\Exception $e) {
            Log::error('AI analysis generation failed', [
                'error' => $e->getMessage(),
                'response' => isset($response) ? $response->body() : 'No response',
            ]);
            throw $e;
        }
    }

    public function generateContent(
        string $topic,
        string $outline,
        string $targetAudience,
        string $summary,
        string $chapterTitle,
        string $title,
        string $description,
        string $language = 'fr'
    ): string {
        $prompt = str_replace(
            ['{topic}', '{outline}', '{target_audience}', '{summary}', '{chapter_title}', '{title}', '{description}', '{language}'],
            [$topic, $outline, $targetAudience, $summary, $chapterTitle, $title, $description, $language],
            config('ai.prompts.content')
        );

        try {
            $response = Http::timeout($this->timeout)->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $prompt]
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 8192,
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
