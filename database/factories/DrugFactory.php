<?php

namespace Database\Factories;

use App\Models\Drug;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Drug>
 */
class DrugFactory extends Factory
{
    protected $model = Drug::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $en = fake()->unique()->words(2, true);

        return [
            'commercial_name_en' => ucwords($en),
            'commercial_name_ar' => fake()->optional()->words(2, true),
            'scientific_name' => fake()->optional()->words(3, true),
            'manufacturer' => fake()->optional()->company(),
            'drug_class' => fake()->optional()->randomElement(['Analgesic', 'Antibiotic', 'Antihistamine', 'Antacid']),
            'route' => fake()->optional()->randomElement(['oral', 'topical', 'IV', 'IM']),
            'price_egp' => fake()->optional()->randomFloat(2, 5, 250),
        ];
    }
}
