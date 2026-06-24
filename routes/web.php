<?php

use App\Http\Controllers\Admin\ClinicController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Clinic\AppointmentController;
use App\Http\Controllers\Clinic\BillingController;
use App\Http\Controllers\Clinic\DashboardController;
use App\Http\Controllers\Clinic\MedicalFileController;
use App\Http\Controllers\Clinic\PatientController;
use App\Http\Controllers\Clinic\PrescriptionController;
use App\Http\Controllers\Clinic\ReportController;
use App\Http\Controllers\Clinic\SettingsController;
use App\Http\Controllers\Clinic\StaffController;
use App\Http\Controllers\Clinic\VisitTypeController;
use App\Http\Controllers\PublicClinicController;
use App\Http\Middleware\CheckSubscriptionStatus;
use App\Http\Middleware\IdentifyTenant;
use Illuminate\Support\Facades\Route;
 
// ==========================================
// Marketing Pages
// ==========================================
Route::get('/', function () {
    return view('pages.landing');
})->name('home');
Route::get('/pricing', function () {
    return view('pages.pricing');
})->name('pricing');
Route::get('/about', function () {
    return view('pages.about');
})->name('about');
Route::get('/contact', function () {
    return view('pages.contact');
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
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'superadmin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('clinic.dashboard');
    })->name('dashboard');
});

// Subscription expired fallback
Route::get('/subscription-expired', function () {
    return view('subscription-expired');
})->name('subscription.expired');

// ==========================================
// Public Clinic Landing Pages  /{slug}
// ==========================================
Route::get('/clinic-page/{slug}', [PublicClinicController::class, 'show'])->name('clinic.public.show');
Route::post('/clinic-page/{slug}/book', [PublicClinicController::class, 'book'])->name('clinic.public.book');

// ==========================================
// Clinic Dashboard  /clinic/*
// ==========================================
Route::middleware(['auth', IdentifyTenant::class, CheckSubscriptionStatus::class])
    ->prefix('clinic')
    ->name('clinic.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Patients CRUD
        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
        Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
        Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');

        // Appointments CRUD
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.update-status');
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

        // Booking Requests (from public page)
        Route::get('/bookings', [SettingsController::class, 'bookingRequests'])->name('bookings.index');
        Route::patch('/bookings/{booking}/status', [SettingsController::class, 'updateBookingStatus'])->name('bookings.update-status');

        // Settings / Clinic Profile
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // Visit Types
        Route::get('/visit-types', [VisitTypeController::class, 'index'])->name('visit-types.index');
        Route::post('/visit-types', [VisitTypeController::class, 'store'])->name('visit-types.store');
        Route::put('/visit-types/{visitType}', [VisitTypeController::class, 'update'])->name('visit-types.update');
        Route::delete('/visit-types/{visitType}', [VisitTypeController::class, 'destroy'])->name('visit-types.destroy');

        // Prescriptions
        Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
        Route::post('/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
        Route::delete('/prescriptions/{prescription}', [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');

        // Medical Records
        Route::get('/records', [MedicalFileController::class, 'index'])->name('records.index');
        Route::post('/records', [MedicalFileController::class, 'store'])->name('records.store');
        Route::delete('/records/{medicalFile}', [MedicalFileController::class, 'destroy'])->name('records.destroy');

        // Billing
        Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        // Staff
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    });

// ==========================================
// Super Admin Dashboard  /admin/*
// ==========================================
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Clinics CRUD
        Route::get('/clinics', [ClinicController::class, 'index'])->name('clinics.index');
        Route::post('/clinics', [ClinicController::class, 'store'])->name('clinics.store');
        Route::patch('/clinics/{clinic}/toggle', [ClinicController::class, 'toggle'])->name('clinics.toggle');
        Route::delete('/clinics/{clinic}', [ClinicController::class, 'destroy'])->name('clinics.destroy');

        // Plans CRUD
        Route::get('/subscriptions', function () {
            return view('admin.subscriptions');
        })->name('subscriptions.index');
        Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');
        Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
        Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');

        // Other pages
        Route::get('/revenue', function () {
            return view('admin.revenue');
        })->name('revenue.index');
        Route::get('/tickets', function () {
            return view('admin.tickets');
        })->name('tickets.index');
        Route::get('/users', function () {
            return view('admin.users');
        })->name('users.index');
        Route::get('/roles', function () {
            return view('admin.roles');
        })->name('roles.index');
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('settings.index');
        Route::get('/logs', function () {
            return view('admin.logs');
        })->name('logs.index');
    });
