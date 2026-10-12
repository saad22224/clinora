<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prescription>
 */
class PrescriptionFactory extends Factory
{
    protected $model = Prescription::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'patient_id' => function (array $attributes) {
                return Patient::factory()->create([
                    'clinic_id' => $attributes['clinic_id'],
                ])->id;
            },
            'appointment_id' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function forClinic(Clinic $clinic): static
    {
        return $this->state(fn (array $attributes) => [
            'clinic_id' => $clinic->id,
        ]);
    }

    public function forPatient(Patient $patient): static
    {
        return $this->state(fn (array $attributes) => [
            'clinic_id' => $patient->clinic_id,
            'patient_id' => $patient->id,
        ]);
    }

    public function forAppointment(Appointment $appointment): static
    {
        return $this->state(fn (array $attributes) => [
            'clinic_id' => $appointment->clinic_id,
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
        ]);
    }

    public function withItems(int $count = 2): static
    {
        return $this->afterCreating(function (Prescription $prescription) use ($count) {
            PrescriptionItemFactory::new()
                ->count($count)
                ->create(['prescription_id' => $prescription->id]);
        });
    }
}
