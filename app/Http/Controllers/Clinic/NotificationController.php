<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\ClinicNotification;

class NotificationController extends Controller
{
    public function index()
    {
        $clinic = $this->currentClinic();

        $notifications = ClinicNotification::where('clinic_id', $clinic->id)
            ->latest()
            ->paginate(15);

        return view('clinic.notifications.index', compact('notifications'));
    }

    public function markAllRead()
    {
        $clinic = $this->currentClinic();
        if ($clinic) {
            ClinicNotification::where('clinic_id', $clinic->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return redirect()->back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة.');
    }

    public function markRead(ClinicNotification $notification)
    {
        if ($notification->clinic_id === $this->currentClinicId()) {
            $notification->update(['is_read' => true]);
        }

        return redirect()->back()->with('success', 'تم تحديد الإشعار كمقروء.');
    }
}
