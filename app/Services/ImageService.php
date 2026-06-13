<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageService
{
    private string $apiKey;
    private string $baseUrl;
    private int $width;
    private int $height;
    private int $timeout;

    public function __construct()
    {
        $this->width = config('images.width');
        $this->height = config('images.height');
        $this->timeout = (int) config('images.timeout', 60);
    }

    public function fetchImage(string $prompt): ?string
    {
        $provider = config('images.default');

        if ($provider === 'gemini') {
            return $this->generateGeminiImage($prompt);
        }

        return $this->fetchUnsplashImage($prompt);
    }

    private function fetchUnsplashImage(string $query): ?string
    {
        try {
            $apiKey = config('images.providers.unsplash.api_key');
            $baseUrl = config('images.providers.unsplash.base_url');

            $response = Http::timeout($this->timeout)->withHeaders([
                'Authorization' => "Client-ID {$apiKey}",
            ])->get("{$baseUrl}/search/photos", [
                'query' => $query,
                'per_page' => 1,
                'orientation' => 'landscape',
            ])->throw();

            $results = $response->json('results');

            if (empty($results)) {
                Log::warning("No Unsplash images found for query: {$query}");
                return null;
            }

            $photo = $results[0];

            return "{$photo['urls']['raw']}&w={$this->width}&h={$this->height}&fit=crop&crop=entropy";
        } catch (\Exception $e) {
            Log::error('Unsplash image fetch failed', ['error' => $e->getMessage(), 'query' => $query]);
            return null;
        }
    }

    private function generateGeminiImage(string $prompt): ?string
    {
        try {
            $config = config('images.providers.gemini');
            $url = "{$config['base_url']}/models/{$config['model']}:predict?key={$config['api_key']}";

            $response = Http::timeout($this->timeout)->post($url, [
                'instances' => [
                    ['prompt' => $prompt]
                ],
                'parameters' => [
                    'sampleCount' => 1,
                ],
            ])->throw();

            $base64Image = $response->json('predictions.0.bytesBase64Encoded');

            if (!$base64Image) {
                Log::warning("No Gemini image generated for prompt: {$prompt}");
                return null;
            }

            return 'data:image/png;base64,' . $base64Image;
        } catch (\Exception $e) {
            Log::error('Gemini image generation failed', [
                'error' => $e->getMessage(),
                'response' => isset($response) ? $response->body() : 'No response',
                'prompt' => $prompt
            ]);
            return $this->fetchUnsplashImage($prompt); // Fallback to Unsplash
        }
    }

    public function generateImagePrompt(string $chapterTitle): string
    {
        return trim($chapterTitle . ' professional illustration');
    }
}
