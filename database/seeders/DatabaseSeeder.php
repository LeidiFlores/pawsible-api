<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->createRoles();
        $this->createPermissions();
        $this->assignPermissionsToRoles();
        $this->createUsers();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function createRoles(): void
    {
        $roles = ['admin', 'staff', 'adopter'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    private function createPermissions(): void
    {
        $permissions = ['manage pets', 'manage adoptions', 'manage appointments', 'manage users'];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }

    private function assignPermissionsToRoles(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $permissions = Permission::all();
        $adminRole->syncPermissions($permissions);

        $staffRole = Role::where('name', 'staff')->first();
        $staffPermissions = ['manage pets', 'manage adoptions', 'manage appointments'];
        $staffRole->syncPermissions($staffPermissions);

        $adopterRole = Role::where('name', 'adopter')->first();
        $adopterPermissions = ['manage adoptions', 'manage appointments'];
        $adopterRole->syncPermissions($adopterPermissions);
    }

    private function createUsers(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@pawsible.dev'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );
        $admin->assignRole('admin');

        $staff = User::firstOrCreate(
            ['email' => 'staff@pawsible.dev'],
            ['name' => 'Staff', 'password' => Hash::make('password')]
        );
        $staff->assignRole('staff');

        $adopter = User::firstOrCreate(
            ['email' => 'user@pawsible.dev'],
            ['name' => 'Adopter', 'password' => Hash::make('password')]
        );
        $adopter->assignRole('adopter');
    }
}
