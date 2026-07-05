<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\ClinicNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $clinic = Auth::user()->clinic;

        $notifications = ClinicNotification::where('clinic_id', $clinic->id)
            ->latest()
            ->paginate(15);

        return view('clinic.notifications.index', compact('notifications'));
    }

    public function markAllRead()
    {
        $clinic = Auth::user()->clinic;
        if ($clinic) {
            ClinicNotification::where('clinic_id', $clinic->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return redirect()->back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة.');
    }

    public function markRead(ClinicNotification $notification)
    {
        if ($notification->clinic_id === Auth::user()->clinic_id) {
            $notification->update(['is_read' => true]);
        }

        return redirect()->back()->with('success', 'تم تحديد الإشعار كمقروء.');
    }
}
