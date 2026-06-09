<?php

namespace App\Jobs;

use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Report $report)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(ReportService $reportService): void
    {
        Log::info("Starting background generation for report {$this->report->id}");
        $reportService->generateReport($this->report);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateReportJob failed for report {$this->report->id}", ['error' => $exception->getMessage()]);
        $this->report->update([
            'status' => 'failed',
            'error_message' => mb_substr($exception->getMessage(), 0, 500),
            'current_step' => __('Error during generation'),
        ]);
    }
}
