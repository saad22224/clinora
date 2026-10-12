<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Finance;
use App\Models\Organization;
use App\Models\Scopes\TenantScope;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FinanceReportService
{
    /**
     * @return array{
     *     collected_income: float,
     *     expenses: float,
     *     net_cash: float,
     *     completed_service_value: float,
     *     outstanding: float
     * }
     */
    public function forClinic(Clinic $clinic, Carbon $from, Carbon $to): array
    {
        $collectedIncome = (float) Finance::query()
            ->withoutGlobalScope(TenantScope::class)
            ->where('clinic_id', $clinic->id)
            ->where('type', 'income')
            ->whereDate('transaction_date', '>=', $from->toDateString())
            ->whereDate('transaction_date', '<=', $to->toDateString())
            ->sum('amount');

        $expenses = (float) Finance::query()
            ->withoutGlobalScope(TenantScope::class)
            ->where('clinic_id', $clinic->id)
            ->where('type', 'expense')
            ->whereDate('transaction_date', '>=', $from->toDateString())
            ->whereDate('transaction_date', '<=', $to->toDateString())
            ->sum('amount');

        $completedServiceValue = (float) Appointment::query()
            ->withoutGlobalScope(TenantScope::class)
            ->where('clinic_id', $clinic->id)
            ->where('status', 'completed')
            ->whereBetween('scheduled_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->sum('price');

        $outstandingAppointments = Appointment::query()
            ->withoutGlobalScope(TenantScope::class)
            ->where('clinic_id', $clinic->id)
            ->where('status', 'completed')
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->whereBetween('scheduled_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->get(['price', 'amount_paid']);

        $outstanding = (float) $outstandingAppointments->sum(
            fn (Appointment $appointment) => max(0, (float) $appointment->price - (float) $appointment->amount_paid)
        );

        return [
            'collected_income' => $collectedIncome,
            'expenses' => $expenses,
            'net_cash' => $collectedIncome - $expenses,
            'completed_service_value' => $completedServiceValue,
            'outstanding' => $outstanding,
        ];
    }

    /**
     * @return array{totals: array<string, float>, branches: Collection<int, array<string, mixed>>}
     */
    public function forOrganization(Organization $organization, Carbon $from, Carbon $to): array
    {
        $branches = $organization->clinics()->get()->map(function (Clinic $clinic) use ($from, $to) {
            $metrics = $this->forClinic($clinic, $from, $to);

            return [
                'clinic_id' => $clinic->id,
                'clinic_name' => $clinic->name,
                'is_active' => $clinic->is_active,
                'patients_count' => $clinic->patients()->count(),
                'appointments_count' => $clinic->appointments()
                    ->whereBetween('scheduled_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
                    ->count(),
                ...$metrics,
            ];
        });

        $totals = [
            'collected_income' => (float) $branches->sum('collected_income'),
            'expenses' => (float) $branches->sum('expenses'),
            'net_cash' => (float) $branches->sum('net_cash'),
            'completed_service_value' => (float) $branches->sum('completed_service_value'),
            'outstanding' => (float) $branches->sum('outstanding'),
            'patients_count' => (int) $branches->sum('patients_count'),
            'appointments_count' => (int) $branches->sum('appointments_count'),
        ];

        return [
            'totals' => $totals,
            'branches' => $branches,
        ];
    }
}
