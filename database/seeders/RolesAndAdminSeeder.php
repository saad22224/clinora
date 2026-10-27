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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'clinic_owner', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'secretary', 'guard_name' => 'web']);

        $permissions = [
            'manage_appointments',
            'use_staff_messages',
            'manage_patients',
            'manage_prescriptions',
            'manage_records',
            'manage_billing',
            'manage_settings',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $ownerRole = Role::where('name', 'clinic_owner')->first();
        $doctorRole = Role::where('name', 'doctor')->first();
        if ($ownerRole) {
            $ownerRole->syncPermissions($permissions);
        }
        if ($doctorRole) {
            $doctorRole->syncPermissions($permissions);
        }

        $email = config('clinora.superadmin.email');
        $password = config('clinora.superadmin.password');

        if (! empty($email) && ! empty($password)) {
            $admin = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => config('clinora.superadmin.name', 'مدير النظام'),
                    'password' => Hash::make($password),
                    'role' => 'superadmin',
                ]
            );

            if ($admin->role !== 'superadmin') {
                $admin->update(['role' => 'superadmin']);
            }

            $admin->assignRole('superadmin');
        }
    }
}
