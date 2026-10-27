<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Marketer;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

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
            session()->forget(['active_clinic_id', 'active_organization_id']);

            $user = Auth::user();

            // Organization owners must land on the chain dashboard, not a clinic URL.
            if ($user->belongsToOrganization()) {
                return redirect()->route(
                    $user->homeRouteName(),
                    $user->homeRouteParameters()
                );
            }

            return redirect()->intended(route(
                $user->homeRouteName(),
                $user->homeRouteParameters()
            ));
        }

        return back()->withErrors([
            'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
        ])->onlyInput('email');
    }

    public function showRegister(Request $request)
    {
        if ($request->has('ref')) {
            session(['referral_code' => $request->query('ref')]);
        }
        $ref = $request->query('ref', session('referral_code'));

        return view('auth.register', compact('ref'));
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

        $marketerId = null;
        $refCode = $request->input('ref', session('referral_code'));
        if (! empty($refCode)) {
            $marketer = Marketer::where('referral_code', trim($refCode))
                ->where('is_active', true)
                ->first();
            if ($marketer) {
                $marketerId = $marketer->id;
            }
        }

        $user = DB::transaction(function () use ($request, $marketerId) {
            $clinic = Clinic::create([
                'name' => $request->clinic_name,
                'slug' => Clinic::uniqueSlug($request->clinic_name),
                'phone' => $request->phone,
                'marketer_id' => $marketerId,
                'is_active' => true,
            ]);

            $freePlan = Plan::query()
                ->where(function ($q) {
                    $q->where('name', 'free plan')->orWhere('price', 0);
                })
                ->where(function ($q) {
                    $q->where('type', 'single')->orWhereNull('type');
                })
                ->first();

            Subscription::create([
                'clinic_id' => $clinic->id,
                'plan_id' => $freePlan?->id,
                'plan' => $freePlan?->name ?? 'free plan',
                'starts_at' => now(),
                'ends_at' => $freePlan ? now()->addDays($freePlan->duration_in_days) : now()->addDays(30),
                'is_active' => true,
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'clinic_id' => $clinic->id,
                'role' => 'owner',
            ]);

            Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);
            $user->assignRole('clinic_owner');

            return $user;
        });

        if (SystemSetting::isWhatsAppEnabled()) {
            $otp = (string) random_int(100000, 999999);
            session([
                'whatsapp_otp' => $otp,
                'whatsapp_otp_expires_at' => now()->addMinutes(10)->timestamp,
                'pending_verification_user_id' => $user->id,
            ]);
            app(WhatsAppService::class)->sendMessage(
                $request->phone,
                "مرحباً بك في منصة Clinora SaaS 🏥\nكود التحقق (OTP) الخاص بتفعيل حساب عيادتك هو: *{$otp}*"
            );

            return redirect()->route('otp.verify');
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function showVerifyOtp()
    {
        if (! session('pending_verification_user_id')) {
            return redirect()->route('register');
        }

        if (! SystemSetting::isWhatsAppEnabled()) {
            $user = User::findOrFail(session('pending_verification_user_id'));
            Auth::login($user);
            session()->forget(['pending_verification_user_id', 'whatsapp_otp', 'whatsapp_otp_expires_at']);

            return redirect()->route('dashboard');
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
        $expiresAt = session('whatsapp_otp_expires_at');

        if (! $userId || ! $savedOtp || ($expiresAt && now()->timestamp > (int) $expiresAt)) {
            return back()->withErrors(['otp' => 'كود التحقق المدخل غير صحيح أو منتهي الصلاحية.']);
        }

        if (! hash_equals((string) $savedOtp, trim($request->otp))) {
            return back()->withErrors(['otp' => 'كود التحقق المدخل غير صحيح أو منتهي الصلاحية.']);
        }

        $user = User::findOrFail($userId);
        Auth::login($user);

        session()->forget(['pending_verification_user_id', 'whatsapp_otp', 'whatsapp_otp_expires_at']);

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
