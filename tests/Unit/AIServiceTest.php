<?php

namespace Tests\Unit;

use App\Services\AIService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIServiceTest extends TestCase
{
    public function test_generate_analysis_calls_gemini_correctly()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'analysis' => 'Subject analysis',
                                        'target_audience' => 'Professionals',
                                        'summary' => 'Overall summary',
                                        'cover_illustration_prompt' => 'Cover prompt',
                                        'chapters' => [
                                            ['title' => 'Chapter 1', 'description' => 'Desc 1', 'illustration_prompt' => 'Ch1 prompt', 'subsections' => []],
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
        $analysis = $service->generateAnalysis('Title', 'Test Topic');

        $this->assertArrayHasKey('analysis', $analysis);
        $this->assertCount(1, $analysis['chapters']);
        $this->assertEquals('Chapter 1', $analysis['chapters'][0]['title']);

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
        $content = $service->generateContent('Topic', 'Outline', 'Audience', 'Summary', 'Chapter Title', 'Section Title', 'Description', 'fr');

        $this->assertEquals('Generated content here.', $content);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'models/gemini-2.5-flash:generateContent') &&
                   isset($request['contents'][0]['parts'][0]['text']);
        });
    }
}
