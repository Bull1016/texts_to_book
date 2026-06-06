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
        ]);

        try {
            $report = Report::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'subject' => $validated['subject'],
                'status' => 'pending',
            ]);

            // Dispatch generation (synchronously for MVP)
            $this->reportService->generateReport($report);

            if ($report->status === 'failed') {
                return redirect()->route('reports.show', $report)
                    ->with('warning', 'Le rapport a été créé mais la génération a échoué. Vous pouvez réessayer.');
            }

            return redirect()->route('reports.show', $report)
                ->with('success', 'Rapport créé et généré avec succès !');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création du rapport : ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Une erreur critique est survenue lors de la création du rapport.')
                ->withInput();
        }
    }

    public function show(Report $report): View
    {
        // $this->authorize('view', $report);

        return view('reports.show', [
            'report' => $report,
            'sections' => $report->sections()->with('images')->get(),
        ]);
    }

    public function download(Report $report)
    {
        // $this->authorize('view', $report);

        try {
            if (!$report->pdf_path) {
                $this->exportService->generatePDF($report);
            }

            $path = $report->pdf_path;
            if (!Storage::exists($path)) {
                $this->exportService->generatePDF($report);
            }

            return Storage::download($path, "report-{$report->id}.pdf");
        } catch (\Exception $e) {
            \Log::error('Erreur lors du téléchargement du PDF : ' . $e->getMessage(), [
                'report_id' => $report->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Impossible de générer ou télécharger le fichier PDF pour le moment.');
        }
    }

    public function retry(Report $report): RedirectResponse
    {
        // Only allow retrying failed reports
        if ($report->status !== 'failed') {
            return redirect()->route('reports.show', $report)
                ->with('info', 'Ce rapport ne nécessite pas d\'être relancé.');
        }

        try {
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

            if ($report->status === 'failed') {
                return redirect()->route('reports.show', $report)
                    ->with('error', 'La tentative de relance a encore échoué.');
            }

            return redirect()->route('reports.show', $report)
                ->with('success', 'Le rapport a été relancé avec succès !');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la relance du rapport : ' . $e->getMessage(), [
                'report_id' => $report->id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('reports.show', $report)
                ->with('error', 'Une erreur est survenue lors de la tentative de relance.');
        }
    }

    public function destroy(Report $report): RedirectResponse
    {
        // $this->authorize('delete', $report);

        try {
            if ($report->pdf_path && Storage::exists($report->pdf_path)) {
                Storage::delete($report->pdf_path);
            }

            $report->delete();

            return redirect()->route('reports.index')->with('success', 'Rapport supprimé avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du rapport : ' . $e->getMessage(), [
                'report_id' => $report->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la suppression du rapport.');
        }
    }
}
