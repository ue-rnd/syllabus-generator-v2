<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        // Note: User, Role, and Permission management permissions are restricted to superadmin only
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign roles',
            
            // Permission Management
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'assign permissions',
            
            // Syllabus Management
            'view syllabus',
            'create syllabus',
            'edit syllabus',
            'delete syllabus',
            'publish syllabus',
            
            // Course Management
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            
            // Settings Management
            'view settings',
            'edit settings',
            
            // Dashboard Access
            'view dashboard',
            'view analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Superadmin (IT Department) - Full system access including user/role management
        $superadminRole = Role::create(['name' => 'superadmin']);
        $superadminRole->givePermissionTo(Permission::all());

        // Admin (College Secretaries) - College-level management without user/role management
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            // Syllabus Management
            'view syllabus',
            'create syllabus',
            'edit syllabus',
            'delete syllabus',
            'publish syllabus',
            
            // Course Management
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            
            // Settings Management (limited)
            'view settings',
            
            // Dashboard Access
            'view dashboard',
            'view analytics',
        ]);

        // Faculty - Teaching staff
        $facultyRole = Role::create(['name' => 'faculty']);
        $facultyRole->givePermissionTo([
            'view dashboard',
            'view syllabus',
            'create syllabus',
            'edit syllabus',
            'view courses',
            'create courses',
            'edit courses',
            'view settings',
        ]);
    }
}
