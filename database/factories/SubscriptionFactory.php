<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Organization;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startsAt = now()->subDays(fake()->numberBetween(0, 30));

        return [
            'clinic_id' => Clinic::factory()->standalone(),
            'organization_id' => null,
            'plan_id' => Plan::factory()->single(),
            'plan' => fn (array $attributes) => Plan::query()->find($attributes['plan_id'])?->name ?? 'plan',
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->addDays(365),
            'is_active' => true,
        ];
    }

    public function forClinic(Clinic $clinic): static
    {
        return $this->state(fn (array $attributes) => [
            'clinic_id' => $clinic->id,
            'organization_id' => null,
        ]);
    }

    /**
     * Organization-scoped subscription (branches inherit; clinic_id must stay null).
     */
    public function forOrganization(Organization $organization): static
    {
        return $this->state(fn (array $attributes) => [
            'organization_id' => $organization->id,
            'clinic_id' => null,
        ]);
    }

    public function withPlan(Plan $plan): static
    {
        return $this->state(fn (array $attributes) => [
            'plan_id' => $plan->id,
            'plan' => $plan->name,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addYear(),
            'is_active' => true,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subYear(),
            'ends_at' => now()->subDay(),
            'is_active' => false,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
