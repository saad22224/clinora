<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        Role::firstOrCreate(['name' => 'superadmin']);
        Role::firstOrCreate(['name' => 'clinic_owner']);
        Role::firstOrCreate(['name' => 'doctor']);
        Role::firstOrCreate(['name' => 'secretary']);

        // Create Permissions
        $permissions = [
            'manage_appointments',
            'manage_patients',
            'manage_prescriptions',
            'manage_records',
            'manage_billing',
            'manage_settings',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Assign all clinic permissions to clinic_owner and doctor roles
        $ownerRole = Role::where('name', 'clinic_owner')->first();
        $doctorRole = Role::where('name', 'doctor')->first();
        if ($ownerRole) {
            $ownerRole->syncPermissions($permissions);
        }
        if ($doctorRole) {
            $doctorRole->syncPermissions($permissions);
        }

        // Create Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'saadharera781@gmail.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('11922172Asd.mass360'),
                'role' => 'superadmin',
            ]
        );

        $admin->assignRole('superadmin');
    }
}
