<?php

namespace Tests\Unit;

use App\Services\AIService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIServiceTest extends TestCase
{
    public function test_generate_outline_calls_gemini_correctly()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'chapters' => [
                                            ['title' => 'Chapter 1', 'description' => 'Desc 1'],
                                            ['title' => 'Chapter 2', 'description' => 'Desc 2'],
                                        ]
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $service = new AIService();
        $outline = $service->generateOutline('Test Topic');

        $this->assertCount(2, $outline);
        $this->assertEquals('Chapter 1', $outline[0]['title']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'models/gemini-2.5-flash:generateContent') &&
                   $request['generationConfig']['responseMimeType'] === 'application/json';
        });
    }

    public function test_generate_content_calls_gemini_correctly()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => 'Generated content here.'
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $service = new AIService();
        $content = $service->generateContent('Topic', 'Outline', 'Chapter Title', 'Section Title', 'Description', 'fr');

        $this->assertEquals('Generated content here.', $content);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'models/gemini-2.5-flash:generateContent') &&
                   isset($request['contents'][0]['parts'][0]['text']);
        });
    }
}
