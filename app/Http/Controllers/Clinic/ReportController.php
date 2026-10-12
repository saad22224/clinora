<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Services\FinanceReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected FinanceReportService $financeReportService) {}

    public function index(Request $request)
    {
        $clinic = $this->currentClinic();

        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $from = isset($validated['date_from'])
            ? Carbon::parse($validated['date_from'])->startOfDay()
            : now()->startOfMonth();
        $to = isset($validated['date_to'])
            ? Carbon::parse($validated['date_to'])->endOfDay()
            : now()->endOfDay();

        $metrics = $this->financeReportService->forClinic($clinic, $from, $to);

        return view('clinic.reports', [
            'clinic' => $clinic,
            'metrics' => $metrics,
            'date_from' => $from->toDateString(),
            'date_to' => $to->toDateString(),
        ]);
    }
}
