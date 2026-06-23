<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\Subscription;

class DashboardController extends Controller
{
    public function index()
    {
        // Super Admin Stats
        $stats = [
            'total_clinics' => Clinic::count(),
            'active_subscriptions' => Subscription::where('is_active', true)->count(),
            'total_revenue' => 0, // Placeholder
            'new_clinics_this_month' => Clinic::whereMonth('created_at', now()->month)->count(),
        ];
        
        $recent_clinics = Clinic::with('subscription')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_clinics'));
    }
}
