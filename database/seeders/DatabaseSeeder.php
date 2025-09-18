<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\Department;
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
            CollegeSeeder::class,
            DepartmentSeeder::class,
            ProgramSeeder::class,
            CourseSeeder::class,
            SettingSeeder::class,
        ]);

        $ccss = College::where('code', 'CCSS')->first();
        $dcs = Department::where('name', 'Department of Computer Science')->first();
        $dit = Department::where('name', 'Department of Information Technology')->first();
        $demc = Department::where('name', 'Department of Entertainment and Multimedia Computing')->first();

        $superAdmin = User::factory()->create([
            'lastname' => 'Admin',
            'firstname' => 'Super',
            'middlename' => '',
            'position' => 'superadmin',
            'college_id' => null,
            'department_id' => null,
            'is_active' => true,
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'email' => 'rnd_admin@ue.edu.ph',
            'password' => bcrypt('password'),
        ]);

        $ccssDean = User::factory()->create([
            'firstname' => 'Ma. Teresa',
            'middlename' => 'Francisco',
            'lastname' => 'Borebor',
            'position' => 'dean',
            'college_id' => $ccss->id,
            'department_id' => null,
            'is_active' => true,
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'email' => 'materesa.borebor@ue.edu.ph',
            'password' => bcrypt('ccssdean'),
        ]);

        $ccssAssocDean = User::factory()->create([
            'firstname' => 'Arne',
            'middlename' => 'Rocero',
            'lastname' => 'Bana',
            'position' => 'associate_dean',
            'college_id' => $ccss->id,
            'department_id' => null,
            'is_active' => true,
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'email' => 'arne.bana@ue.edu.ph',
            'password' => bcrypt('ccssassociatedean'),
        ]);

        $ccssDC1 = User::factory()->create([
            'firstname' => 'Sheila',
            'middlename' => 'Marasigan',
            'lastname' => 'Geronimo',
            'position' => 'department_chair',
            'college_id' => $ccss->id,
            'department_id' => $dcs->id,
            'is_active' => true,
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'email' => 'sheila.geronimo@ue.edu.ph',
            'password' => bcrypt('ccssdccs'),
        ]);

        $ccssDC2 = User::factory()->create([
            'firstname' => 'Marc Rodin',
            'middlename' => 'C',
            'lastname' => 'Ligas',
            'position' => 'department_chair',
            'college_id' => $ccss->id,
            'department_id' => $dit->id,
            'is_active' => true,
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'email' => 'marcrodin.ligas@ue.edu.ph',
            'password' => bcrypt('ccssdcit'),
        ]);

        $ccssDC3 = User::factory()->create([
            'firstname' => 'Mark Anthony',
            'middlename' => '',
            'lastname' => 'Uy',
            'position' => 'department_chair',
            'college_id' => $ccss->id,
            'department_id' => $demc->id,
            'is_active' => true,
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'email' => 'markanthony.uy@ue.edu.ph',
            'password' => bcrypt('ccssdcemc'),
        ]);

        $ccssFaculty = User::factory()->create([
            'firstname' => 'Melie Jim',
            'middlename' => 'Flores',
            'lastname' => 'Sarmiento',
            'position' => 'associate_professor',
            'college_id' => $ccss->id,
            'department_id' => $dcs->id,
            'is_active' => true,
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'email' => 'meliejim.sarmiento@ue.edu.ph',
            'password' => bcrypt('ccssfaculty'),
        ]);

        $ccss->update([
            'dean_id' => $ccssDean->id,
            'associate_dean_id' => $ccssAssocDean->id,
        ]);

        $dcs->update([
            'department_chair_id' => $ccssDC1->id,
        ]);

        $dit->update([
            'department_chair_id' => $ccssDC2->id,
        ]);

        $demc->update([
            'department_chair_id' => $ccssDC3->id,
        ]);

        // Assign superadmin role to the super admin user
        $superAdmin->assignRole('superadmin');
        $ccssDean->assignRole('admin');
        $ccssAssocDean->assignRole('admin');
        $ccssDC1->assignRole('admin');
        $ccssDC2->assignRole('admin');
        $ccssDC3->assignRole('admin');
        $ccssFaculty->assignRole('faculty');
    }
}
