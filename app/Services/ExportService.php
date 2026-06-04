<?php

namespace App\Services;

use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExportService
{
    public function generatePDF(Report $report): string
    {
        try {
            $pdf = Pdf::loadView('exports.report', [
                'report' => $report,
                'sections' => $report->sections()->with('images')->get(),
            ]);

            $filename = "report-{$report->id}-" . now()->timestamp . '.pdf';
            $path = "reports/{$filename}";

            Storage::disk('local')->put($path, $pdf->output());

            $report->update(['pdf_path' => $path]);

            Log::info("PDF generated for report {$report->id}: {$path}");

            return $path;
        } catch (\Exception $e) {
            Log::error("PDF generation failed for report {$report->id}", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function downloadPDF(Report $report): string
    {
        if (!$report->pdf_path) {
            throw new \Exception('No PDF has been generated for this report');
        }

        return $report->pdf_path;
    }
}
