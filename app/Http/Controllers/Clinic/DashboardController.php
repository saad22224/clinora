<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $clinic = Auth::user()->clinic;
        $clinicId = $clinic->id;

        $filter = $request->query('filter', '1');
        [$startDate, $label] = match ($filter) {
            '3' => [Carbon::now()->subMonths(3)->startOfMonth(), 'إيرادات آخر 3 أشهر'],
            '2' => [Carbon::now()->subMonths(2)->startOfMonth(), 'إيرادات آخر شهرين'],
            default => [Carbon::now()->startOfMonth(), 'إيرادات هذا الشهر'],
        };
        $filter = in_array($filter, ['1', '2', '3']) ? $filter : '1';

        // Revenue from completed appointments in date range
        $revenue = Appointment::where('clinic_id', $clinicId)
            ->where('status', 'completed')
            ->where('scheduled_at', '>=', $startDate)
            ->sum('price');

        $base = Appointment::where('clinic_id', $clinicId);

        $stats = [
            'total_patients' => Patient::where('clinic_id', $clinicId)->count(),
            'today_appointments' => (clone $base)->whereDate('scheduled_at', Carbon::today())->count(),
            'pending_appointments' => (clone $base)->where('status', 'pending')->count(),
            'confirmed_appointments' => (clone $base)->where('status', 'confirmed')->count(),
            'completed_appointments' => (clone $base)->where('status', 'completed')->count(),
            'cancelled_appointments' => (clone $base)->whereIn('status', ['cancelled', 'rejected'])->whereMonth('scheduled_at', Carbon::now()->month)->count(),
            'revenue_label' => $label,
            'revenue_amount' => $revenue,
            'current_filter' => $filter,
        ];

        $upcoming_appointments = Appointment::with(['patient', 'visitType'])
            ->where('clinic_id', $clinicId)
            ->whereDate('scheduled_at', '>=', Carbon::today())
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('scheduled_at', 'asc')
            ->take(6)
            ->get();

        // Recent activity: last 5 created appointments
        $recent_appointments = Appointment::with(['patient', 'visitType'])
            ->where('clinic_id', $clinicId)
            ->latest()
            ->take(5)
            ->get();

        return view('clinic.dashboard', compact('stats', 'upcoming_appointments', 'recent_appointments', 'clinic'));
    }
}
