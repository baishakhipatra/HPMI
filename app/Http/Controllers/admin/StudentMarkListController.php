<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ClassList, Subject, Student, StudentAdmission,ClassWiseSubject, StudentsMark};

class StudentMarkListController extends Controller
{
    public function index()
    {
        $marks = StudentsMark::with(['student', 'class', 'subject'])->get();
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
        return view('admin.student_marks.index',compact('classes','subjects','classOptions', 'students', 'sessions','marks'));
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

       
        return response()->json([
            'success' => true,
            'students' => $students,
        ]);
    }
    public function getClassBySessionAndStudent(Request $request)
    {
         $session_id = $request->session_id;
         $student_id = $request->student_id;

        $admissions = StudentAdmission::with('class')
                        ->where('session_id', $session_id)
                        ->where('student_id', $student_id)
                        ->get();
    
        if(count($admissions)>0){
            $ClassWiseSubject = ClassWiseSubject::with('subject')
            ->where('class_id', $admissions[0]->class_id)
            ->get();
            
            $subjects = $ClassWiseSubject->map(function ($subjectData) {
                return [
                    'id' => $subjectData->subject->id,
                    'name' => ucwords($subjectData->subject->sub_name),
                ];
            });
            // dd($subjects);
            $classes = $admissions->map(function ($admission) {
                return [
                    'id' => $admission->class->id,
                    'name' => ucwords($admission->class->class).'('.$admission->section.')',
                ];
            });
            return response()->json([
                'success' => true,
                'classes' => $classes,
                'subjects' => $subjects,
            ]);
            }else{
            return response()->json([
                'success' => true,
                'classes' => [],
                'subjects' => [],
            ]);
        }   
    }

    public function storeStudentMarks(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'class_id'   => 'required|exists:class_lists,id',
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:class_wise_subjects,id',

            'term_one_out_off' => 'nullable|integer',
            'term_one_stu_marks' => 'required_with:term_one_out_off|nullable|numeric',

            'term_two_out_off' => 'nullable|integer',
            'term_two_stu_marks' => 'required_with:term_two_out_off|nullable|numeric',

            'mid_term_out_off' => 'nullable|integer',
            'mid_term_stu_marks' => 'required_with:mid_term_out_off|nullable|numeric',

            'final_exam_out_off' => 'nullable|integer',
            'final_exam_stu_marks' => 'required_with:final_exam_out_off|nullable|numeric',
        ]);

        $admission = StudentAdmission::where('student_id', $validated['student_id'])
                                 ->where('class_id', $validated['class_id'])
                                 ->first();

        if (!$admission) {
            return back()->withErrors(['error' => 'Student admission record not found.']);
        }

        // Add student_admission_id to validated data
        $validated['student_admission_id'] = $admission->id;

        StudentsMark::create($validated);
        // dd($validated);
        return back()->with('success', 'Marks Added Successfully');
    }

}
