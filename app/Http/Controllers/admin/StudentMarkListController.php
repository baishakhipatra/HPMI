<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\{ClassList, Subject, Student, StudentAdmission,ClassWiseSubject, StudentsMark};

class StudentMarkListController extends Controller
{
    public function index(Request $request)
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

        if ($request->ajax()) {
            $query = $request->input('query');
            $marks = StudentsMark::with(['student', 'class', 'subjectlist'])
                ->whereHas('student', function ($q) use ($query) {
                    $q->where('student_name', 'like', "%$query%");
                })
                ->get();
            return response()->json([
                'view' => view('admin.student_marks.partials.table', compact('marks'))->render()
            ]);
        }


        $marks = StudentsMark::with(['student', 'class', 'subjectlist'])->get();

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
            'subject_id' => 'required|exists:subjects,id',

            'term_one_out_off' => 'nullable|integer',
            'term_one_stu_marks' => 'required_with:term_one_out_off|nullable|numeric',

            'term_two_out_off' => 'nullable|integer',
            'term_two_stu_marks' => 'required_with:term_two_out_off|nullable|numeric',

            'mid_term_out_off' => 'nullable|integer',
            'mid_term_stu_marks' => 'required_with:mid_term_out_off|nullable|numeric',

            'final_exam_out_off' => 'nullable|integer',
            'final_exam_stu_marks' => 'required_with:final_exam_out_off|nullable|numeric',
        ]);

        $errors = [];

        if ($request->term_one_out_off && $request->term_one_stu_marks === null) {
            $errors['term_one_stu_marks'] = 'Term 1 marks required.';
        }
        if ($request->term_two_out_off && $request->term_two_stu_marks === null) {
            $errors['term_two_stu_marks'] = 'Term 2 marks required.';
        }
        if ($request->mid_term_out_off && $request->mid_term_stu_marks === null) {
            $errors['mid_term_stu_marks'] = 'Mid Term marks required.';
        }
        if ($request->final_exam_out_off && $request->final_exam_stu_marks === null) {
            $errors['final_exam_stu_marks'] = 'Final Exam marks required.';
        }

        if(
            empty($request->term_one_out_off) &&
            empty($request->term_two_out_off) &&
            empty($request->mid_term_out_off) &&
            empty($request->final_exam_out_off)
        ) {
            $errors['at_least_one_term'] = 'Select at least one term.';
        }

        if ($errors) {
            return back()->withErrors($errors)->withInput();
        }

        $admission = StudentAdmission::where('student_id', $validated['student_id'])
                                 ->where('class_id', $validated['class_id'])
                                 ->first();

        if (!$admission) {
            return back()->withErrors(['error' => 'Student admission record not found.']);
        }


        $validated['student_admission_id'] = $admission->id;

        StudentsMark::create($validated);
        // dd($validated);
        return back()->with('success', 'Marks Added Successfully');
    }


    public function update(Request $request)
    {
        // Log incoming request data
        //dd($request->all());
        Log::info('Update Marks Request:', $request->all());

        // Validate request
        $validated = $request->validate([
            'id' => 'required|exists:students_marks,id',
            'session_id' => 'required|exists:academic_sessions,id',
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:class_lists,id',
            'subject_id' => 'required|exists:subjects,id',

            'term_one_out_off' => 'nullable|integer',
            'term_one_stu_marks' => 'nullable|numeric|required_with:term_one_out_off|max:100',

            'term_two_out_off' => 'nullable|integer',
            'term_two_stu_marks' => 'nullable|numeric|required_with:term_two_out_off|max:100',

            'mid_term_out_off' => 'nullable|integer',
            'mid_term_stu_marks' => 'nullable|numeric|required_with:mid_term_out_off|max:100',

            'final_exam_out_off' => 'nullable|integer',
            'final_exam_stu_marks' => 'nullable|numeric|required_with:final_exam_out_off|max:100',
        ]);

        // Find the mark record
        $mark = StudentsMark::findOrFail($validated['id']);

        // Find student admission
        $admission = StudentAdmission::where('student_id', $validated['student_id'])
                                    ->where('class_id', $validated['class_id'])
                                    ->where('session_id', $validated['session_id'])
                                    ->first();
        //dd($admission);

        if (!$admission) {
            Log::warning('Student Admission Not Found', [
                'student_id' => $validated['student_id'],
                'class_id' => $validated['class_id'],
                'session_id' => $validated['session_id']
            ]);

            return back()->withErrors(['error' => 'Student admission record not found for the selected student, class, and session combination. Please ensure the student is admitted to this class in this session.'])->withInput();
        }

        // Update marks
        $mark->student_admission_id   = $admission->id;
        $mark->subject_id             = $validated['subject_id'];
        $mark->term_one_out_off       = $validated['term_one_out_off'] ?? null;
        $mark->term_one_stu_marks     = $validated['term_one_stu_marks'] ?? null; // Fixed here
        $mark->term_two_out_off       = $validated['term_two_out_off'] ?? null;
        $mark->term_two_stu_marks     = $validated['term_two_stu_marks'] ?? null;
        $mark->mid_term_out_off       = $validated['mid_term_out_off'] ?? null;
        $mark->mid_term_stu_marks     = $validated['mid_term_stu_marks'] ?? null;
        $mark->final_exam_out_off     = $validated['final_exam_out_off'] ?? null;
        $mark->final_exam_stu_marks   = $validated['final_exam_stu_marks'] ?? null;

        $mark->save();
       // dd($mark->student_admission_id );

        Log::info('Marks Updated Successfully', [
            'mark_id' => $mark->id,
            'data' => $mark->toArray()
        ]);

        return redirect()->back()->with('success', 'Student marks updated successfully.');
    }





}
