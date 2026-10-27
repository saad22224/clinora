<?php

namespace Database\Factories;

use App\Models\Marketer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Marketer>
 */
class MarketerFactory extends Factory
{
    protected $model = Marketer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => '010'.fake()->numerify('########'),
            'email' => fake()->unique()->safeEmail(),
            'referral_code' => Str::upper(Str::random(8)),
            'is_active' => true,
        ];
    }
}

