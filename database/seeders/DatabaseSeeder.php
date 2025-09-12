<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions
        $this->call([
            RoleSeeder::class,
            RolePermissionSeeder::class,
        ]);

        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'lastname' => 'Admin',
            'firstname' => 'Super',
            'middlename' => '',
            'position' => 'Super Admin',
            'is_active' => true,
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'email' => 'rnd_admin@ue.edu.ph',
        ]);

        // Assign superadmin role to the super admin user
        $superAdmin->assignRole('superadmin');
    }
}
