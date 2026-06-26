<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Subscription;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'clinic_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // Create Clinic
        $slug = Str::slug($request->clinic_name);
        // Ensure slug is unique
        $originalSlug = $slug;
        $count = 1;
        while (Clinic::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count;
            $count++;
        }

        $clinic = Clinic::create([
            'name' => $request->clinic_name,
            'slug' => $slug,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        // Send WhatsApp Registration OTP
        $otp = rand(100000, 999999);
        session(['whatsapp_otp' => $otp]);
        app(WhatsAppService::class)->sendMessage(
            $request->phone,
            "مرحباً بك في منصة Clinora SaaS 🏥\nكود التحقق (OTP) الخاص بتفعيل حساب عيادتك هو: *{$otp}*"
        );

        // Create Free Subscription
        Subscription::create([
            'clinic_id' => $clinic->id,
            'plan' => 'free',
            'starts_at' => now(),
            'ends_at' => now()->addDays(14), // 14 days trial
            'is_active' => true,
        ]);

        // Create User (Owner)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'clinic_id' => $clinic->id,
            'role' => 'owner',
        ]);

        session(['pending_verification_user_id' => $user->id]);

        return redirect()->route('otp.verify');
    }

    public function showVerifyOtp()
    {
        if (!session('pending_verification_user_id')) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string'],
        ]);

        $userId = session('pending_verification_user_id');
        $savedOtp = session('whatsapp_otp');

        if (!$userId || !$savedOtp || trim($request->otp) !== (string) $savedOtp) {
            return back()->withErrors(['otp' => 'كود التحقق المدخل غير صحيح أو منتهي الصلاحية.']);
        }

        $user = User::findOrFail($userId);
        Auth::login($user);

        session()->forget(['pending_verification_user_id', 'whatsapp_otp']);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
