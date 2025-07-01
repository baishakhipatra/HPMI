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
            ['parent_name' => 'master_management', 'name' => 'create_subject', 'route' => 'admin.subjectlist.create'],
            ['parent_name' => 'master_management', 'name' => 'update_subject', 'route' => 'admin.subjectlist.update'],
            ['parent_name' => 'master_management', 'name' => 'delete_subject', 'route' => 'admin.subjectlist.delete'],

            ['parent_name' => 'master_management', 'name' => 'class_list', 'route' => 'admin.classlist'],
            ['parent_name' => 'master_management', 'name' => 'create_class', 'route' => 'admin.classcreate'],
            ['parent_name' => 'master_management', 'name' => 'update_class', 'route' => 'admin.classedit'],
            ['parent_name' => 'master_management', 'name' => 'delete_class', 'route' => 'admin.classdelete'],
            ['parent_name' => 'master_management', 'name' => 'class_wise_subject', 'route' => 'admin.class.subjects'],
            ['parent_name' => 'master_management', 'name' => 'class_wise_subject_assign', 'route' => 'admin.class.subjects.assign'],
            ['parent_name' => 'master_management', 'name' => 'class_wise_subject_delete', 'route' => 'admin.class.subjects.delete'],

            ['parent_name' => 'master_management', 'name' => 'progress_marking_list', 'route' => 'admin.student.progresslist'],
            ['parent_name' => 'master_management', 'name' => 'create_progress_marking', 'route' => 'admin.student.progressstore'],
            ['parent_name' => 'master_management', 'name' => 'edit_progress_marking', 'route' => 'admin.student.progressupdate'],
            ['parent_name' => 'master_management', 'name' => 'delete_progress_marking', 'route' => 'admin.student.progressdelete'],

            ['parent_name' => 'master_management', 'name' => 'progress_chart', 'route' => 'admin.progresschart'],

            ['parent_name' => 'master_management', 'name' => 'designations', 'route' => 'admin.designation.list'],

            ['parent_name' => 'employee_management', 'name' => 'employee_list', 'route' => 'admin.employee.index'],
            ['parent_name' => 'employee_management', 'name' => 'create_employee', 'route' => 'admin.employee.create'],
            ['parent_name' => 'employee_management', 'name' => 'employee_details', 'route' => 'admin.employee.show'],
            ['parent_name' => 'employee_management', 'name' => 'edit_employee', 'route' => 'admin.employee.edit'],
            ['parent_name' => 'employee_management', 'name' => 'delete_employee', 'route' => 'admin.employee.delete'],
            ['parent_name' => 'employee_management', 'name' => 'export_employee_list', 'route' => 'admin.employee.export'],

            ['parent_name' => 'teacher_management', 'name' => 'teacher_list', 'route' => 'admin.teacher.index'],
            ['parent_name' => 'teacher_management', 'name' => 'create_teacher', 'route' => 'admin.teacher.create'],
            ['parent_name' => 'teacher_management', 'name' => 'details_teacher', 'route' => 'admin.teacher.show'],
            ['parent_name' => 'teacher_management', 'name' => 'edit_teacher', 'route' => 'admin.teacher.edit'],
            ['parent_name' => 'teacher_management', 'name' => 'delete_teacher', 'route' => 'admin.teacher.delete'],
            ['parent_name' => 'teacher_management', 'name' => 'export_teacher_list', 'route' => 'admin.teacher.export'],

            ['parent_name' => 'student_management', 'name' => 'student_list', 'route' => 'admin.studentlist'],
            ['parent_name' => 'student_management', 'name' => 'create_student', 'route' => 'admin.studentcreate'],
            ['parent_name' => 'student_management', 'name' => 'details_student', 'route' => 'admin.admin.student.show'],
            ['parent_name' => 'student_management', 'name' => 'edit_student', 'route' => 'admin.studentedit'],
            ['parent_name' => 'student_management', 'name' => 'delete_student', 'route' => 'admin.studentdelete'],
            ['parent_name' => 'student_management', 'name' => 'export_student_list', 'route' => 'admin.student.export'],

            ['parent_name' => 'student_management', 'name' => 'student_progress_marking', 'route' => 'admin.student.progressmarkinglist'],
            ['parent_name' => 'student_management', 'name' => 'student_class_wise_comparison', 'route' => 'admin.student.classcompare'],

            ['parent_name' => 'student_management', 'name' => 'student_readmision_list', 'route' => 'admin.student.readmission.index'],
            ['parent_name' => 'student_management', 'name' => 'create_student_readmision', 'route' => 'admin.student.readmission.store'],

            ['parent_name' => 'student_management', 'name' => 'student_mark_list', 'route' => 'admin.studentmarklist'],
            ['parent_name' => 'student_management', 'name' => 'create_student_mark', 'route' => 'admin.student-marks.store'],
            ['parent_name' => 'student_management', 'name' => 'edit_student_mark', 'route' => 'admin.student-marks.update'],
            ['parent_name' => 'student_management', 'name' => 'delete_student_mark', 'route' => 'admin.student-marks.delete'],
            ['parent_name' => 'student_management', 'name' => 'export_student_mark_list', 'route' => 'admin.student-marks.export'],

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

        //Assign all permissions to Admin
        $adminDesignation = Designation::find(3);
        if($adminDesignation) {
            $allPermissionIds = Permission::pluck('id');
            $adminDesignation->permissions()->sync($allPermissionIds);
        }
    }
}
