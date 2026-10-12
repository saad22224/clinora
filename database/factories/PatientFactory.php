<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dob = fake()->optional(0.8)->dateTimeBetween('-80 years', '-1 year');
        $age = $dob ? (int) $dob->diff(now())->y : fake()->optional()->numberBetween(1, 90);

        return [
            'clinic_id' => Clinic::factory(),
            'name' => fake()->name(),
            'phone' => fake()->numerify('01#########'),
            'dob' => $dob?->format('Y-m-d'),
            'gender' => fake()->optional()->randomElement(['male', 'female']),
            'age' => $age,
            'notes' => fake()->optional()->sentence(),
            'medical_history' => fake()->optional(0.4)->paragraph(),
            'surgical_history' => fake()->optional(0.2)->sentence(),
        ];
    }

    public function forClinic(Clinic $clinic): static
    {
        return $this->state(fn (array $attributes) => [
            'clinic_id' => $clinic->id,
        ]);
    }
}
