<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\{ClassList, StudentsMark, StudentAdmission, Student, Subject};
use Illuminate\Support\Facades\DB;

class ClassComparisonController extends Controller
{
   

    public function index($student_id)
    {
        $student = Student::with('admissions.session')->findOrFail($student_id);

        $admissions = $student->admissions()
            ->with('session')
            ->orderBy('session_id', 'desc')
            ->get();

        return view('admin.student_management.class_wise_compare', compact('student', 'admissions'));
    }

    public function getClassBySession(Request $request)
    {
        $studentId = $request->student_id;
        $sessionId = $request->session_id;

        $admission = StudentAdmission::where('student_id', $studentId)
                    ->where('session_id', $sessionId)
                    ->with('class')
                    ->first();

        if ($admission) {
            return response()->json([
                'success' => true,
                'class' => [
                    'id' => $admission->class->id,
                    'name' => $admission->class->class
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No admission found.']);
    }

    public function getSubjectsByClass(Request $request)
    {
        $classId = $request->class_id;

        $subjectIds = StudentsMark::where('class_id', $classId)
                        ->pluck('subject_id')
                        ->unique();

        $subjects = Subject::whereIn('id', $subjectIds)->get();

        return response()->json(['success' => true, 'subjects' => $subjects]);
    }

    
}
