<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\VisitType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VisitType>
 */
class VisitTypeFactory extends Factory
{
    protected $model = VisitType::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'name' => fake()->randomElement(['كشف', 'استشارة', 'متابعة', 'طوارئ']),
            'description' => fake()->optional()->sentence(),
            'price' => fake()->randomFloat(2, 50, 500),
            'icon' => null,
        ];
    }

    public function forClinic(Clinic $clinic): static
    {
        return $this->state(fn (array $attributes) => [
            'clinic_id' => $clinic->id,
        ]);
    }
}
