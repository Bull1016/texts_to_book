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
            $report->update([
                'status' => 'generating',
                'progress' => 5,
                'current_step' => __('Generating cover image...')
            ]);

            // Step 0: Generate cover image
            Log::info("Generating cover image for report {$report->id}");
            $coverPrompt = $report->title . ' professional book cover illustration';
            $coverUrl = $this->imageService->fetchImage($coverPrompt);
            if ($coverUrl) {
                $report->update(['cover_image_url' => $coverUrl]);
            }

            $report->update([
                'progress' => 10,
                'current_step' => __('Generating report outline...')
            ]);

            // Step 1: Generate outline
            Log::info("Generating outline for report {$report->id}");
            $outline = $this->aiService->generateOutline($report->subject, $report->language ?? 'fr');
            $report->update([
                'outline' => $outline,
                'progress' => 20,
            ]);

            $outlineText = collect($outline)->map(function ($c) {
                $subs = collect($c['subsections'] ?? [])->map(fn($s) => "- " . $s['title'])->implode("\n");
                return $c['title'] . "\n" . $subs;
            })->implode("\n\n");

            $totalChapters = count($outline);
            $totalSubsections = collect($outline)->sum(fn($c) => count($c['subsections'] ?? []));
            $totalSteps = $totalChapters + $totalSubsections;
            $currentStep = 0;

            // Step 2: Generate content for each section
            foreach ($outline as $chapterIndex => $chapter) {
                Log::info("Generating content for chapter: {$chapter['title']}");

                $report->update([
                    'current_step' => __('Generating chapter: :title...', ['title' => $chapter['title']])
                ]);

                $chapterSection = ReportSection::create([
                    'report_id' => $report->id,
                    'title'     => $chapter['title'],
                    'content'   => '', // Chapters might just be containers or have intro
                    'order'     => $chapterIndex,
                ]);

                // Generate intro for chapter if needed, or just proceed to sub-sections
                $chapterContent = $this->aiService->generateContent(
                    $report->subject,
                    $outlineText,
                    $chapter['title'],
                    $chapter['title'],
                    $chapter['description'] ?? '',
                    $report->language ?? 'fr'
                );
                $chapterSection->update(['content' => $chapterContent]);

                // Step 3: Fetch images for chapter
                $imagePrompt = $this->imageService->generateImagePrompt($chapter['title']);
                $imageUrl = $this->imageService->fetchImage($imagePrompt);
                if ($imageUrl) {
                    ReportImage::create([
                        'report_section_id' => $chapterSection->id,
                        'prompt' => $imagePrompt,
                        'image_url' => $imageUrl,
                        'source' => 'unsplash',
                        'order' => 0,
                    ]);
                }

                $currentStep++;
                $progress = 20 + ($currentStep / $totalSteps) * 75;
                $report->update(['progress' => (int)$progress]);

                // Generate sub-sections
                if (isset($chapter['subsections']) && is_array($chapter['subsections'])) {
                    foreach ($chapter['subsections'] as $subIndex => $sub) {
                        Log::info("Generating content for sub-section: {$sub['title']}");

                        $report->update([
                            'current_step' => __('Generating sub-section: :title...', ['title' => $sub['title']])
                        ]);

                        $subContent = $this->aiService->generateContent(
                            $report->subject,
                            $outlineText,
                            $chapter['title'],
                            $sub['title'],
                            $sub['description'] ?? '',
                            $report->language ?? 'fr'
                        );

                        $subSection = ReportSection::create([
                            'report_id' => $report->id,
                            'parent_id' => $chapterSection->id,
                            'title'     => $sub['title'],
                            'content'   => $subContent,
                            'order'     => $subIndex,
                        ]);

                        $currentStep++;
                        $progress = 20 + ($currentStep / $totalSteps) * 75;
                        $report->update(['progress' => (int)$progress]);
                    }
                }
            }

            $report->update([
                'status' => 'completed',
                'progress' => 100,
                'current_step' => __('Generation complete!')
            ]);

            Log::info("Report {$report->id} generated successfully");
        } catch (\Exception $e) {
            Log::error("Report generation failed for report {$report->id}", ['error' => $e->getMessage()]);
            $report->update([
                'status' => 'failed',
                'error_message' => mb_substr($e->getMessage(), 0, 500),
                'current_step' => __('Error during generation')
            ]);
        }
    }
}
