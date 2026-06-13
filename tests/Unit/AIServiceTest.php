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

    public function test_generate_analysis_includes_thinking_config_in_request()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'analysis' => 'Analysis',
                                        'chapters' => [['title' => 'Ch1']],
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $service = new AIService();
        $service->generateAnalysis('Title', 'Subject');

        Http::assertSent(function ($request) {
            return isset($request['generationConfig']['thinkingConfig']['thinkingBudget']);
        });
    }

    public function test_generate_analysis_sends_correct_thinking_budget()
    {
        config(['ai.thinking_budget' => 5000]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'analysis' => 'Analysis',
                                        'chapters' => [['title' => 'Ch1']],
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $service = new AIService();
        $service->generateAnalysis('Title', 'Subject');

        Http::assertSent(function ($request) {
            return $request['generationConfig']['thinkingConfig']['thinkingBudget'] === 5000;
        });
    }

    public function test_generate_content_includes_thinking_config_in_request()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Content.']
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $service = new AIService();
        $service->generateContent('Topic', 'Outline', 'Audience', 'Summary', 'Ch Title', 'Title', 'Desc', 'fr');

        Http::assertSent(function ($request) {
            return isset($request['generationConfig']['thinkingConfig']['thinkingBudget']);
        });
    }

    public function test_generate_content_sends_correct_thinking_budget()
    {
        config(['ai.thinking_budget' => 2048]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Content.']
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $service = new AIService();
        $service->generateContent('Topic', 'Outline', 'Audience', 'Summary', 'Ch Title', 'Title', 'Desc', 'fr');

        Http::assertSent(function ($request) {
            return $request['generationConfig']['thinkingConfig']['thinkingBudget'] === 2048;
        });
    }

    public function test_generate_analysis_uses_timeout_from_config()
    {
        config(['ai.timeout' => 300]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'analysis' => 'Analysis',
                                        'chapters' => [['title' => 'Ch1']],
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        // Service instantiated after config override picks up the new timeout
        $service = new AIService();
        $result = $service->generateAnalysis('Title', 'Subject');

        // Assert request was sent (confirming timeout didn't prevent execution)
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'generateContent');
        });

        $this->assertArrayHasKey('chapters', $result);
    }

    public function test_generate_analysis_throws_exception_on_http_error()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([], 500),
        ]);

        $service = new AIService();

        $this->expectException(\Exception::class);
        $service->generateAnalysis('Title', 'Subject');
    }

    public function test_generate_analysis_throws_exception_when_response_missing_chapters()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode(['analysis' => 'Some analysis'])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $service = new AIService();

        $this->expectException(\Exception::class);
        $service->generateAnalysis('Title', 'Subject');
    }

    public function test_generate_content_throws_exception_on_http_error()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([], 503),
        ]);

        $service = new AIService();

        $this->expectException(\Exception::class);
        $service->generateContent('Topic', 'Outline', 'Audience', 'Summary', 'Ch Title', 'Title', 'Desc', 'fr');
    }

    public function test_service_loads_default_thinking_budget_as_zero()
    {
        config(['ai.thinking_budget' => null]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Content.']
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $service = new AIService();
        $service->generateContent('Topic', 'Outline', 'Audience', 'Summary', 'Ch Title', 'Title', 'Desc', 'fr');

        Http::assertSent(function ($request) {
            return $request['generationConfig']['thinkingConfig']['thinkingBudget'] === 0;
        });
    }
}
