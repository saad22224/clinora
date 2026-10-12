<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->numerify('01#########'),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'clinic_id' => null,
            'role' => 'owner',
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function forClinic(Clinic $clinic): static
    {
        return $this->state(fn (array $attributes) => [
            'clinic_id' => $clinic->id,
        ]);
    }

    public function superadmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'superadmin',
            'clinic_id' => null,
        ])->afterCreating(function (User $user) {
            $this->ensureRole('superadmin');
            $user->assignRole('superadmin');
        });
    }

    public function clinicOwner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'owner',
        ])->afterCreating(function (User $user) {
            $this->ensureRole('clinic_owner');
            $user->assignRole('clinic_owner');
        });
    }

    public function doctor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'doctor',
        ])->afterCreating(function (User $user) {
            $this->ensureRole('doctor');
            $user->assignRole('doctor');
        });
    }

    public function secretary(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'secretary',
        ])->afterCreating(function (User $user) {
            $this->ensureRole('secretary');
            $user->assignRole('secretary');
        });
    }

    /**
     * Secretary with only appointment management (restricted staff scenario).
     */
    public function restrictedSecretary(): static
    {
        return $this->secretary()->afterCreating(function (User $user) {
            $permission = Permission::findOrCreate('manage_appointments', 'web');
            $user->syncPermissions([$permission]);
        });
    }

    private function ensureRole(string $name): void
    {
        Role::findOrCreate($name, 'web');
    }
}
