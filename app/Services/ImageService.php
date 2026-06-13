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

    /**
     * Initialize image service settings from configuration.
     *
     * Sets the target image width and height from `images.width` and `images.height`,
     * and sets the HTTP timeout from `images.timeout` (defaults to 60 seconds).
     */
    public function __construct()
    {
        $this->width = config('images.width');
        $this->height = config('images.height');
        $configuredTimeout = (int) config('images.timeout', 60);
        $this->timeout = max(1, $configuredTimeout);
    }

    /**
     * Selects the configured image provider and obtains an image for the given prompt.
     *
     * Chooses the provider configured under `images.default` and returns either a provider-generated
     * data URL or a provider-hosted image URL resized to the service configuration. If no image
     * can be obtained, returns `null`.
     *
     * @param string $prompt Natural-language prompt or query used to generate or search for the image.
     * @return string|null A data URL or image URL appropriate for embedding; `null` if no image was obtained.
     */
    public function fetchImage(string $prompt): ?string
    {
        $provider = config('images.default');

        if ($provider === 'gemini') {
            return $this->generateGeminiImage($prompt);
        }

        return $this->fetchUnsplashImage($prompt);
    }

    /**
     * Fetches a suitable Unsplash photo URL for the given search query.
     *
     * @param string $query The search term used to find an Unsplash photo.
     * @return string|null The photo URL with resizing and cropping parameters applied (`&w={width}&h={height}&fit=crop&crop=entropy`), or `null` if no photo is found or the request fails.
     */
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

    /**
     * Generate an image using the configured Gemini provider for the given prompt.
     *
     * Sends the prompt to Gemini and returns a PNG image encoded as a data URL when available.
     *
     * @param string $prompt The text prompt describing the desired image.
     * @return string|null Data URL (`data:image/png;base64,...`) of the generated image if Gemini returned image data, `null` if Gemini returned no image. On request failure, returns the Unsplash fallback result (data URL or `null`).
     */
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
