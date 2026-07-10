<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\ClinicNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $clinics = Clinic::orderBy('name')->get();
        $notifications = ClinicNotification::with('clinic')->latest()->paginate(20);

        return view('admin.notifications.index', compact('clinics', 'notifications'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,danger',
            'clinic_id' => 'nullable',
        ]);

        if (empty($validated['clinic_id']) || $validated['clinic_id'] == 'all') {
            $clinics = Clinic::all();
            foreach ($clinics as $clinic) {
                ClinicNotification::create([
                    'clinic_id' => $clinic->id,
                    'title' => $validated['title'],
                    'message' => $validated['message'],
                    'type' => $validated['type'],
                    'is_read' => false,
                ]);
            }
            $msg = 'تم إرسال الإشعار لجميع العيادات بنجاح ('.$clinics->count().' عيادة).';
        } else {
            ClinicNotification::create([
                'clinic_id' => $validated['clinic_id'],
                'title' => $validated['title'],
                'message' => $validated['message'],
                'type' => $validated['type'],
                'is_read' => false,
            ]);
            $msg = 'تم إرسال الإشعار للعيادة المحددة بنجاح.';
        }

        return redirect()->route('admin.notifications.index')->with('success', $msg);
    }

    public function destroy(ClinicNotification $notification)
    {
        $notification->delete();

        return redirect()->route('admin.notifications.index')->with('success', 'تم حذف الإشعار بنجاح.');
    }
}
