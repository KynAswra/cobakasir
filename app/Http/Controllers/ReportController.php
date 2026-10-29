<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function index(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $report = $this->reportService->getReportData($from, $to);

        return view('reports.index', array_merge($report, [
            'dateFrom' => $from->format('Y-m-d'),
            'dateTo'   => $to->format('Y-m-d'),
        ]));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->parseDateRange($request);
        $report = $this->reportService->getReportData($from, $to);
        $csv = $this->reportService->toCsv($report);

        $filename = 'laporan-gorengan-' . $from->format('Y-m-d') . '-' . $to->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportGoogleSheets(Request $request)
    {
        [$from, $to] = $this->parseDateRange($request);
        $report = $this->reportService->getReportData($from, $to);
        $csv = $this->reportService->toCsv($report);
        $filename = 'laporan-gorengan-' . $from->format('Y-m-d') . '-' . $to->format('Y-m-d') . '.csv';

        return view('reports.export-sheets', compact('csv', 'filename', 'from', 'to'));
    }

    private function parseDateRange(Request $request): array
    {
        $from = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $to = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : now()->endOfMonth()->endOfDay();

        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        return [$from, $to];
    }
}
