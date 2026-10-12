<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true).' Plan',
            'type' => 'single',
            'description' => fake()->optional()->sentence(),
            'price' => fake()->randomFloat(2, 0, 5000),
            'duration_in_days' => 365,
            'max_clinics' => 1,
            'features' => [],
            'is_active' => true,
        ];
    }

    public function single(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'single',
            'max_clinics' => 1,
        ]);
    }

    public function chain(int $maxClinics = 5): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'chain',
            'max_clinics' => $maxClinics,
        ]);
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'free plan',
            'type' => 'single',
            'price' => 0,
            'duration_in_days' => 30,
            'max_clinics' => 1,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
