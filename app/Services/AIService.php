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
    private int $thinkingBudget;

    /**
     * Initialize the service by loading AI-related configuration values.
     *
     * Loads the API key, model name, and base URL from config keys `ai.api_key`, `ai.model`,
     * and `ai.base_url`. Also loads `ai.timeout` and `ai.thinking_budget`, casting them to
     * integers and storing them as the request timeout and thinking budget respectively.
     */
    public function __construct()
    {
        $this->apiKey = config('ai.api_key');
        $this->model = config('ai.model');
        $this->baseUrl = config('ai.base_url');
        $this->timeout = (int) config('ai.timeout', 120);
        $this->thinkingBudget = (int) config('ai.thinking_budget', 0);
    }

    /**
     * Determine if thinkingConfig should be included based on the model name and thinking budget.
     */
    private function shouldIncludeThinkingConfig(): bool
    {
        $supportsThinking = str_contains(strtolower($this->model), 'thinking');

        if (!$supportsThinking) {
            return false;
        }

        if ($this->thinkingBudget === 0) {
            return false;
        }

        return true;
    }

    /**
     * Generates a structured analysis (including chapters) for the given title and subject using the configured AI model.
     *
     * Sends the composed prompt to the AI service and parses the returned JSON content into an associative array.
     *
     * @param string $title The title to include in the analysis prompt.
     * @param string $subject The subject/context to include in the analysis prompt.
     * @param string $language The language code for the analysis output (default 'fr').
     * @return array The decoded associative array produced by the AI; must include a `chapters` key on success.
     * @throws \Exception If the AI response is missing or invalid, or if the HTTP request fails.
     */
    public function generateAnalysis(string $title, string $subject, string $language = 'fr', ?string $filePath = null): array
    {
        $prompt = str_replace(
            ['{title}', '{subject}', '{language}'],
            [$title, $subject, $language],
            config('ai.prompts.analysis')
        );

        $parts = [['text' => $prompt]];

        if ($filePath && \Illuminate\Support\Facades\Storage::disk('local')->exists($filePath)) {
            $mimeType = \Illuminate\Support\Facades\Storage::disk('local')->mimeType($filePath);
            if (str_starts_with($mimeType, 'video/')) {
                $parts[] = [
                    'inlineData' => [
                        'mimeType' => $mimeType,
                        'data' => base64_encode(\Illuminate\Support\Facades\Storage::disk('local')->get($filePath))
                    ]
                ];
            }
        }

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => $parts,
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 8192,
                'responseMimeType' => 'application/json',
            ],
        ];

        if ($this->shouldIncludeThinkingConfig()) {
            $payload['generationConfig']['thinkingConfig'] = [
                'thinkingBudget' => $this->thinkingBudget,
            ];
        }

        try {
            try {
                $response = Http::timeout($this->timeout)->post(
                    "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
                    $payload
                )->throw();
            } catch (\Illuminate\Http\Client\RequestException $e) {
                if ($e->response->status() === 400 && isset($payload['generationConfig']['thinkingConfig'])) {
                    Log::warning('AI analysis generation failed with 400, retrying without thinkingConfig', [
                        'error' => $e->getMessage(),
                    ]);
                    unset($payload['generationConfig']['thinkingConfig']);
                    $response = Http::timeout($this->timeout)->post(
                        "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
                        $payload
                    )->throw();
                } else {
                    throw $e;
                }
            }

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

    /**
     * Generate chapter content from the AI using the configured prompt template and provided context.
     *
     * @param string $topic The main topic for the content.
     * @param string $outline The chapter or section outline to guide generation.
     * @param string $targetAudience Description of the intended audience.
     * @param string $summary A short summary of the content to produce.
     * @param string $chapterTitle The specific chapter title to focus on.
     * @param string $title The overall work title.
     * @param string $description Additional description or instructions for the AI.
     * @param string $language Language code for the generated content (defaults to 'fr').
     * @return string The raw text content produced by the AI (extracted from the response at candidates.0.content.parts.0.text).
     */
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

        $payload = [
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
        ];

        if ($this->shouldIncludeThinkingConfig()) {
            $payload['generationConfig']['thinkingConfig'] = [
                'thinkingBudget' => $this->thinkingBudget,
            ];
        }

        try {
            try {
                $response = Http::timeout($this->timeout)->post(
                    "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
                    $payload
                )->throw();
            } catch (\Illuminate\Http\Client\RequestException $e) {
                if ($e->response->status() === 400 && isset($payload['generationConfig']['thinkingConfig'])) {
                    Log::warning('AI content generation failed with 400, retrying without thinkingConfig', [
                        'error' => $e->getMessage(),
                    ]);
                    unset($payload['generationConfig']['thinkingConfig']);
                    $response = Http::timeout($this->timeout)->post(
                        "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
                        $payload
                    )->throw();
                } else {
                    throw $e;
                }
            }

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
