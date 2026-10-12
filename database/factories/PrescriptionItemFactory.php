<?php

namespace Database\Factories;

use App\Models\Prescription;
use App\Models\PrescriptionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrescriptionItem>
 */
class PrescriptionItemFactory extends Factory
{
    protected $model = PrescriptionItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prescription_id' => Prescription::factory(),
            'medicine_name' => fake()->randomElement([
                'Amoxicillin 500mg',
                'Paracetamol 500mg',
                'Ibuprofen 400mg',
                'Omeprazole 20mg',
                'Cetirizine 10mg',
                'أموكسيسيلين',
                'باراسيتامول',
            ]),
            'dosage' => fake()->randomElement(['1x1', '1x2', '1x3', '2x1', 'عند الحاجة']),
            'instructions' => fake()->optional()->randomElement([
                'بعد الأكل',
                'قبل الأكل',
                'مع كوب ماء',
                'لمدة 5 أيام',
                'لمدة أسبوع',
            ]),
        ];
    }

    public function forPrescription(Prescription $prescription): static
    {
        return $this->state(fn (array $attributes) => [
            'prescription_id' => $prescription->id,
        ]);
    }
}
