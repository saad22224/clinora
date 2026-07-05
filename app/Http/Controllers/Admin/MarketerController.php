<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MarketerController extends Controller
{
    public function index(): View
    {
        $marketers = Marketer::withCount('clinics')->latest()->get();

        return view('admin.marketers', compact('marketers'));
    }

    public function show(Marketer $marketer): View
    {
        $clinics = $marketer->clinics()->with(['users', 'subscription'])->latest()->paginate(15);

        return view('admin.marketer-clinics', compact('marketer', 'clinics'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'referral_code' => ['nullable', 'string', 'max:50', 'unique:marketers,referral_code'],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        if (empty($validated['referral_code'])) {
            $validated['referral_code'] = strtoupper(Str::random(8));
        } else {
            $validated['referral_code'] = strtoupper(trim($validated['referral_code']));
        }

        Marketer::create($validated);

        return back()->with('success', 'تم إضافة المسوق بنجاح وكود الإحالة جاهز للاستخدام!');
    }

    public function update(Request $request, Marketer $marketer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'referral_code' => ['required', 'string', 'max:50', 'unique:marketers,referral_code,'.$marketer->id],
            'commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['referral_code'] = strtoupper(trim($validated['referral_code']));
        $marketer->update($validated);

        return back()->with('success', 'تم تحديث بيانات المسوق بنجاح.');
    }

    public function destroy(Marketer $marketer)
    {
        $marketer->delete();

        return back()->with('success', 'تم حذف المسوق بنجاح.');
    }
}
