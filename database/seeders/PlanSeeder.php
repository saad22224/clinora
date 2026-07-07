<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'name' => 'free plan',
            'price' => 0.00,
            'duration_in_days' => 30,
            'features' => [
                'max_patients' => 'unlimited',
                'max_appointments' => 'unlimited',
                'max_doctors' => 'unlimited',
                'support' => 'vip',
            ],
            'is_active' => true,
        ]);
    }
}
