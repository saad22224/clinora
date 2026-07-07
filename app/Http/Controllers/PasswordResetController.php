<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot password request form.
     */
    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP via WhatsApp to the user's clinic phone number.
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'login_input' => ['required', 'string'],
        ], [
            'login_input.required' => 'يرجى إدخال البريد الإلكتروني أو رقم هاتف العيادة.',
        ]);

        $input = trim($request->input('login_input'));
        $user = User::where('email', $input)->first();

        if (! $user) {
            $clinic = Clinic::where('phone', $input)->first();
            if ($clinic) {
                $user = $clinic->users()->where('role', 'owner')->first() ?? $clinic->users()->first();
            }
        }

        if (! $user || ! $user->clinic || empty($user->clinic->phone)) {
            return back()->withErrors([
                'login_input' => 'لم يتم العثور على حساب مرتبط بهذا البريد الإلكتروني أو رقم هاتف العيادة.',
            ])->onlyInput('login_input');
        }

        $otp = rand(100000, 999999);
        session([
            'password_reset_user_id' => $user->id,
            'password_reset_otp' => $otp,
            'password_reset_phone' => $user->clinic->phone,
            'password_reset_otp_verified' => false,
        ]);

        app(WhatsAppService::class)->sendMessage(
            $user->clinic->phone,
            "مرحباً دكتور {$user->name} 🏥\nكود التحقق (OTP) الخاص بإعادة تعيين كلمة المرور في منصة Clinora هو: *{$otp}*\nيرجى عدم مشاركة هذا الكود مع أي شخص."
        );

        return redirect()->route('password.whatsapp.verify.form');
    }

    /**
     * Show the OTP verification form (Step 2).
     */
    public function showVerifyForm(): View|RedirectResponse
    {
        if (! session('password_reset_user_id')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password-otp');
    }

    /**
     * Verify the OTP code (Step 2 submission).
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string'],
        ], [
            'otp.required' => 'يرجى إدخال كود التحقق.',
        ]);

        $userId = session('password_reset_user_id');
        $savedOtp = session('password_reset_otp');

        if (! $userId || ! $savedOtp || trim($request->otp) !== (string) $savedOtp) {
            return back()->withErrors(['otp' => 'كود التحقق المدخل غير صحيح أو منتهي الصلاحية.']);
        }

        session(['password_reset_otp_verified' => true]);

        return redirect()->route('password.whatsapp.reset.form');
    }

    /**
     * Show the new password form (Step 3).
     */
    public function showResetForm(): View|RedirectResponse
    {
        if (! session('password_reset_user_id') || ! session('password_reset_otp_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password-form');
    }

    /**
     * Reset the user's password (Step 3 submission).
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        if (! session('password_reset_user_id') || ! session('password_reset_otp_verified')) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 8 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $userId = session('password_reset_user_id');
        $user = User::findOrFail($userId);
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        session()->forget([
            'password_reset_user_id',
            'password_reset_otp',
            'password_reset_phone',
            'password_reset_otp_verified',
        ]);

        return redirect()->route('login')->with('success', 'تم تغيير كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول بكلمة المرور الجديدة.');
    }
}
