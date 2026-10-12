<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::query()->updateOrCreate(
            ['name' => 'free plan'],
            [
                'type' => 'single',
                'description' => 'باقة تجريبية لعيادة واحدة',
                'price' => 0.00,
                'duration_in_days' => 30,
                'max_clinics' => 1,
                'features' => [],
                'is_active' => true,
            ]
        );

        Plan::query()->updateOrCreate(
            ['name' => 'عيادة واحدة'],
            [
                'type' => 'single',
                'description' => 'اشتراك عيادة مستقلة',
                'price' => 499.00,
                'duration_in_days' => 365,
                'max_clinics' => 1,
                'features' => [],
                'is_active' => true,
            ]
        );

        Plan::query()->updateOrCreate(
            ['name' => 'سلسلة صغيرة'],
            [
                'type' => 'chain',
                'description' => 'إدارة مركزية حتى 5 عيادات',
                'price' => 1999.00,
                'duration_in_days' => 365,
                'max_clinics' => 5,
                'features' => [],
                'is_active' => true,
            ]
        );

        Plan::query()->updateOrCreate(
            ['name' => 'شبكة عيادات'],
            [
                'type' => 'chain',
                'description' => 'إدارة مركزية حتى 20 عيادة',
                'price' => 4999.00,
                'duration_in_days' => 365,
                'max_clinics' => 20,
                'features' => [],
                'is_active' => true,
            ]
        );
    }
}
