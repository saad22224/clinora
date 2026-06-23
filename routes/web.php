<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Marketing Pages
Route::get('/', function () { return view('pages.landing'); })->name('home');
Route::get('/pricing', function () { return view('pages.pricing'); })->name('pricing');
Route::get('/about', function () { return view('pages.about'); })->name('about');
Route::get('/contact', function () { return view('pages.contact'); })->name('contact');
Route::get('/privacy', function () { return view('pages.privacy'); })->name('privacy');
Route::get('/terms', function () { return view('pages.terms'); })->name('terms');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Redirect generic dashboard route based on role
    Route::get('/dashboard', function() {
        if(auth()->user()->role === 'superadmin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('clinic.dashboard');
    })->name('dashboard');
});

// Fallback for expired subscriptions
Route::get('/subscription-expired', function () {
    return view('subscription-expired');
})->name('subscription.expired');

// ==========================================
// Clinic Domain (/clinic/*)
// ==========================================
Route::middleware(['auth', \App\Http\Middleware\IdentifyTenant::class, \App\Http\Middleware\CheckSubscriptionStatus::class])
    ->prefix('clinic')
    ->name('clinic.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Clinic\DashboardController::class, 'index'])->name('dashboard');
        
        // Sidebar Links
        Route::get('/patients', function() { return view('clinic.patients'); })->name('patients.index');
        Route::get('/appointments', function() { return view('clinic.appointments'); })->name('appointments.index');
        Route::get('/prescriptions', function() { return view('clinic.prescriptions'); })->name('prescriptions.index');
        Route::get('/records', function() { return view('clinic.records'); })->name('records.index');
        Route::get('/billing', function() { return view('clinic.billing'); })->name('billing.index');
        Route::get('/reports', function() { return view('clinic.reports'); })->name('reports.index');
        Route::get('/staff', function() { return view('clinic.staff'); })->name('staff.index');
        Route::get('/settings', function() { return view('clinic.settings'); })->name('settings.index');
});

// ==========================================
// Super Admin Domain (/admin/*)
// ==========================================
Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Sidebar Links
        Route::get('/clinics', function() { return view('admin.clinics'); })->name('clinics.index');
        Route::get('/subscriptions', function() { return view('admin.subscriptions'); })->name('subscriptions.index');
        Route::get('/revenue', function() { return view('admin.revenue'); })->name('revenue.index');
        Route::get('/tickets', function() { return view('admin.tickets'); })->name('tickets.index');
        Route::get('/users', function() { return view('admin.users'); })->name('users.index');
        Route::get('/roles', function() { return view('admin.roles'); })->name('roles.index');
        Route::get('/settings', function() { return view('admin.settings'); })->name('settings.index');
        Route::get('/logs', function() { return view('admin.logs'); })->name('logs.index');
});
