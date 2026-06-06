<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\ReportService;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|min:10',
            'language' => 'required|string|in:fr,en,es,de',
        ]);

        $report = Report::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'subject' => $validated['subject'],
            'language' => $validated['language'],
            'status' => 'pending',
        ]);

        // Dispatch generation (synchronously for MVP)
        $this->reportService->generateReport($report);

        return redirect()->route('reports.show', $report);
    }

    public function show(Report $report): View
    {
        // $this->authorize('view', $report);

        return view('reports.show', [
            'report' => $report,
            'sections' => $report->sections()->whereNull('parent_id')->with(['images', 'children.images'])->get(),
        ]);
    }

    public function download(Report $report)
    {
        // $this->authorize('view', $report);

        $this->exportService->generatePDF($report);

        $path = $report->pdf_path;

        return Storage::download($path, "report-{$report->id}.pdf");
    }

    public function retry(Report $report): RedirectResponse
    {
        // Only allow retrying failed reports
        if ($report->status !== 'failed') {
            return redirect()->route('reports.show', $report);
        }

        // Reset the report state
        $report->update([
            'status'        => 'pending',
            'progress'      => 0,
            'error_message' => null,
        ]);

        // Remove any previously generated sections
        $report->sections()->each(function ($section) {
            $section->images()->delete();
            $section->delete();
        });

        // Re-trigger generation on the same report
        $this->reportService->generateReport($report);

        return redirect()->route('reports.show', $report);
    }

    public function destroy(Report $report): RedirectResponse
    {
        // $this->authorize('delete', $report);

        if ($report->pdf_path && Storage::exists($report->pdf_path)) {
            Storage::delete($report->pdf_path);
        }

        $report->delete();

        return redirect()->route('reports.index')->with('success', 'Report deleted successfully');
    }
}
