<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\{ClassList, StudentsMark, StudentAdmission, Student, Subject, AcademicSession};
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


        $admission = StudentAdmission::where('student_id', $studentId)
                        ->where('session_id', $sessionId)
                        ->where('class_id', $classId)
                        ->first();

        if (!$admission) {
            return response()->json(['success' => false, 'subjects' => []]);
        }
        

        $subjectIds = StudentsMark::where('student_admission_id', $admission->id)
                        ->pluck('subject_id')
                        ->unique();

        $subjects = Subject::whereIn('id', $subjectIds)->get();

        return response()->json(['success' => true, 'subjects' => $subjects]);
    }

    public function compareMarks(Request $request){
        $request->validate([
            'student_id' => 'required|integer',
            'session1' => 'required|different:season2',
            'session2' => 'required',
            'term1' => 'required|in:term_one,term_two,mid_term,final_exam',
            'term2' => 'required|in:term_one,term_two,mid_term,final_exam'
        ]);

        $studentId = $request->student_id;
        $session1 = $request->session1;
        $session2 = $request->session2;
        $term1Column = $request->term1 . '_stu_marks';
        $term2Column = $request->term2 . '_stu_marks';

        $marks1 = StudentsMark::where('student_id', $studentId)
            ->whereHas('studentAdmission', fn($q) => $q->where('session_id', $request->session1))
            ->with('subjectlist')
            ->get();

        $marks2 = StudentsMark::where('student_id', $studentId)
            ->whereHas('studentAdmission', fn($q) => $q->where('session_id', $request->session2))
            ->with('subjectlist')
            ->get();


        if ($marks1->isEmpty() || $marks2->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'No marks found for one or both sessions.'
            ]);
        }

        $subjectIds1 = $marks1->pluck('subject_id')->toArray();
        $subjectIds2 = $marks2->pluck('subject_id')->toArray();

        $commonSubjectsIds = array_intersect($subjectIds1,$subjectIds2);

        if(empty($commonSubjectsIds)){
            return response()->json([
                'success' => false,
                'message' => 'Only same subjects are needed to compare',
                'data' => []

            ]);
        }

        $results = [];


        foreach ($commonSubjectsIds as $subjectId) {
            $mark1 = $marks1->firstWhere('subject_id', $subjectId);
            $mark2 = $marks2->firstWhere('subject_id', $subjectId);

            $subjectName = $mark1->subjectlist->sub_name ?? 'N/A';
            $session1Mark = $mark1->$term1Column ?? 0;
            $session2Mark = $mark2->$term2Column ?? 0;

            $improvement = ($session1Mark > 0)
                ? round((($session2Mark - $session1Mark) / $session1Mark) * 100, 2) . '%'
                : 'N/A';

            $results[] = [
                'subject'     => $subjectName,
                'marks1'      => $session1Mark,
                'marks2'      => $session2Mark,
                'improvement' => $improvement,
            ];
        }

        return response()->json([
            'success' => true,
            'session1' => AcademicSession::find($session1)->session_name,
            'session2' => AcademicSession::find($session2)->session_name,
            'data' => $results
        ]);
    }

}
