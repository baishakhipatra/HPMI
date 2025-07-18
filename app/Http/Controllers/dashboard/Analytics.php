<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{ClassList, Student, Admin, TeacherClass, StudentAdmission};

class Analytics extends Controller
{
  
    public function index()
    {
        $user = Auth::guard('admin')->user();


        if($user->user_type == 'Teacher') { // <-- Change 'teacher' to 'Teacher'
            $assignedClassIds = TeacherClass::where('teacher_id', $user->id)
                                ->pluck('class_id')
                                ->unique();
            $totalClasses = count($assignedClassIds);
         
            
             $totalStudents = Student::whereHas('admission', function ($q) use ($assignedClassIds) {
                            $q->whereIn('class_id', $assignedClassIds);
                        })
                        ->whereNull('deleted_at')
                        ->count();

                                    // dd($totalStudents);

            $totalTeachers = 1;
        } else {
            $totalStudents = Student::whereNull('deleted_at')->count();
            $totalClasses = ClassList::count();
            $totalTeachers = Admin::where('user_type', 'Teacher')->count(); 
        }
        return view('content.dashboard.dashboards-analytics', compact(
            'totalStudents', 'totalClasses', 'totalTeachers', 'user'));
    }

    public function getStudentAdmissionChartData() {
        $user = Auth::guard('admin')->user();

        // Base query
        $query = StudentAdmission::with('session');

        if ($user->user_type === 'Teacher') {
            // Limit data to teacher's assigned classes
            $assignedClassIds = TeacherClass::where('teacher_id', $user->id)
                ->pluck('class_id')
                ->unique()
                ->toArray();

            $query->whereIn('class_id', $assignedClassIds);
        }

        $admissions = $query->get();
        $grouped = $admissions->groupBy('session_id');

        $admissionData = $grouped->map(function ($item, $sessionId) {
            $sessionName = optional($item->first()->session)->session_name ?? 'Unknown';
            return [
                'session_name' => $sessionName,
                'total' => $item->count()
            ];
        })->sortBy('session_name')->values();

        return response()->json([
            'labels' => $admissionData->pluck('session_name'),
            'data' => $admissionData->pluck('total'),
        ]);
    }
}
