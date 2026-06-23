<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\ReportService;
use App\Services\ExportService;
use App\Jobs\GenerateReportJob;
use App\Http\Requests\StoreReportRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReportController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private ReportService $reportService,
        private ExportService $exportService
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $reports = $user->reports()->latest()->paginate(10);
        return view('reports.index', ['reports' => $reports]);
    }

    public function create(): View
    {
        return view('reports.create');
    }

    public function store(StoreReportRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $filePath = null;
        $fileType = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('uploads', 'public');
            $fileType = $file->getClientOriginalExtension();
        }

        $report = Report::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'subject' => $validated['subject'] ?? '',
            'file_path' => $filePath,
            'file_type' => $fileType,
            'language' => $validated['language'],
            'status' => 'pending',
        ]);

        try {
            GenerateReportJob::dispatch($report);

            return redirect()->route('reports.show', $report)
                ->with('success', __('Report generation started in the background.'));
        } catch (\Exception $e) {
            return redirect()->route('reports.show', $report)
                ->with('error', __('An error occurred while queueing the report: ') . $e->getMessage());
        }
    }

    public function show(Report $report): View
    {
        $this->authorize('view', $report);

        return view('reports.show', [
            'report' => $report,
            'sections' => $report->sections()->whereNull('parent_id')->with(['images', 'children.images'])->get(),
        ]);
    }

    public function download(Report $report)
    {
        $this->authorize('view', $report);

        if ($report->status !== 'completed') {
            return redirect()->route('reports.show', $report)->with('error', __('The report is not finished yet.'));
        }

        $this->exportService->generatePDF($report);

        $path = $report->pdf_path;

        return Storage::download($path, "report-{$report->id}.pdf");
    }

    public function retry(Report $report): RedirectResponse
    {
        $this->authorize('update', $report);

        // Only allow retrying failed reports
        if ($report->status !== 'failed') {
            return redirect()->route('reports.show', $report);
        }

        // Reset the report state
        $report->update([
            'status'        => 'pending',
            'progress'      => 0,
            'error_message' => null,
            'current_step'  => __('Restarting generation...'),
        ]);

        // Remove any previously generated sections
        $report->sections()->each(function ($section) {
            $section->images()->delete();
            $section->delete();
        });

        try {
            GenerateReportJob::dispatch($report);

            return redirect()->route('reports.show', $report)
                ->with('success', __('Re-generation has started.'));
        } catch (\Exception $e) {
            return redirect()->route('reports.show', $report)
                ->with('error', __('Unexpected error during re-generation: ') . $e->getMessage());
        }
    }

    public function destroy(Report $report): RedirectResponse
    {
        $this->authorize('delete', $report);

        if ($report->pdf_path && Storage::exists($report->pdf_path)) {
            Storage::delete($report->pdf_path);
        }

        $report->delete();

        return redirect()->route('reports.index')->with('success', __('Report deleted successfully'));
    }
}
