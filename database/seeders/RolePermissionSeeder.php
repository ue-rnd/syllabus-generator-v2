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
            'manage user profiles',
            'reset user passwords',
            'lock unlock accounts',

            // Quality Assurance permissions
            'create quality standards',
            'edit quality standards',
            'view quality standards',
            'delete quality standards',
            'create quality checklists',
            'edit quality checklists',
            'view quality checklists',
            'delete quality checklists',
            'run quality checks',
            'create quality audits',
            'edit quality audits',
            'view quality audits',
            'delete quality audits',
            'manage standards compliance',
            'view quality reports',
            'export quality reports',

            // Analytics and Reporting permissions
            'view analytics dashboard',
            'create custom reports',
            'edit custom reports',
            'view custom reports',
            'delete custom reports',
            'export reports',
            'view historical data',
            'manage report templates',

            // System administration permissions
            'access admin panel',
            'manage system settings',
            'view system logs',
            'manage backups',
            'manage notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $this->createSuperAdminRole();
        $this->createAdminRole();
        $this->createDeanRole();
        $this->createAssociateDeanRole();
        $this->createDepartmentChairRole();
        $this->createQARepresentativeRole();
        $this->createFacultyRole();
        $this->createStaffRole();

        // Ensure admin and superadmin roles have the new permissions
        $this->ensureRolePermissions();
    }

    private function createSuperAdminRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'superadmin']);
        $role->syncPermissions(Permission::all());
    }

    private function createAdminRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'admin']);
        $role->syncPermissions([
            'access admin panel',
            'create colleges',
            'edit colleges',
            'view colleges',
            'delete colleges',
            'create departments',
            'edit departments',
            'view departments',
            'delete departments',
            'create programs',
            'edit programs',
            'view programs',
            'delete programs',
            'create courses',
            'edit courses',
            'view courses',
            'delete courses',
            'create users',
            'edit users',
            'view users',
            'delete users',
            'manage user profiles',
            'reset user passwords',
            'lock unlock accounts',
            'assign roles',
            'view syllabi',
            'export syllabi pdf',
            'view analytics dashboard',
            'view custom reports',
            'export reports',
            'view historical data',
            'view system logs',
            'manage system settings',
        ]);
    }

    private function createDeanRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'dean']);
        $role->syncPermissions([
            'access admin panel',
            'view syllabi',
            'approve syllabi as dean',
            'reject syllabi',
            'export syllabi pdf',
            'view colleges',
            'view departments',
            'view programs',
            'view courses',
            'view users',
            'view analytics dashboard',
            'view custom reports',
            'export reports',
            'view quality standards',
            'view quality audits',
            'view quality reports',
        ]);
    }

    private function createAssociateDeanRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'associate_dean']);
        $role->syncPermissions([
            'access admin panel',
            'view syllabi',
            'approve syllabi as associate dean',
            'reject syllabi',
            'export syllabi pdf',
            'view colleges',
            'view departments',
            'view programs',
            'view courses',
            'view users',
            'view analytics dashboard',
            'view custom reports',
            'export reports',
            'view quality standards',
            'view quality audits',
            'view quality reports',
        ]);
    }

    private function createDepartmentChairRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'department_chair']);
        $role->syncPermissions([
            'access admin panel',
            'create syllabi',
            'edit syllabi',
            'view syllabi',
            'submit syllabi for approval',
            'approve syllabi as department chair',
            'reject syllabi',
            'export syllabi pdf',
            'edit departments',
            'view departments',
            'create programs',
            'edit programs',
            'view programs',
            'create courses',
            'edit courses',
            'view courses',
            'view users',
            'view analytics dashboard',
            'view custom reports',
            'export reports',
            'view quality standards',
            'view quality audits',
            'view quality reports',
        ]);
    }

    private function createQARepresentativeRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'qa_representative']);
        $role->syncPermissions([
            'access admin panel',
            'view syllabi',
            'export syllabi pdf',
            'view colleges',
            'view departments',
            'view programs',
            'view courses',
            'create quality standards',
            'edit quality standards',
            'view quality standards',
            'delete quality standards',
            'create quality checklists',
            'edit quality checklists',
            'view quality checklists',
            'delete quality checklists',
            'run quality checks',
            'create quality audits',
            'edit quality audits',
            'view quality audits',
            'delete quality audits',
            'manage standards compliance',
            'view quality reports',
            'export quality reports',
            'view analytics dashboard',
            'create custom reports',
            'edit custom reports',
            'view custom reports',
            'export reports',
            'view historical data',
        ]);
    }

    private function createFacultyRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'faculty']);
        $role->syncPermissions([
            'access admin panel',
            'create syllabi',
            'edit syllabi',
            'view syllabi',
            'submit syllabi for approval',
            'export syllabi pdf',
            'view colleges',
            'view departments',
            'view programs',
            'view courses',
            'view quality standards',
            'view quality checklists',
        ]);
    }

    private function createStaffRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'staff']);
        $role->syncPermissions([
            'access admin panel',
            'view syllabi',
            'view colleges',
            'view departments',
            'view programs',
            'view courses',
            'view quality standards',
        ]);
    }

    private function ensureRolePermissions(): void
    {
        // Make sure superadmin has ALL permissions
        $superadminRole = Role::where('name', 'superadmin')->first();
        if ($superadminRole) {
            $superadminRole->syncPermissions(Permission::all());
        }

        // Make sure admin role has system management permissions
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminPermissions = $adminRole->permissions->pluck('name')->toArray();
            $newPermissions = [
                'view system logs',
                'manage notifications',
                'manage backups',
            ];

            foreach ($newPermissions as $permission) {
                if (!in_array($permission, $adminPermissions)) {
                    $permissionModel = Permission::where('name', $permission)->first();
                    if ($permissionModel) {
                        $adminRole->givePermissionTo($permissionModel);
                    }
                }
            }
        }
    }
}
