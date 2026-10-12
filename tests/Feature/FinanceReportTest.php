<?php

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Finance;
use App\Models\Patient;
use App\Models\User;
use App\Services\FinanceReportService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

test('finance report does not count appointment price as collected income', function () {
    Auth::logout();

    $clinic = Clinic::create([
        'name' => 'عيادة مالية',
        'slug' => 'finance-clinic',
        'is_active' => true,
    ]);

    $doctor = User::factory()->create([
        'role' => 'owner',
        'clinic_id' => $clinic->id,
    ]);

    $patient = Patient::create([
        'clinic_id' => $clinic->id,
        'name' => 'مريض',
        'phone' => '01011111111',
    ]);

    Appointment::create([
        'clinic_id' => $clinic->id,
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'scheduled_at' => now(),
        'status' => 'completed',
        'price' => 200,
        'payment_status' => 'unpaid',
        'amount_paid' => 0,
        'source' => 'reception_manual_entry',
    ]);

    Finance::withoutGlobalScopes()->create([
        'clinic_id' => $clinic->id,
        'type' => 'income',
        'amount' => 50,
        'description' => 'دفعة جزئية',
        'transaction_date' => now()->toDateString(),
    ]);

    Finance::withoutGlobalScopes()->create([
        'clinic_id' => $clinic->id,
        'type' => 'expense',
        'amount' => 20,
        'description' => 'مستلزمات',
        'transaction_date' => now()->toDateString(),
    ]);

    expect(Finance::withoutGlobalScopes()->count())->toBe(2);
    expect((float) Finance::withoutGlobalScopes()->where('type', 'income')->sum('amount'))->toEqual(50.0);

    $metrics = app(FinanceReportService::class)->forClinic(
        $clinic,
        Carbon::now()->startOfMonth(),
        Carbon::now()->endOfDay()
    );

    expect((float) $metrics['collected_income'])->toEqual(50.0)
        ->and((float) $metrics['expenses'])->toEqual(20.0)
        ->and((float) $metrics['net_cash'])->toEqual(30.0)
        ->and((float) $metrics['completed_service_value'])->toEqual(200.0)
        ->and((float) $metrics['outstanding'])->toEqual(200.0);
});
