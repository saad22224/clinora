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
    public function index()
    {
        // Sample stats for the beautiful dashboard
        $stats = [
            'total_patients' => Patient::count(),
            'today_appointments' => Appointment::whereDate('scheduled_at', Carbon::today())->count(),
            'monthly_revenue' => Finance::where('type', 'income')->whereMonth('transaction_date', Carbon::now()->month)->sum('amount'),
            'cancelled_appointments' => Appointment::where('status', 'cancelled')->whereMonth('scheduled_at', Carbon::now()->month)->count(),
        ];
        
        $upcoming_appointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('scheduled_at', '>=', Carbon::today())
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        return view('clinic.dashboard', compact('stats', 'upcoming_appointments'));
    }
}
