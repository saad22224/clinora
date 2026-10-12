<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrganizationUser>
 */
class OrganizationUserFactory extends Factory
{
    protected $model = OrganizationUser::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory()->state(['role' => 'owner', 'clinic_id' => null]),
            'role' => 'owner',
            'permissions' => ['enter_clinic', 'manage_org_clinics', 'view_org_reports'],
        ];
    }

    public function owner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'owner',
            'permissions' => ['enter_clinic', 'manage_org_clinics', 'view_org_reports'],
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'permissions' => ['enter_clinic', 'view_org_reports'],
        ]);
    }

    public function restricted(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'permissions' => ['view_org_reports'],
        ]);
    }
}
