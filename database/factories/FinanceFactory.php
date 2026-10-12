<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Finance;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Finance>
 */
class FinanceFactory extends Factory
{
    protected $model = Finance::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['income', 'expense']);

        return [
            'clinic_id' => Clinic::factory(),
            'type' => $type,
            'category' => $type === 'income'
                ? fake()->randomElement(['كشف', 'متابعة', 'إجراء', 'أخرى'])
                : fake()->randomElement(['مستلزمات', 'إيجار', 'رواتب', 'صيانة']),
            'payment_method' => fake()->optional()->randomElement(['cash', 'card', 'transfer', 'instapay']),
            'patient_id' => null,
            'appointment_id' => null,
            'amount' => fake()->randomFloat(2, 10, 1000),
            'description' => fake()->sentence(4),
            'transaction_date' => fake()->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
        ];
    }

    public function forClinic(Clinic $clinic): static
    {
        return $this->state(fn (array $attributes) => [
            'clinic_id' => $clinic->id,
        ]);
    }

    public function income(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'income',
            'category' => fake()->randomElement(['كشف', 'متابعة', 'إجراء', 'أخرى']),
        ]);
    }

    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'expense',
            'category' => fake()->randomElement(['مستلزمات', 'إيجار', 'رواتب', 'صيانة']),
            'patient_id' => null,
            'appointment_id' => null,
        ]);
    }

    /**
     * Link an income row to an appointment without equating amount to appointment price.
     */
    public function forAppointment(Appointment $appointment, ?float $amount = null): static
    {
        return $this->state(function (array $attributes) use ($appointment, $amount) {
            $price = (float) $appointment->price;
            $collected = $amount ?? fake()->randomFloat(2, 10, max(10, $price));

            return [
                'clinic_id' => $appointment->clinic_id,
                'type' => 'income',
                'patient_id' => $appointment->patient_id,
                'appointment_id' => $appointment->id,
                'amount' => $collected,
                'category' => 'كشف',
                'description' => 'تحصيل موعد #'.$appointment->id,
            ];
        });
    }

    public function forPatient(Patient $patient): static
    {
        return $this->state(fn (array $attributes) => [
            'clinic_id' => $patient->clinic_id,
            'patient_id' => $patient->id,
        ]);
    }
}
