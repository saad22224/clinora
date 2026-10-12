<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Services\FinanceReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected FinanceReportService $financeReportService) {}

    public function index(Request $request)
    {
        $clinic = $this->currentClinic();
        $clinicId = $clinic->id;

        $filter = $request->query('filter', '1');
        [$startDate, $label] = match ($filter) {
            '3' => [Carbon::now()->subMonths(3)->startOfMonth(), 'الملخص المالي لآخر 3 أشهر'],
            '2' => [Carbon::now()->subMonths(2)->startOfMonth(), 'الملخص المالي لآخر شهرين'],
            default => [Carbon::now()->startOfMonth(), 'الملخص المالي لهذا الشهر'],
        };
        $filter = in_array($filter, ['1', '2', '3'], true) ? $filter : '1';

        $metrics = $this->financeReportService->forClinic($clinic, $startDate, now()->endOfDay());

        $base = Appointment::where('clinic_id', $clinicId);

        $stats = [
            'total_patients' => Patient::where('clinic_id', $clinicId)->count(),
            'today_appointments' => (clone $base)->whereDate('scheduled_at', Carbon::today())->count(),
            'pending_appointments' => (clone $base)->where('status', 'pending')->count(),
            'confirmed_appointments' => (clone $base)->where('status', 'confirmed')->count(),
            'completed_appointments' => (clone $base)->where('status', 'completed')->count(),
            'cancelled_appointments' => (clone $base)->whereIn('status', ['cancelled', 'rejected'])->whereMonth('scheduled_at', Carbon::now()->month)->count(),
            'revenue_label' => $label,
            'collected_income' => $metrics['collected_income'],
            'expenses' => $metrics['expenses'],
            'net_cash' => $metrics['net_cash'],
            'completed_service_value' => $metrics['completed_service_value'],
            'outstanding' => $metrics['outstanding'],
            'current_filter' => $filter,
        ];

        $upcoming_appointments = Appointment::with(['patient', 'visitType'])
            ->where('clinic_id', $clinicId)
            ->whereDate('scheduled_at', '>=', Carbon::today())
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('scheduled_at', 'asc')
            ->take(6)
            ->get();

        $recent_appointments = Appointment::with(['patient', 'visitType'])
            ->where('clinic_id', $clinicId)
            ->latest()
            ->take(5)
            ->get();

        return view('clinic.dashboard', compact('stats', 'upcoming_appointments', 'recent_appointments', 'clinic'));
    }
}
