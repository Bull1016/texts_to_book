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
        $reports = auth()->user()->reports()->latest()->paginate(10);
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
        ]);

        $report = Report::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'subject' => $validated['subject'],
            'status' => 'pending',
        ]);

        // Dispatch generation (synchronously for MVP)
        $this->reportService->generateReport($report);

        return redirect()->route('reports.show', $report);
    }

    public function show(Report $report): View
    {
        $this->authorize('view', $report);

        return view('reports.show', [
            'report' => $report,
            'sections' => $report->sections()->with('images')->get(),
        ]);
    }

    public function download(Report $report)
    {
        $this->authorize('view', $report);

        if (!$report->pdf_path) {
            $this->exportService->generatePDF($report);
        }

        $path = $report->pdf_path;
        if (!Storage::exists($path)) {
            $this->exportService->generatePDF($report);
        }

        return Storage::download($path, "report-{$report->id}.pdf");
    }

    public function destroy(Report $report): RedirectResponse
    {
        $this->authorize('delete', $report);

        if ($report->pdf_path && Storage::exists($report->pdf_path)) {
            Storage::delete($report->pdf_path);
        }

        $report->delete();

        return redirect()->route('reports.index')->with('success', 'Report deleted successfully');
    }
}
