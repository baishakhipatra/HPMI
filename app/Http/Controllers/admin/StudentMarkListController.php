<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ClassList, Subject, Student, StudentAdmission};

class StudentMarkListController extends Controller
{
    public function index()
    {
        $sessions = StudentAdmission::with('session')
              ->select('session_id')
              ->distinct()
              ->get();
        $classes = ClassList::with('sections')->get();
        $subjects = Subject::all();

        $students = [];

        $classOptions = $classes->map(function($class){
            $sections = $class->sections->pluck('section')->toArray();
            $sectionList = implode(', ', $sections);
            return [
                'id' => $class->id,
                'name' => $class->class . ' - ' . $sectionList
            ];
        });
        return view('admin.student_marks.index',compact('classes','subjects','classOptions', 'students', 'sessions'));
    }

    public function getStudentsBySession(Request $request)
    {
        $sessionId = $request->sessionId;

        $admissions = StudentAdmission::with('student')
                        ->where('session_id', $sessionId)
                        ->get();

        $students = $admissions->map(function ($admission) {
            return [
                'id' => ucwords($admission->student->id),
                'name' => ucwords($admission->student->student_name),
            ];
        });

        $classIds = StudentAdmission::with('class')
                    ->where('session_id', $sessionId)
                    ->select('class_id')
                    ->distinct()
                    ->get();

        $classes = $classIds->map(function ($item) {
            return [
                'id' => $item->class->id ?? null,
                'name' => $item->class->class ?? 'N/A'
            ];
        });

       
        return response()->json([
            'success' => true,
            'students' => $students,
            'classes' => $classes
        ]);
    }

}
