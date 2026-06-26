<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function __construct(protected WhatsAppService $whatsApp) {}

    public function index()
    {
        return view('admin.whatsapp');
    }

    public function status()
    {
        return response()->json($this->whatsApp->getStatus());
    }

    public function logout()
    {
        $res = $this->whatsApp->logout();

        return redirect()->route('admin.whatsapp.index')
            ->with($res ? 'success' : 'error', $res ? 'تم فصل الجلسة وإعادة تشغيل خدمة الواتساب السريعة بنجاح.' : 'حدث خطأ أو أن الخدمة غير مشغلة.');
    }

    public function sendTest(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $res = $this->whatsApp->sendMessage($validated['phone'], $validated['message']);

        return redirect()->route('admin.whatsapp.index')
            ->with($res ? 'success' : 'error', $res ? 'تم إرسال الرسالة التجريبية بنجاح على الواتساب!' : 'فشل إرسال الرسالة. يرجى التأكد من مسح الباركود واتصال الهاتف بالإنترنت.');
    }
}
