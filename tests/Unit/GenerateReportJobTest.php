<?php

namespace Tests\Unit;

use App\Jobs\GenerateReportJob;
use App\Models\Report;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class GenerateReportJobTest extends TestCase
{
    use RefreshDatabase;

    private function createReport(array $overrides = []): Report
    {
        $user = \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        return Report::create(array_merge([
            'user_id' => $user->id,
            'title' => 'Test Report',
            'subject' => 'Test Subject',
            'status' => 'pending',
        ], $overrides));
    }

    // ─── Timeout property ────────────────────────────────────────────────

    public function test_job_timeout_is_set_to_600_seconds()
    {
        $report = $this->createReport();
        $job = new GenerateReportJob($report);

        $this->assertSame(600, $job->timeout);
    }

    public function test_job_timeout_property_is_public()
    {
        $report = $this->createReport();
        $job = new GenerateReportJob($report);

        $reflection = new \ReflectionProperty($job, 'timeout');
        $this->assertTrue($reflection->isPublic());
    }

    // ─── handle() ────────────────────────────────────────────────────────

    public function test_handle_delegates_to_report_service()
    {
        $report = $this->createReport();
        $job = new GenerateReportJob($report);

        $mockService = Mockery::mock(ReportService::class);
        $mockService->shouldReceive('generateReport')
            ->once()
            ->with(Mockery::on(fn($arg) => $arg->id === $report->id));

        $job->handle($mockService);
    }

    public function test_handle_passes_the_correct_report_to_service()
    {
        $report = $this->createReport(['title' => 'Specific Report Title']);
        $job = new GenerateReportJob($report);

        $capturedReport = null;
        $mockService = Mockery::mock(ReportService::class);
        $mockService->shouldReceive('generateReport')
            ->once()
            ->withArgs(function ($arg) use (&$capturedReport) {
                $capturedReport = $arg;
                return true;
            });

        $job->handle($mockService);

        $this->assertEquals($report->id, $capturedReport->id);
        $this->assertEquals('Specific Report Title', $capturedReport->title);
    }

    // ─── failed() ────────────────────────────────────────────────────────

    public function test_failed_sets_report_status_to_failed()
    {
        $report = $this->createReport(['status' => 'processing']);
        $job = new GenerateReportJob($report);

        $exception = new \RuntimeException('Something went wrong');
        $job->failed($exception);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'failed',
        ]);
    }

    public function test_failed_stores_error_message_on_report()
    {
        $report = $this->createReport();
        $job = new GenerateReportJob($report);

        $exception = new \RuntimeException('Detailed error message');
        $job->failed($exception);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'error_message' => 'Detailed error message',
        ]);
    }

    public function test_failed_truncates_error_message_at_500_characters()
    {
        $report = $this->createReport();
        $job = new GenerateReportJob($report);

        $longMessage = str_repeat('a', 600);
        $exception = new \RuntimeException($longMessage);
        $job->failed($exception);

        $report->refresh();
        $this->assertEquals(500, mb_strlen($report->error_message));
    }

    public function test_failed_does_not_truncate_short_error_messages()
    {
        $report = $this->createReport();
        $job = new GenerateReportJob($report);

        $shortMessage = 'Short error';
        $exception = new \RuntimeException($shortMessage);
        $job->failed($exception);

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'error_message' => $shortMessage,
        ]);
    }

    // ─── ShouldQueue contract ─────────────────────────────────────────────

    public function test_job_implements_should_queue_interface()
    {
        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, new GenerateReportJob($this->createReport()));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}