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

    public function __construct()
    {
        $this->apiKey = config('images.providers.unsplash.api_key');
        $this->baseUrl = config('images.providers.unsplash.base_url');
        $this->width = config('images.width');
        $this->height = config('images.height');
    }

    public function fetchImage(string $query): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Client-ID {$this->apiKey}",
            ])->get("{$this->baseUrl}/search/photos", [
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

            // Return the raw URL with dimensions
            return "{$photo['urls']['raw']}&w={$this->width}&h={$this->height}&fit=crop&crop=entropy";
        } catch (\Exception $e) {
            Log::error('Image fetch failed', ['error' => $e->getMessage(), 'query' => $query]);
            return null;
        }
    }

    public function generateImagePrompt(string $chapterTitle): string
    {
        return trim($chapterTitle . ' professional illustration');
    }
}
