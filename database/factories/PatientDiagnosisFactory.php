<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Patient;
use App\Models\PatientDiagnosis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientDiagnosis>
 */
class PatientDiagnosisFactory extends Factory
{
    protected $model = PatientDiagnosis::class;

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
            'diagnosis' => fake()->randomElement([
                'التهاب جيوب أنفية',
                'ارتفاع ضغط الدم',
                'سكري من النوع الثاني',
                'التهاب البلعوم',
                'صداع نصفي',
            ]),
            'notes' => fake()->optional()->sentence(),
            'diagnosed_at' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        ];
    }

    public function forPatient(Patient $patient): static
    {
        return $this->state(fn (array $attributes) => [
            'clinic_id' => $patient->clinic_id,
            'patient_id' => $patient->id,
        ]);
    }
}
