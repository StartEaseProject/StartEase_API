<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* $permissions = [
            'role', 'user', 'permission', 'establishment',
            'filiere', 'grade', 'period', 'project', 'remark', 'speciality',
            'comment', 'task', 'announcement', 'defence'
        ];
        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name' => 'create-' . $permission
            ]);
            DB::table('permissions')->insert([
                'name' => 'read-' . $permission
            ]);
            DB::table('permissions')->insert([
                'name' => 'update-' . $permission
            ]);
            DB::table('permissions')->insert([
                'name' => 'delete-' . $permission
            ]);
        } */
        DB::table('permissions')->insert([
            //['name' => 'validate-project'],
            ['name' => 'read-project-observation'],
            ['name' => 'update-progress-project'],
            ['name' => 'authorize-project'],
            ['name' => 'validate-task'],
            ['name' => 'submit-task'],
            ['name' => 'upload-files-defence'],
            ['name' => 'read-deliberation'],
            ['name' => 'print-defence'],
        ]);

        $roles = [
            Role::DEFAULT_ROLES["ADMIN"] => [
                'create-role', 'delete-role', 'read-role', 'update-role',
                'create-permission', 'delete-permission', 'read-permission',
                'create-user', 'read-user', 'update-user', 'read-dashboard-admin'
            ],
            Role::DEFAULT_ROLES["HEADMASTER"]  => [
                'read-project',
                'read-announcement',
                'read-period',
                'read-defence', 'read-deliberation',
                'read-dashboard-other'
            ],
            Role::DEFAULT_ROLES['COMMITTEE'] => [
                'read-project',
                'read-period',
                'create-remark', 'delete-remark', 'read-remark', 'update-remark',
                'read-announcement',
                'read-defence', 'read-deliberation',
                'read-dashboard-other'
            ],
            Role::DEFAULT_ROLES["INCUBATOR_PRESIDENT"]  => [
                'read-project', 'validate-project',
                'update-period', 'read-period',
                'create-remark', 'delete-remark', 'read-remark', 'update-remark',
                'read-announcement', 'create-announcement', 'update-announcement', 'delete-announcement',
                'read-defence', 'read-deliberation',
                'read-dashboard-other'
            ],
            Role::DEFAULT_ROLES["INTERNSHIP"]  => [
                'read-project',
                'read-period',
                'read-announcement',
                 'read-dashboard-other',
                'read-defence', 'create-defence', 'update-defence', 'delete-defence', 'read-deliberation', 'print-defence'
            ],
            Role::DEFAULT_ROLES["PROJECT_HOLDER"] => [
                'withdraw-project', 'read-project', 'update-project', 'read-project-observation',
                'read-remark',
                'create-comment', 'delete-comment', 'read-comment', 'update-comment', 'read-deliberation',
                'read-task',
                'read-defence',
            ],
            Role::DEFAULT_ROLES["SUPERVISOR"]  => [
                'read-project', 'read-project-observation', 'update-progress-project', 'authorize-project',
                'read-remark',
                'create-comment', 'delete-comment', 'read-comment', 'update-comment',
                'create-task', 'delete-task', 'read-task', 'update-task', 'validate-task',
                'read-defence', 'read-deliberation'
            ],
            Role::DEFAULT_ROLES["JURY"]  => [
                'read-project',
                'read-defence', 'read-deliberation'
            ],
            Role::DEFAULT_ROLES["PROJECT_MEMBER"]  => [
                'read-project', 'read-project-observation',
                'read-remark',
                'create-comment', 'delete-comment', 'read-comment', 'update-comment',
                'read-task', 'submit-task',
                'read-defence', 'upload-files-defence', 'read-deliberation'
            ],
            Role::DEFAULT_ROLES["STUDENT"]  => [
                'create-project',
                'read-announcement',
                'read-period',
                'read-dashboard-student'
            ],
            Role::DEFAULT_ROLES["TEACHER"]  => [
                'create-project',
                'read-announcement',
                'read-period',
                'read-dashboard-teacher'
            ],
        ];
        foreach ($roles as $role => $permissions) {
            $r = Role::create([
                'name' => $role
            ]);
            foreach ($permissions as $name) {
                $permission = Permission::firstWhere('name', $name);
                if (!$permission) {
                    $permission = Permission::create([
                        'name' => $name
                    ]);
                }
                DB::table('role_permission')->insert([
                    'role_id' => $r->id,
                    'permission_id' => $permission->id
                ]);
            }
        }
    }
}
