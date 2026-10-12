<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\MedicalFile;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MedicalFile>
 */
class MedicalFileFactory extends Factory
{
    protected $model = MedicalFile::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['document', 'xray', 'lab']);

        return [
            'clinic_id' => Clinic::factory(),
            'patient_id' => function (array $attributes) {
                return Patient::factory()->create([
                    'clinic_id' => $attributes['clinic_id'],
                ])->id;
            },
            'file_path' => 'medical-records/'.Str::uuid().'.pdf',
            'type' => $type,
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

    public function document(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'document',
        ]);
    }

    public function xray(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'xray',
        ]);
    }

    public function lab(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'lab',
        ]);
    }
}
