<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Finance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $clinic = \Illuminate\Support\Facades\Auth::user()->clinic;
        
        $filter = $request->query('filter', '1'); // Default 1 month
        $startDate = Carbon::now()->subMonths((int)$filter)->startOfMonth();
        if ($filter == '1') {
            $startDate = Carbon::now()->startOfMonth();
            $label = 'إيرادات الشهر';
        } elseif ($filter == '2') {
            $label = 'إيرادات آخر شهرين';
        } elseif ($filter == '3') {
            $label = 'إيرادات آخر 3 أشهر';
        } else {
            $startDate = Carbon::now()->startOfMonth();
            $label = 'إيرادات الشهر';
            $filter = '1';
        }

        // Calculate revenue from completed appointments
        $revenue = Appointment::where('clinic_id', $clinic->id)
            ->where('status', 'completed')
            ->where('scheduled_at', '>=', $startDate)
            ->sum('price');

        // Sample stats for the beautiful dashboard
        $stats = [
            'total_patients' => Patient::where('clinic_id', $clinic->id)->count(),
            'today_appointments' => Appointment::where('clinic_id', $clinic->id)->whereDate('scheduled_at', Carbon::today())->count(),
            'revenue_label' => $label,
            'revenue_amount' => $revenue,
            'current_filter' => $filter,
            'cancelled_appointments' => Appointment::where('clinic_id', $clinic->id)->where('status', 'cancelled')->whereMonth('scheduled_at', Carbon::now()->month)->count(),
        ];
        
        $upcoming_appointments = Appointment::with(['patient', 'doctor'])
            ->where('clinic_id', $clinic->id)
            ->whereDate('scheduled_at', '>=', Carbon::today())
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        return view('clinic.dashboard', compact('stats', 'upcoming_appointments'));
    }
}
