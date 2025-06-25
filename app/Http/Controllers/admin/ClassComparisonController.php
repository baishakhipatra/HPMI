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
        $sessionId = $request->session_id;
        $studentId = $request->student_id;

        // First, find the admission record for this student, session, and class
        $admission = StudentAdmission::where('student_id', $studentId)
                        ->where('session_id', $sessionId)
                        ->where('class_id', $classId)
                        ->first();

        if (!$admission) {
            return response()->json(['success' => false, 'subjects' => []]);
        }
        
        // Now get only subjects used in marks table for this exact admission
        $subjectIds = StudentsMark::where('student_admission_id', $admission->id)
                        ->pluck('subject_id')
                        ->unique();

        $subjects = Subject::whereIn('id', $subjectIds)->get();

        return response()->json(['success' => true, 'subjects' => $subjects]);
    }

    
}
