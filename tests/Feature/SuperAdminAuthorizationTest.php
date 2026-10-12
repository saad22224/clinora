<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('super admin can open admin dashboard', function () {
    Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

    $admin = User::factory()->create(['role' => 'superadmin']);
    $admin->assignRole('superadmin');

    $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
});

test('non superadmin is forbidden from admin dashboard', function () {
    $user = User::factory()->create(['role' => 'owner']);

    $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
});
