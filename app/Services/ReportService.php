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

    private function createPrefaceSections(Report $report, array $analysisResult): void
    {
        ReportSection::create([
            'report_id' => $report->id,
            'title'     => __('Analyse du sujet'),
            'content'   => $analysisResult['analysis'],
            'order'     => 0,
        ]);

        ReportSection::create([
            'report_id' => $report->id,
            'title'     => __('Public cible'),
            'content'   => $analysisResult['target_audience'],
            'order'     => 1,
        ]);

        ReportSection::create([
            'report_id' => $report->id,
            'title'     => __('Résumé général'),
            'content'   => $analysisResult['summary'],
            'order'     => 2,
        ]);
    }

    public function generateReport(Report $report): void
    {
        try {
            $report->update([
                'status' => 'generating',
                'progress' => 5,
                'current_step' => __('Analyse du sujet et création du plan...')
            ]);

            // Étape 1 : Analyse et Plan détaillé
            Log::info("Generating analysis and outline for report {$report->id}");
            $analysisResult = $this->aiService->generateAnalysis($report->title, $report->subject, $report->language ?? 'fr');

            $report->update([
                'outline' => $analysisResult['chapters'],
                'progress' => 15,
            ]);

            // Étape 2 : Création des sections de préface (Analyse, Public, Résumé)
            $this->createPrefaceSections($report, $analysisResult);

            // Étape 3 : Génération de la couverture
            $report->update([
                'current_step' => __('Génération de l\'image de couverture...')
            ]);
            $coverUrl = $this->imageService->fetchImage($analysisResult['cover_illustration_prompt']);
            if ($coverUrl) {
                $report->update(['cover_image_url' => $coverUrl]);
            }

            $outlineText = collect($analysisResult['chapters'])->map(function ($c) {
                $subs = collect($c['subsections'] ?? [])->map(fn($s) => "- " . $s['title'])->implode("\n");
                return $c['title'] . "\n" . $subs;
            })->implode("\n\n");

            $totalChapters = count($analysisResult['chapters']);
            $totalSubsections = collect($analysisResult['chapters'])->sum(fn($c) => count($c['subsections'] ?? []));
            $totalSteps = $totalChapters + $totalSubsections;
            $currentStep = 0;

            // Étape 4 : Génération du contenu détaillé
            foreach ($analysisResult['chapters'] as $chapterIndex => $chapter) {
                Log::info("Generating content for chapter: {$chapter['title']}");

                $report->update([
                    'current_step' => __('Génération du chapitre : :title...', ['title' => $chapter['title']])
                ]);

                $chapterSection = ReportSection::create([
                    'report_id' => $report->id,
                    'title'     => $chapter['title'],
                    'content'   => '',
                    'order'     => $chapterIndex + 3, // Après les 3 sections de préface
                ]);

                // Illustration du chapitre
                if (!empty($chapter['illustration_prompt'])) {
                    $imageUrl = $this->imageService->fetchImage($chapter['illustration_prompt']);
                    if ($imageUrl) {
                        ReportImage::create([
                            'report_section_id' => $chapterSection->id,
                            'prompt' => $chapter['illustration_prompt'],
                            'image_url' => $imageUrl,
                            'source' => config('images.default'),
                            'order' => 0,
                        ]);
                    }
                }

                $currentStep++;
                $progress = 20 + ($currentStep / $totalSteps) * 75;
                $report->update(['progress' => (int)$progress]);

                // Sous-sections
                if (isset($chapter['subsections']) && is_array($chapter['subsections'])) {
                    foreach ($chapter['subsections'] as $subIndex => $sub) {
                        Log::info("Generating content for sub-section: {$sub['title']}");

                        $report->update([
                            'current_step' => __('Génération de la section : :title...', ['title' => $sub['title']])
                        ]);

                        $subContent = $this->aiService->generateContent(
                            $report->subject,
                            $outlineText,
                            $analysisResult['target_audience'],
                            $analysisResult['summary'],
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
