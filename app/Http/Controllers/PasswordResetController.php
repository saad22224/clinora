<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot password request form.
     */
    public function showForgotForm(): View
    {
        $isWhatsAppEnabled = SystemSetting::isWhatsAppEnabled();

        return view('auth.forgot-password', compact('isWhatsAppEnabled'));
    }

    /**
     * Send OTP via WhatsApp to the user's clinic phone number.
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        $isWhatsAppEnabled = SystemSetting::isWhatsAppEnabled();

        if (! $isWhatsAppEnabled) {
            $request->validate([
                'login_input' => ['required', 'email'],
            ], [
                'login_input.required' => 'يرجى إدخال البريد الإلكتروني.',
                'login_input.email' => 'يرجى إدخال بريد إلكتروني صحيح لأن استعادة كلمة المرور تتم عبر البريد فقط حالياً.',
            ]);
        } else {
            $request->validate([
                'login_input' => ['required', 'string'],
            ], [
                'login_input.required' => 'يرجى إدخال البريد الإلكتروني أو رقم الواتساب.',
            ]);
        }

        $input = trim($request->input('login_input'));

        if (! $isWhatsAppEnabled) {
            $user = User::where('email', $input)->first();
            if (! $user) {
                return back()->withErrors([
                    'login_input' => 'لم يتم العثور على حساب مسجل بهذا البريد الإلكتروني.',
                ])->onlyInput('login_input');
            }
            $targetContact = $user->email;
        } else {
            $user = User::where('email', $input)->orWhere('phone', $input)->first();

            if (! $user) {
                $clinic = Clinic::where('phone', $input)->first();
                if ($clinic) {
                    $user = $clinic->users()->where('role', 'owner')->first() ?? $clinic->users()->first();
                }
            }

            $targetPhone = $user?->phone ?: ($user?->clinic?->phone ?: null);

            if (! $user || empty($targetPhone)) {
                return back()->withErrors([
                    'login_input' => 'لم يتم العثور على حساب أو رقم هاتف واتساب مرتبط بهذا الحساب أو بالعيادة.',
                ])->onlyInput('login_input');
            }
            $targetContact = $targetPhone;
        }

        $otp = rand(100000, 999999);
        session([
            'password_reset_user_id' => $user->id,
            'password_reset_otp' => $otp,
            'password_reset_phone' => $targetContact,
            'password_reset_otp_verified' => false,
        ]);

        if ($isWhatsAppEnabled) {
            app(WhatsAppService::class)->sendMessage(
                $targetContact,
                "مرحباً {$user->name} 🏥\nكود التحقق (OTP) الخاص بإعادة تعيين كلمة المرور في منصة Clinora هو: *{$otp}*\nيرجى عدم مشاركة هذا الكود مع أي شخص."
            );
        } else {
            try {
                Mail::raw(
                    "مرحباً {$user->name} 🏥\n\nكود التحقق (OTP) الخاص بإعادة تعيين كلمة المرور في منصة Clinora هو:\n{$otp}\n\nيرجى عدم مشاركة هذا الكود مع أي شخص.\n\nفريق دعم Clinora SaaS",
                    function ($message) use ($user) {
                        $message->to($user->email, $user->name)
                            ->subject('كود التحقق لاستعادة كلمة المرور - Clinora');
                    }
                );
            } catch (\Exception $e) {
                Log::error('Password reset email sending error: '.$e->getMessage());
            }
        }

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

        $isWhatsAppEnabled = SystemSetting::isWhatsAppEnabled();

        return view('auth.reset-password-otp', compact('isWhatsAppEnabled'));
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

        if (! $userId || ! $savedOtp || trim($request->otp) != (string) $savedOtp) {
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
