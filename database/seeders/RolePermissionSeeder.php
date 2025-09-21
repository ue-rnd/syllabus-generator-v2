<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Syllabus permissions
            'create syllabi',
            'edit syllabi',
            'view syllabi',
            'delete syllabi',
            'submit syllabi for approval',
            'approve syllabi as department chair',
            'approve syllabi as associate dean',
            'approve syllabi as dean',
            'reject syllabi',
            'export syllabi pdf',

            // College permissions
            'create colleges',
            'edit colleges',
            'view colleges',
            'delete colleges',

            // Department permissions
            'create departments',
            'edit departments',
            'view departments',
            'delete departments',

            // Program permissions
            'create programs',
            'edit programs',
            'view programs',
            'delete programs',

            // Course permissions
            'create courses',
            'edit courses',
            'view courses',
            'delete courses',

            // User management permissions
            'create users',
            'edit users',
            'view users',
            'delete users',
            'assign roles',
            'manage permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $this->createSuperAdminRole();
        $this->createDeanRole();
        $this->createAssociateDeanRole();
        $this->createDepartmentChairRole();
        $this->createFacultyRole();
    }

    private function createSuperAdminRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'superadmin']);
        $role->syncPermissions(Permission::all());
    }

    private function createDeanRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'dean']);
        $role->syncPermissions([
            'view syllabi',
            'approve syllabi as dean',
            'reject syllabi',
            'export syllabi pdf',
            'view colleges',
            'view departments',
            'view programs',
            'view courses',
            'view users',
        ]);
    }

    private function createAssociateDeanRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'associate_dean']);
        $role->syncPermissions([
            'view syllabi',
            'approve syllabi as associate dean',
            'reject syllabi',
            'export syllabi pdf',
            'view colleges',
            'view departments',
            'view programs',
            'view courses',
            'view users',
        ]);
    }

    private function createDepartmentChairRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'department_chair']);
        $role->syncPermissions([
            'create syllabi',
            'edit syllabi',
            'view syllabi',
            'submit syllabi for approval',
            'approve syllabi as department chair',
            'reject syllabi',
            'export syllabi pdf',
            'create departments',
            'edit departments',
            'view departments',
            'view programs',
            'view courses',
            'view users',
        ]);
    }

    private function createFacultyRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'faculty']);
        $role->syncPermissions([
            'create syllabi',
            'edit syllabi',
            'view syllabi',
            'submit syllabi for approval',
            'export syllabi pdf',
            'view colleges',
            'view departments',
            'view programs',
            'view courses',
        ]);
    }
}
