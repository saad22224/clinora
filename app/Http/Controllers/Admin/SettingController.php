<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $whatsappEnabled = SystemSetting::isWhatsAppEnabled();

        return view('admin.settings', compact('whatsappEnabled'));
    }

    public function update(Request $request)
    {
        $whatsappEnabled = $request->boolean('whatsapp_enabled') ? '1' : '0';
        SystemSetting::set(
            'whatsapp_enabled',
            $whatsappEnabled,
            'تفعيل أو تعطيل كافة ميزات وإشعارات الواتساب في المنصة'
        );

        return back()->with('success', 'تم حفظ وتحديث إعدادات المنصة بنجاح!');
    }
}
