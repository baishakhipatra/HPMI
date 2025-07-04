<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{ClassList, Student, Admin, TeacherClass, StudentAdmission};

class Analytics extends Controller
{
  // public function index()
  // {
  //   $user = Auth::guard('admin')->user();

  //   if($user->user_type == 'teacher') {
  //     $assignedClassIds  = TeacherClass::where('teacher_id', $user->id)->pluck('class_id');

  //     $totalClasses = $assignedClassIds->count();
  //     $totalStudents = StudentAdmission::whereIn('class_id', $assignedClassIds)->count();

  //     $totalTeachers = 1;
  //   } else {
  //       $totalStudents = StudentAdmission::count();
  //       $totalClasses = ClassList::count();
  //       $totalTeachers = Admin::where('user_type', 'teacher')->count();
  //   }
  //   return view('content.dashboard.dashboards-analytics', compact(
  //       'totalStudents', 'totalClasses', 'totalTeachers', 'user'));
  // }
  public function index()
    {
        $user = Auth::guard('admin')->user();

        // Debugging line (optional, but good to keep in mind for future issues)
        // dd($user->user_type);

        if($user->user_type == 'Teacher') { // <-- Change 'teacher' to 'Teacher'
            $assignedClassIds = TeacherClass::where('teacher_id', $user->id)
                                ->pluck('class_id')
                                ->unique();

            $totalClasses = $assignedClassIds->count();
            // $totalStudents = StudentAdmission::whereIn('class_id', $assignedClassIds)->count();
            // Assuming 'student_id' is the column that uniquely identifies a student in StudentAdmission
            $uniqueStudentsInAssignedClasses = StudentAdmission::whereIn('class_id', $assignedClassIds)
                                                ->pluck('student_id') // Pluck the unique identifier for students
                                                ->unique(); // Get only unique student IDs
            $totalStudents = $uniqueStudentsInAssignedClasses->count();

            $totalTeachers = 1;
        } else {
            $totalStudents = StudentAdmission::count();
            $totalClasses = ClassList::count();
            $totalTeachers = Admin::where('user_type', 'Teacher')->count(); // <-- Also change here if fetching count of all teachers
        }
        return view('content.dashboard.dashboards-analytics', compact(
            'totalStudents', 'totalClasses', 'totalTeachers', 'user'));
    }
}
