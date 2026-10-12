<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\User;
use App\Models\VisitType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(2, 50, 500);

        return [
            'clinic_id' => Clinic::factory(),
            'patient_id' => function (array $attributes) {
                return Patient::factory()->create([
                    'clinic_id' => $attributes['clinic_id'],
                ])->id;
            },
            'doctor_id' => function (array $attributes) {
                return User::factory()->doctor()->create([
                    'clinic_id' => $attributes['clinic_id'],
                ])->id;
            },
            'visit_type_id' => null,
            'scheduled_at' => fake()->dateTimeBetween('-14 days', '+30 days'),
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled', 'rejected']),
            'source' => fake()->randomElement(['website_landing_page', 'reception_manual_entry']),
            'price' => $price,
            'payment_status' => 'unpaid',
            'amount_paid' => 0,
            'notes' => fake()->optional()->sentence(),
            'reminder_sent' => false,
        ];
    }

    public function forClinic(Clinic $clinic): static
    {
        return $this->state(fn (array $attributes) => [
            'clinic_id' => $clinic->id,
        ]);
    }

    public function withVisitType(?VisitType $visitType = null): static
    {
        return $this->state(function (array $attributes) use ($visitType) {
            $type = $visitType ?? VisitType::factory()->create([
                'clinic_id' => $attributes['clinic_id'],
            ]);

            return [
                'visit_type_id' => $type->id,
                'price' => $type->price,
            ];
        });
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'scheduled_at' => fake()->dateTimeBetween('+1 day', '+14 days'),
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'scheduled_at' => fake()->dateTimeBetween('+1 day', '+14 days'),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'scheduled_at' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    public function fromWebsite(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'website_landing_page',
            'status' => 'pending',
        ]);
    }

    public function fromReception(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'reception_manual_entry',
            'status' => 'confirmed',
        ]);
    }

    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'unpaid',
            'amount_paid' => 0,
        ]);
    }

    public function partial(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'partial',
        ])->afterMaking(function (Appointment $appointment) {
            $appointment->amount_paid = round(((float) $appointment->price) / 2, 2);
        });
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
        ])->afterMaking(function (Appointment $appointment) {
            $appointment->amount_paid = (float) $appointment->price;
        });
    }
}
