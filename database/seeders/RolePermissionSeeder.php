<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // This seeder is for additional role and permission management
        // You can add custom roles and permissions here as needed
        
        // Example: Create a custom role for content managers
        $contentManagerRole = Role::firstOrCreate(['name' => 'content-manager']);
        
        // Example: Create custom permissions for content management
        $contentPermissions = [
            'manage content',
            'approve content',
            'schedule content',
        ];

        foreach ($contentPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to the content manager role
        $contentManagerRole->givePermissionTo($contentPermissions);
        
        // Example: Create a guest role with minimal permissions
        $guestRole = Role::firstOrCreate(['name' => 'guest']);
        $guestRole->givePermissionTo(['view dashboard']);
    }
}
