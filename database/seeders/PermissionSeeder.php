<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         $permissions = [
            ['parent_name' => 'master_management', 'name' => 'subject_list', 'route' => 'admin.subjectlist.index'],
            ['parent_name' => 'master_management', 'name' => 'subject_create', 'route' => 'admin.subjectlist.create'],
            ['parent_name' => 'master_management', 'name' => 'subject_update', 'route' => 'admin.subjectlist.update'],
            ['parent_name' => 'master_management', 'name' => 'subject_delete', 'route' => 'admin.subjectlist.delete'],

            ['parent_name' => 'master_management', 'name' => 'class_list', 'route' => 'admin.classlist'],
            ['parent_name' => 'master_management', 'name' => 'class_create', 'route' => 'admin.classcreate'],
            ['parent_name' => 'master_management', 'name' => 'class_update', 'route' => 'admin.classedit'],
            ['parent_name' => 'master_management', 'name' => 'class_delete', 'route' => 'admin.classdelete'],
            ['parent_name' => 'master_management', 'name' => 'class_wise_subject', 'route' => 'admin.class.subjects'],
            ['parent_name' => 'master_management', 'name' => 'class_wise_subject_assign', 'route' => 'admin.class.subjects.assign'],
            ['parent_name' => 'master_management', 'name' => 'class_wise_subject_delete', 'route' => 'admin.class.subjects.delete'],

            ['parent_name' => 'master_management', 'name' => 'progress_marking_categories_list', 'route' => 'admin.student.progresslist'],
            ['parent_name' => 'master_management', 'name' => 'progress_marking_categories_create', 'route' => 'admin.student.progressstore'],
            ['parent_name' => 'master_management', 'name' => 'progress_marking_categories_edit', 'route' => 'admin.student.progressupdate'],
            ['parent_name' => 'master_management', 'name' => 'progress_marking_categories_delete', 'route' => 'admin.student.progressdelete'],

            ['parent_name' => 'master_management', 'name' => 'progress_chart', 'route' => 'admin.progresschart'],

            ['parent_name' => 'master_management', 'name' => 'designations', 'route' => 'admin.designation.list'],

            ['parent_name' => 'employee_management', 'name' => 'employee_list', 'route' => 'admin.employee.index'],
            ['parent_name' => 'employee_management', 'name' => 'employee_create', 'route' => 'admin.employee.create'],
            ['parent_name' => 'employee_management', 'name' => 'employee_details', 'route' => 'admin.employee.show'],
            ['parent_name' => 'employee_management', 'name' => 'employee_edit', 'route' => 'admin.employee.edit'],
            ['parent_name' => 'employee_management', 'name' => 'employee_delete', 'route' => 'admin.employee.delete'],
            ['parent_name' => 'employee_management', 'name' => 'employee_export', 'route' => 'admin.employee.export'],

            ['parent_name' => 'teacher_management', 'name' => 'teacher_list', 'route' => 'admin.teacher.index'],
            ['parent_name' => 'teacher_management', 'name' => 'teacher_create', 'route' => 'admin.teacher.create'],
            ['parent_name' => 'teacher_management', 'name' => 'teacher_details', 'route' => 'admin.teacher.show'],
            ['parent_name' => 'teacher_management', 'name' => 'teacher_edit', 'route' => 'admin.teacher.edit'],
            ['parent_name' => 'teacher_management', 'name' => 'teacher_delete', 'route' => 'admin.teacher.delete'],
            ['parent_name' => 'teacher_management', 'name' => 'teacher_export', 'route' => 'admin.teacher.export'],

            ['parent_name' => 'student_management', 'name' => 'student_list', 'route' => 'admin.studentlist'],
            ['parent_name' => 'student_management', 'name' => 'student_create', 'route' => 'admin.studentcreate'],
            ['parent_name' => 'student_management', 'name' => 'student_details', 'route' => 'admin.admin.student.show'],
            ['parent_name' => 'student_management', 'name' => 'student_edit', 'route' => 'admin.studentedit'],
            ['parent_name' => 'student_management', 'name' => 'student_delete', 'route' => 'admin.studentdelete'],
            ['parent_name' => 'student_management', 'name' => 'student_export', 'route' => 'admin.student.export'],

            ['parent_name' => 'student_management', 'name' => 'student_progress_marking', 'route' => 'admin.student.progressmarkinglist'],
            ['parent_name' => 'student_management', 'name' => 'student_class_wise_comparison', 'route' => 'admin.student.classcompare'],

            ['parent_name' => 'student_management', 'name' => 'student_readmision_list', 'route' => 'admin.student.readmission.index'],
            ['parent_name' => 'student_management', 'name' => 'student_readmision_create', 'route' => 'admin.student.readmission.store'],

            ['parent_name' => 'student_management', 'name' => 'student_mark_list', 'route' => 'admin.studentmarklist'],
            ['parent_name' => 'student_management', 'name' => 'student_mark_create', 'route' => 'admin.student-marks.store'],
            ['parent_name' => 'student_management', 'name' => 'student_mark_edit', 'route' => 'admin.student-marks.update'],
            ['parent_name' => 'student_management', 'name' => 'student_mark_delete', 'route' => 'admin.student-marks.delete'],
            ['parent_name' => 'student_management', 'name' => 'student_mark_export', 'route' => 'admin.student-marks.export'],

        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'parent_name' => $permission['parent_name'],
                    'route' => $permission['route']
                ]
            );
        }
    }
}
