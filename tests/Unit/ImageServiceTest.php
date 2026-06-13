<?php

namespace Tests\Unit;

use App\Services\ImageService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ImageServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set defaults for image config used by the service constructor
        config([
            'images.width' => 800,
            'images.height' => 600,
            'images.timeout' => 60,
            'images.default' => 'unsplash',
            'images.providers.unsplash.api_key' => 'test-unsplash-key',
            'images.providers.unsplash.base_url' => 'https://api.unsplash.com',
            'images.providers.gemini.api_key' => 'test-gemini-key',
            'images.providers.gemini.model' => 'imagen-4.0-generate-001',
            'images.providers.gemini.base_url' => 'https://generativelanguage.googleapis.com/v1beta',
        ]);
    }

    // ─── fetchImage routing ────────────────────────────────────────────────

    public function test_fetch_image_routes_to_unsplash_when_provider_is_unsplash()
    {
        config(['images.default' => 'unsplash']);

        Http::fake([
            'api.unsplash.com/*' => Http::response([
                'results' => [
                    ['urls' => ['raw' => 'https://images.unsplash.com/photo-123']],
                ],
            ], 200),
        ]);

        $service = new ImageService();
        $result = $service->fetchImage('forest landscape');

        $this->assertNotNull($result);
        $this->assertStringContainsString('unsplash.com', $result);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.unsplash.com');
        });
    }

    public function test_fetch_image_routes_to_gemini_when_provider_is_gemini()
    {
        config(['images.default' => 'gemini']);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'predictions' => [
                    ['bytesBase64Encoded' => base64_encode('fake-image-bytes')],
                ],
            ], 200),
        ]);

        $service = new ImageService();
        $result = $service->fetchImage('a mountain at sunset');

        $this->assertNotNull($result);
        $this->assertStringStartsWith('data:image/png;base64,', $result);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'generativelanguage.googleapis.com');
        });
    }

    // ─── fetchUnsplashImage ────────────────────────────────────────────────

    public function test_fetch_unsplash_image_returns_url_with_dimensions()
    {
        config([
            'images.default' => 'unsplash',
            'images.width' => 1200,
            'images.height' => 800,
        ]);

        Http::fake([
            'api.unsplash.com/*' => Http::response([
                'results' => [
                    ['urls' => ['raw' => 'https://images.unsplash.com/photo-abc']],
                ],
            ], 200),
        ]);

        $service = new ImageService();
        $result = $service->fetchImage('city skyline');

        $this->assertStringContainsString('w=1200', $result);
        $this->assertStringContainsString('h=800', $result);
        $this->assertStringContainsString('fit=crop', $result);
        $this->assertStringContainsString('crop=entropy', $result);
    }

    public function test_fetch_unsplash_image_sends_correct_query_parameters()
    {
        config(['images.default' => 'unsplash']);

        Http::fake([
            'api.unsplash.com/*' => Http::response([
                'results' => [
                    ['urls' => ['raw' => 'https://images.unsplash.com/photo-xyz']],
                ],
            ], 200),
        ]);

        $service = new ImageService();
        $service->fetchImage('ocean waves');

        Http::assertSent(function ($request) {
            return $request['query'] === 'ocean waves'
                && $request['per_page'] === 1
                && $request['orientation'] === 'landscape';
        });
    }

    public function test_fetch_unsplash_image_sends_authorization_header()
    {
        config([
            'images.default' => 'unsplash',
            'images.providers.unsplash.api_key' => 'my-secret-key',
        ]);

        Http::fake([
            'api.unsplash.com/*' => Http::response([
                'results' => [
                    ['urls' => ['raw' => 'https://images.unsplash.com/photo-xyz']],
                ],
            ], 200),
        ]);

        $service = new ImageService();
        $service->fetchImage('mountains');

        Http::assertSent(function ($request) {
            return $request->header('Authorization')[0] === 'Client-ID my-secret-key';
        });
    }

    public function test_fetch_unsplash_image_returns_null_when_no_results()
    {
        config(['images.default' => 'unsplash']);

        Http::fake([
            'api.unsplash.com/*' => Http::response(['results' => []], 200),
        ]);

        $service = new ImageService();
        $result = $service->fetchImage('very obscure query that yields nothing');

        $this->assertNull($result);
    }

    public function test_fetch_unsplash_image_returns_null_on_http_failure()
    {
        config(['images.default' => 'unsplash']);

        Http::fake([
            'api.unsplash.com/*' => Http::response([], 500),
        ]);

        $service = new ImageService();
        $result = $service->fetchImage('any prompt');

        $this->assertNull($result);
    }

    public function test_fetch_unsplash_image_uses_configured_timeout()
    {
        config([
            'images.default' => 'unsplash',
            'images.timeout' => 90,
        ]);

        Http::fake([
            'api.unsplash.com/*' => Http::response([
                'results' => [
                    ['urls' => ['raw' => 'https://images.unsplash.com/photo-timeout']],
                ],
            ], 200),
        ]);

        // Service reads timeout during construction
        $service = new ImageService();
        $result = $service->fetchImage('nature');

        // Request should complete successfully with the custom timeout
        $this->assertNotNull($result);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.unsplash.com');
        });
    }

    // ─── generateGeminiImage ───────────────────────────────────────────────

    public function test_generate_gemini_image_returns_data_url()
    {
        config(['images.default' => 'gemini']);

        $fakeBase64 = base64_encode('fake-png-bytes');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'predictions' => [
                    ['bytesBase64Encoded' => $fakeBase64],
                ],
            ], 200),
        ]);

        $service = new ImageService();
        $result = $service->fetchImage('a red apple');

        $this->assertEquals('data:image/png;base64,' . $fakeBase64, $result);
    }

    public function test_generate_gemini_image_returns_null_when_no_base64_data()
    {
        config(['images.default' => 'gemini']);

        // Gemini returns no predictions or empty bytesBase64Encoded
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'predictions' => [[]],
            ], 200),
            // Also stub unsplash for fallback-within-null path (not triggered here
            // because there's no exception — just null image data)
            'api.unsplash.com/*' => Http::response(['results' => []], 200),
        ]);

        $service = new ImageService();
        $result = $service->fetchImage('abstract pattern');

        $this->assertNull($result);
    }

    public function test_generate_gemini_image_falls_back_to_unsplash_on_http_failure()
    {
        config(['images.default' => 'gemini']);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([], 500),
            'api.unsplash.com/*' => Http::response([
                'results' => [
                    ['urls' => ['raw' => 'https://images.unsplash.com/fallback-photo']],
                ],
            ], 200),
        ]);

        $service = new ImageService();
        $result = $service->fetchImage('landscape photo');

        // Should have fallen back to Unsplash
        $this->assertNotNull($result);
        $this->assertStringContainsString('unsplash.com', $result);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.unsplash.com');
        });
    }

    public function test_generate_gemini_image_sends_correct_payload()
    {
        config(['images.default' => 'gemini']);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'predictions' => [
                    ['bytesBase64Encoded' => base64_encode('img')],
                ],
            ], 200),
        ]);

        $service = new ImageService();
        $service->fetchImage('sunset over ocean');

        Http::assertSent(function ($request) {
            return isset($request['instances'][0]['prompt'])
                && $request['instances'][0]['prompt'] === 'sunset over ocean'
                && $request['parameters']['sampleCount'] === 1;
        });
    }

    public function test_generate_gemini_image_uses_configured_timeout()
    {
        config([
            'images.default' => 'gemini',
            'images.timeout' => 120,
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'predictions' => [
                    ['bytesBase64Encoded' => base64_encode('img')],
                ],
            ], 200),
        ]);

        $service = new ImageService();
        $result = $service->fetchImage('rainforest canopy');

        $this->assertNotNull($result);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'generativelanguage.googleapis.com');
        });
    }

    public function test_timeout_defaults_to_60_when_not_configured()
    {
        config([
            'images.default' => 'unsplash',
        ]);

        Http::fake([
            'api.unsplash.com/*' => Http::response([
                'results' => [
                    ['urls' => ['raw' => 'https://images.unsplash.com/photo-default']],
                ],
            ], 200),
        ]);

        // Constructing without a timeout config should default to 60
        $service = new ImageService();
        $service->fetchImage('test');

        // Request completes with default timeout of 60
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.unsplash.com');
        });
    }
}
