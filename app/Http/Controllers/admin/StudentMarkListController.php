<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;
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

       // $students = [];

        $classOptions = $classes->map(function($class){
            $sections = $class->sections->pluck('section')->toArray();
            $sectionList = implode(', ', $sections);
            return [
                'id' => $class->id,
                // 'name' => $class->class . ' - ' . $sectionList
                'name' => $class->class
            ];
        });
        
        $query = StudentsMark::with(['student', 'class', 'subjectlist']);

        if($request->filled('student_name')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('student_name', 'LIKE', '%' . $request->student_name . '%');
            });
        }

        if($request->filled('class_id')){
            $query->where('class_id', $request->class_id);
        }

        if($request->filled('subject_id')){
            $query->where('subject_id', $request->subject_id);
        }


        $marks = $query->get();

        $totalRecords = $marks->count();

        $totalPercentage = 0;
        $studentCount = 0;

        foreach ($marks as $mark) {
            $obtained = 
                ($mark->term_one_stu_marks ?? 0) +
                ($mark->term_two_stu_marks ?? 0) +
                ($mark->mid_term_stu_marks ?? 0) +
                ($mark->final_exam_stu_marks ?? 0);

            $fullMarks = 
                ($mark->term_one_out_off ?? 0) +
                ($mark->term_two_out_off ?? 0) +
                ($mark->mid_term_out_off ?? 0) +
                ($mark->final_exam_out_off ?? 0);
                
            if ($fullMarks > 0) {
                $studentPercentage = ($obtained / $fullMarks) * 100;
                $totalPercentage += $studentPercentage;
                $studentCount++;
            }
        }

        $averagePercentage = $studentCount > 0 
            ? round($totalPercentage / $studentCount, 2)
            : 0;

        return view('admin.student_marks.index',compact('classes','subjects','classOptions', 'sessions', 'marks', 'totalRecords', 'averagePercentage'));
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


    // public function update(Request $request)
    // {
    //     //dd($request->all());
    //     // Validate request
    //     $validated = $request->validate([
    //         'id' => 'required|exists:students_marks,id',
    //         'session_id' => 'required|exists:academic_sessions,id',
    //         'student_id' => 'required|exists:students,id',
    //         'class_id' => 'required|exists:class_lists,id',
    //         'subject_id' => 'required|exists:subjects,id',
            
    //         'term_one_out_off' => 'nullable|integer',
    //         'term_one_stu_marks' => 'nullable|numeric|required_with:term_one_out_off|max:100',
            
    //         'term_two_out_off' => 'nullable|integer',
    //         'term_two_stu_marks' => 'nullable|numeric|required_with:term_two_out_off|max:100',
            
    //         'mid_term_out_off' => 'nullable|integer',
    //         'mid_term_stu_marks' => 'nullable|numeric|required_with:mid_term_out_off|max:100',
            
    //         'final_exam_out_off' => 'nullable|integer',
    //         'final_exam_stu_marks' => 'nullable|numeric|required_with:final_exam_out_off|max:100',
    //     ]);

    //     // Find the mark record
    //     $mark = StudentsMark::findOrFail($validated['id']);

    //     // Find or create student admission (use separate query so we don't skip updates)
    //     $admission = StudentAdmission::where([
    //         'student_id' => $validated['student_id'],
    //         'class_id' => $validated['class_id'],
    //         'session_id' => $validated['session_id']
    //     ])->first();

    //     if (!$admission) {
    //         $admission = StudentAdmission::create([
    //             'student_id' => $validated['student_id'],
    //             'class_id' => $validated['class_id'],
    //             'session_id' => $validated['session_id'],
    //             'admission_date' => now(),
    //         ]);
    //     }

    //     // Update marks
    //     $mark->update([
    //         'student_admission_id' => $admission->id,
    //         'subject_id' => $validated['subject_id'],
    //         'term_one_out_off' => $validated['term_one_out_off'] ?? null,
    //         'term_one_stu_marks' => $validated['term_one_stu_marks'] ?? null,
    //         'term_two_out_off' => $validated['term_two_out_off'] ?? null,
    //         'term_two_stu_marks' => $validated['term_two_stu_marks'] ?? null,
    //         'mid_term_out_off' => $validated['mid_term_out_off'] ?? null,
    //         'mid_term_stu_marks' => $validated['mid_term_stu_marks'] ?? null,
    //         'final_exam_out_off' => $validated['final_exam_out_off'] ?? null,
    //         'final_exam_stu_marks' => $validated['final_exam_stu_marks'] ?? null,
    //     ]);

    //     return redirect()->back()->with('success', 'Student marks updated successfully.');
    // }
    public function update(Request $request)
    {
        try {
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

            // Find or create student admission
            $admission = StudentAdmission::firstOrCreate(
                [
                    'student_id' => $validated['student_id'],
                    'class_id' => $validated['class_id'],
                    'session_id' => $validated['session_id'],
                ],
                [
                    'admission_date' => now(),
                ]
            );
            //dd($validated['session_id']);

            // Update marks
            // Update marks, including student_id, class_id, session_id
            $mark->update([
                'student_id' => $validated['student_id'],
                'class_id' => $validated['class_id'],
                'session_id' => $validated['session_id'], 
                'student_admission_id' => $admission->id,
                'subject_id' => $validated['subject_id'],
                'term_one_out_off' => $validated['term_one_out_off'] ?? null,
                'term_one_stu_marks' => $validated['term_one_stu_marks'] ?? null,
                'term_two_out_off' => $validated['term_two_out_off'] ?? null,
                'term_two_stu_marks' => $validated['term_two_stu_marks'] ?? null,
                'mid_term_out_off' => $validated['mid_term_out_off'] ?? null,
                'mid_term_stu_marks' => $validated['mid_term_stu_marks'] ?? null,
                'final_exam_out_off' => $validated['final_exam_out_off'] ?? null,
                'final_exam_stu_marks' => $validated['final_exam_stu_marks'] ?? null,
            ]);


            return redirect()->back()->with('success', 'Student marks updated successfully.');
        } catch (\Exception $e) {
            //dd($e->getMessage());
            \Log::error('Marks update failed: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);
            

            return redirect()->back()->with('error', 'Failed to update student marks. Please try again.');
        }
    }

   

}
