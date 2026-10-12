<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Clinic>
 */
class ClinicFactory extends Factory
{
    protected $model = Clinic::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company().' Clinic';

        return [
            'organization_id' => null,
            'marketer_id' => null,
            'name' => $name,
            'slug' => Clinic::uniqueSlug($name),
            'logo' => null,
            'description' => fake()->optional()->paragraph(),
            'theme_color' => fake()->hexColor(),
            'is_active' => true,
            'doctor_bio' => fake()->optional()->paragraph(),
            'doctor_title' => fake()->optional()->jobTitle(),
            'doctor_image' => null,
            'phone' => fake()->numerify('01#########'),
            'address' => fake()->address(),
            'latitude' => null,
            'longitude' => null,
            'working_hours' => [
                'sun' => ['09:00', '17:00'],
                'mon' => ['09:00', '17:00'],
                'tue' => ['09:00', '17:00'],
                'wed' => ['09:00', '17:00'],
                'thu' => ['09:00', '17:00'],
            ],
            'hero_title' => fake()->optional()->sentence(3),
            'hero_subtitle' => fake()->optional()->sentence(),
            'about_us' => fake()->optional()->paragraph(),
            'social_links' => [],
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function forOrganization(Organization $organization): static
    {
        return $this->state(fn (array $attributes) => [
            'organization_id' => $organization->id,
        ]);
    }

    public function standalone(): static
    {
        return $this->state(fn (array $attributes) => [
            'organization_id' => null,
        ]);
    }
}
