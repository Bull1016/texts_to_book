<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportSection;
use App\Models\ReportImage;
use Illuminate\Support\Facades\Log;

class ReportService
{
    private AIService $aiService;
    private ImageService $imageService;

    public function __construct(
        AIService $aiService,
        ImageService $imageService
    ) {
        $this->aiService = $aiService;
        $this->imageService = $imageService;
    }

    public function generateReport(Report $report): void
    {
        try {
            $report->update(['status' => 'generating', 'progress' => 10]);

            // Step 1: Generate outline
            Log::info("Generating outline for report {$report->id}");
            $outline = $this->aiService->generateOutline($report->subject);
            $report->update([
                'outline' => $outline,
                'progress' => 30,
            ]);

            // Step 2: Generate content for each section
            foreach ($outline as $index => $chapter) {
                Log::info("Generating content for chapter: {$chapter['title']}");

                $content = $this->aiService->generateContent($chapter['title']);

                $section = ReportSection::create([
                    'report_id' => $report->id,
                    'title' => $chapter['title'],
                    'content' => $content,
                    'order' => $index,
                ]);

                // Step 3: Fetch images
                $imagePrompt = $this->imageService->generateImagePrompt($chapter['title']);
                $imageUrl = $this->imageService->fetchImage($imagePrompt);

                if ($imageUrl) {
                    ReportImage::create([
                        'report_section_id' => $section->id,
                        'prompt' => $imagePrompt,
                        'image_url' => $imageUrl,
                        'source' => 'unsplash',
                        'order' => 0,
                    ]);
                }

                $progress = 30 + (($index + 1) / count($outline)) * 60;
                $report->update(['progress' => (int)$progress]);
            }

            $report->update([
                'status' => 'completed',
                'progress' => 100,
            ]);

            Log::info("Report {$report->id} generated successfully");
        } catch (\Exception $e) {
            Log::error("Report generation failed for report {$report->id}", ['error' => $e->getMessage()]);
            $report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
