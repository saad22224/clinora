<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        Role::firstOrCreate(['name' => 'superadmin']);
        Role::firstOrCreate(['name' => 'clinic_owner']);
        Role::firstOrCreate(['name' => 'doctor']);
        Role::firstOrCreate(['name' => 'secretary']);

        // Create Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@clinora.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
            ]
        );

        $admin->assignRole('superadmin');
    }
}
