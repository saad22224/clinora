<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\StaffMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StaffMessage>
 */
class StaffMessageFactory extends Factory
{
    protected $model = StaffMessage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'from_user_id' => User::factory(),
            'body' => fake()->sentence(),
            'is_quick' => false,
        ];
    }
}
