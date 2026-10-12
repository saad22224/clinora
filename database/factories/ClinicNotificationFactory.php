<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\ClinicNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClinicNotification>
 */
class ClinicNotificationFactory extends Factory
{
    protected $model = ClinicNotification::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'title' => fake()->sentence(3),
            'message' => fake()->sentence(8),
            'type' => fake()->randomElement(['info', 'warning', 'success', 'danger']),
            'is_read' => false,
        ];
    }

    public function forClinic(Clinic $clinic): static
    {
        return $this->state(fn (array $attributes) => [
            'clinic_id' => $clinic->id,
        ]);
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }
}
