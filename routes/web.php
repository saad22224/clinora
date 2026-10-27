<?php

use App\Http\Controllers\Admin\ClinicController;
use App\Http\Controllers\Admin\MarketerController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\OrganizationController as AdminOrganizationController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\WhatsAppController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Clinic\AppointmentController;
use App\Http\Controllers\Clinic\BillingController;
use App\Http\Controllers\Clinic\DashboardController;
use App\Http\Controllers\Clinic\MedicalFileController;
use App\Http\Controllers\Clinic\NotificationController as ClinicNotificationController;
use App\Http\Controllers\Clinic\PatientAIChatController;
use App\Http\Controllers\Clinic\PatientController;
use App\Http\Controllers\Clinic\PatientDiagnosisController;
use App\Http\Controllers\Clinic\PrescriptionController;
use App\Http\Controllers\Clinic\ReportController;
use App\Http\Controllers\Clinic\SettingsController;
use App\Http\Controllers\Clinic\StaffController;
use App\Http\Controllers\Clinic\StaffMessageController;
use App\Http\Controllers\Clinic\VisitTypeController;
use App\Http\Controllers\Organization\ClinicSwitchController;
use App\Http\Controllers\Organization\DashboardController as OrganizationDashboardController;
use App\Http\Controllers\Organization\OrganizationClinicController;
use App\Http\Controllers\Organization\ReportController as OrganizationReportController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PublicClinicController;
use App\Http\Middleware\CheckSubscriptionStatus;
use App\Http\Middleware\IdentifyTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==========================================
// Marketing Pages
// ==========================================
Route::get('/', function (Request $request) {
    if ($request->has('ref')) {
        session(['referral_code' => $request->query('ref')]);
    }

    return view('pages.landing');
})->name('home');
Route::get('/pricing', function () {
    return redirect()->to(url('/#pricing'));
})->name('pricing');
Route::get('/about', function () {
    return redirect()->to(url('/#about'));
})->name('about');
Route::get('/contact', function () {
    return redirect()->to(url('/#contact'));
})->name('contact');
Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');
Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

// ==========================================
// Authentication
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.post');
    Route::get('/verify-otp', [AuthController::class, 'showVerifyOtp'])->name('otp.verify');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:10,1')->name('otp.verify.post');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendOtp'])->middleware('throttle:5,1')->name('password.whatsapp.send');
    Route::get('/verify-password-otp', [PasswordResetController::class, 'showVerifyForm'])->name('password.whatsapp.verify.form');
    Route::post('/verify-password-otp', [PasswordResetController::class, 'verifyOtp'])->middleware('throttle:10,1')->name('password.whatsapp.verify');
    Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.whatsapp.reset.form');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->middleware('throttle:5,1')->name('password.whatsapp.reset');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        $user = auth()->user();

        return redirect()->route($user->homeRouteName(), $user->homeRouteParameters());
    })->name('dashboard');

    Route::get('/leave-impersonation', [ClinicController::class, 'leaveImpersonation'])->name('impersonate.leave');
});

// Subscription expired fallback
Route::get('/subscription-expired', function () {
    return view('subscription-expired');
})->name('subscription.expired');

// ==========================================
// Public Clinic Landing Pages
// ==========================================
Route::get('/clinic-page/{slug}', [PublicClinicController::class, 'show'])->name('clinic.public.show');
Route::post('/clinic-page/{slug}/book', [PublicClinicController::class, 'book'])->middleware('throttle:20,1')->name('clinic.public.book');
Route::get('/clinic-page/{slug}/booking/{token}', [PublicClinicController::class, 'bookingConfirm'])
    ->middleware('throttle:60,1')
    ->name('clinic.public.booking.confirm');
Route::get('/clinic-page/{slug}/booking/{token}/track', [PublicClinicController::class, 'bookingTrack'])
    ->middleware('throttle:60,1')
    ->name('clinic.public.booking.track');
Route::get('/clinic-page/{slug}/booking/{token}/status', [PublicClinicController::class, 'bookingStatus'])
    ->middleware('throttle:120,1')
    ->name('clinic.public.booking.status');

// ==========================================
// Organization Dashboard
// ==========================================
Route::middleware(['auth', 'organization.member'])
    ->prefix('organization/{organization}')
    ->name('organization.')
    ->group(function () {
        Route::get('/dashboard', [OrganizationDashboardController::class, 'index'])->name('dashboard');
        Route::get('/reports', [OrganizationReportController::class, 'index'])->name('reports.index');
        Route::post('/clinics', [OrganizationClinicController::class, 'store'])->name('clinics.store');
        Route::get('/clinics/{clinic}', [OrganizationClinicController::class, 'show'])->name('clinics.show');
        Route::put('/clinics/{clinic}', [OrganizationClinicController::class, 'update'])->name('clinics.update');
        Route::delete('/clinics/{clinic}', [OrganizationClinicController::class, 'destroy'])->name('clinics.destroy');
        Route::post('/clinics/{clinic}/enter', [ClinicSwitchController::class, 'enter'])->name('clinics.enter');
        Route::post('/leave-clinic', [ClinicSwitchController::class, 'leave'])->name('clinics.leave');
    });

// ==========================================
// Clinic Dashboard  /clinic/*
// ==========================================
Route::middleware(['auth', IdentifyTenant::class, CheckSubscriptionStatus::class])
    ->prefix('clinic')
    ->name('clinic.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::middleware('clinic.permission:manage_patients')->group(function () {
            Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
            Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
            Route::get('/patients/{patient}/medical-file', [PatientController::class, 'medicalFile'])->name('patients.medical-file');
            Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
            Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
            Route::post('/patients/{patient}/diagnoses', [PatientDiagnosisController::class, 'store'])->name('patients.diagnoses.store');
            Route::delete('/patients/{patient}/diagnoses/{diagnosis}', [PatientDiagnosisController::class, 'destroy'])->name('patients.diagnoses.destroy');
            Route::post('/patients/{patient}/ai-chat', [PatientAIChatController::class, 'chat'])->name('patients.ai-chat');
        });

        Route::middleware('clinic.permission:manage_appointments')->group(function () {
            Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
            Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
            Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');
            Route::post('/appointments/{appointment}/queue', [AppointmentController::class, 'queueAction'])->name('appointments.queue-action');
            Route::post('/appointments/queue/call-next', [AppointmentController::class, 'callNext'])->name('appointments.call-next');
            Route::post('/appointments/queue/pause', [AppointmentController::class, 'pauseQueue'])->name('appointments.queue-pause');
            Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
        });

        Route::middleware('clinic.permission:use_staff_messages')->group(function () {
            Route::get('/messages', [StaffMessageController::class, 'index'])->name('messages.index');
            Route::post('/messages', [StaffMessageController::class, 'store'])->middleware('throttle:30,1')->name('messages.store');
        });

        Route::middleware('clinic.permission:manage_settings')->group(function () {
            Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
            Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
            Route::get('/visit-types', [VisitTypeController::class, 'index'])->name('visit-types.index');
            Route::post('/visit-types', [VisitTypeController::class, 'store'])->name('visit-types.store');
            Route::put('/visit-types/{visitType}', [VisitTypeController::class, 'update'])->name('visit-types.update');
            Route::delete('/visit-types/{visitType}', [VisitTypeController::class, 'destroy'])->name('visit-types.destroy');
        });

        Route::middleware('clinic.permission:manage_prescriptions')->group(function () {
            Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
            Route::get('/drugs/search', [PrescriptionController::class, 'searchDrugs'])->name('drugs.search');
            Route::post('/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
            Route::get('/prescriptions/{prescription}/print', [PrescriptionController::class, 'print'])->name('prescriptions.print');
            Route::delete('/prescriptions/{prescription}', [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');
        });

        Route::middleware('clinic.permission:manage_records')->group(function () {
            Route::get('/records', [MedicalFileController::class, 'index'])->name('records.index');
            Route::post('/records', [MedicalFileController::class, 'store'])->name('records.store');
            Route::get('/records/{medicalFile}/download', [MedicalFileController::class, 'download'])->name('records.download');
            Route::get('/records/{medicalFile}/preview', [MedicalFileController::class, 'preview'])->name('records.preview');
            Route::delete('/records/{medicalFile}', [MedicalFileController::class, 'destroy'])->name('records.destroy');
        });

        Route::middleware('clinic.permission:manage_billing')->group(function () {
            Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
            Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
            Route::put('/billing/{finance}', [BillingController::class, 'update'])->name('billing.update');
            Route::delete('/billing/{finance}', [BillingController::class, 'destroy'])->name('billing.destroy');
            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        });

        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::put('/staff/{user}', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{user}', [StaffController::class, 'destroy'])->name('staff.destroy');

        Route::get('/notifications', [ClinicNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/mark-all-read', [ClinicNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
        Route::post('/notifications/{notification}/read', [ClinicNotificationController::class, 'markRead'])->name('notifications.read');
    });

// ==========================================
// Super Admin Dashboard  /admin/*
// ==========================================
Route::middleware(['auth', 'superadmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/clinics', [ClinicController::class, 'index'])->name('clinics.index');
        Route::post('/clinics', [ClinicController::class, 'store'])->name('clinics.store');
        Route::get('/clinics/{clinic}/impersonate', [ClinicController::class, 'impersonate'])->name('clinics.impersonate');
        Route::patch('/clinics/{clinic}/toggle', [ClinicController::class, 'toggle'])->name('clinics.toggle');
        Route::delete('/clinics/{clinic}', [ClinicController::class, 'destroy'])->name('clinics.destroy');

        Route::get('/organizations', [AdminOrganizationController::class, 'index'])->name('organizations.index');
        Route::post('/organizations', [AdminOrganizationController::class, 'store'])->name('organizations.store');
        Route::get('/organizations/{organization}', [AdminOrganizationController::class, 'show'])->name('organizations.show');
        Route::patch('/organizations/{organization}/toggle', [AdminOrganizationController::class, 'toggle'])->name('organizations.toggle');
        Route::post('/organizations/{organization}/assign-plan', [AdminOrganizationController::class, 'assignPlan'])->name('organizations.assign-plan');

        Route::get('/marketers', [MarketerController::class, 'index'])->name('marketers.index');
        Route::get('/marketers/{marketer}', [MarketerController::class, 'show'])->name('marketers.show');
        Route::post('/marketers', [MarketerController::class, 'store'])->name('marketers.store');
        Route::put('/marketers/{marketer}', [MarketerController::class, 'update'])->name('marketers.update');
        Route::delete('/marketers/{marketer}', [MarketerController::class, 'destroy'])->name('marketers.destroy');

        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('/subscriptions/assign', [SubscriptionController::class, 'assign'])->name('subscriptions.assign');
        Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');
        Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
        Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');

        Route::get('/revenue', function () {
            abort_unless(config('clinora.features.admin_placeholder_pages', false), 404);

            return view('admin.revenue');
        })->name('revenue.index');
        Route::get('/tickets', function () {
            abort_unless(config('clinora.features.admin_placeholder_pages', false), 404);

            return view('admin.tickets');
        })->name('tickets.index');
        Route::get('/users', function () {
            abort_unless(config('clinora.features.admin_placeholder_pages', false), 404);

            return view('admin.users');
        })->name('users.index');
        Route::get('/roles', function () {
            abort_unless(config('clinora.features.admin_placeholder_pages', false), 404);

            return view('admin.roles');
        })->name('roles.index');
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
        Route::get('/logs', function () {
            abort_unless(config('clinora.features.admin_placeholder_pages', false), 404);

            return view('admin.logs');
        })->name('logs.index');

        Route::get('/whatsapp', [WhatsAppController::class, 'index'])->name('whatsapp.index');
        Route::get('/whatsapp/status', [WhatsAppController::class, 'status'])->name('whatsapp.status');
        Route::post('/whatsapp/logout', [WhatsAppController::class, 'logout'])->name('whatsapp.logout');
        Route::post('/whatsapp/test', [WhatsAppController::class, 'sendTest'])->name('whatsapp.test');
        Route::post('/whatsapp/toggle-setting', [AdminSettingController::class, 'update'])->name('whatsapp.toggle-setting');

        Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');
        Route::delete('/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');
    });
