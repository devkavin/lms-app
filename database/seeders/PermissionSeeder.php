<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    private const PERMISSIONS = [
        'admins_create',
        'admins_edit',
        'admins_delete',
        'admins_view',

        'instructors_create',
        'instructors_edit',
        'instructors_delete',
        'instructors_view',

        'students_create',
        'students_edit',
        'students_delete',
        'students_view',

        'reports_generate',
        'reports_download',
        'reports_create',
        'reports_edit',
        'reports_delete',
        'reports_view',

        'enroll_course', // student enrolls to course
        'unenroll_course', // student unenrolls from course

        'courses_create',
        'courses_edit',
        'courses_delete',
        'courses_view',

        'modules_create',
        'modules_edit',
        'modules_delete',
        'modules_view',
        'modules_assign', // assign to course
        'modules_unassign', // unassign from course

        'lessons_create',
        'lessons_edit',
        'lessons_delete',
        'lessons_view',
        'lessons_assign', // assign to module
        'lessons_unassign', // unassign from module

        'assignments_create',
        'assignments_edit',
        'assignments_delete',
        'assignments_view',
        'assignments_submit',
        'assignments_assign', // assign to lesson
        'assignments_unassign', // unassign from lesson
    ];

    private const ROLES = [
        'admin' => [
            'admins_create',
            'admins_edit',
            'admins_delete',
            'admins_view',

            'instructors_create',
            'instructors_edit',
            'instructors_delete',
            'instructors_view',

            'students_create',
            'students_edit',
            'students_delete',
            'students_view',

            'reports_generate',
            'reports_download',
            'reports_view',
        ],
        'instructor' => [
            'reports_generate',
            'reports_download',
            'reports_create',
            'reports_edit',
            'reports_delete',
            'reports_view',

            'courses_create',
            'courses_edit',
            'courses_delete',
            'courses_view',

            'modules_create',
            'modules_edit',
            'modules_delete',
            'modules_view',
            'modules_assign',
            'modules_unassign',

            'lessons_create',
            'lessons_edit',
            'lessons_delete',
            'lessons_view',
            'lessons_assign',
            'lessons_unassign',

            'assignments_create',
            'assignments_edit',
            'assignments_delete',
            'assignments_view',
            'assignments_submit',
            'assignments_assign',
            'assignments_unassign',
        ],
        'student' => [
            'enroll_course',
            'unenroll_course',


            'courses_view',
            'modules_view',
            'lessons_view',

            'assignments_view',
            'assignments_submit',

            'reports_view',
            'reports_download',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $permission) {
            Permission::firstOrCreate(['name' => $permission]); // firstOrCreate only create if the permission does not exist
        }

        foreach (self::ROLES as $role => $permissions) {
            $roleModel = Role::firstOrCreate(['name' => $role]); // firstOrCreate only create if the role does not exist
            $roleModel->givePermissionTo($permissions);
        }
    }
}
