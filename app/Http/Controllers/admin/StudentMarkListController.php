<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Http\Request;
use App\Models\{ClassList, Subject, Student, StudentAdmission,ClassWiseSubject, StudentsMark, AcademicSession};

class StudentMarkListController extends Controller
{
    
    // public function index(Request $request)
    // {
        
    //     $sessions = StudentAdmission::with('session')
    //           ->select('session_id')
    //           ->distinct()
    //           ->get();
    //     $classes = ClassList::with('sections')->get();
    //     $subjects = Subject::all();

    //     $academicSessions = AcademicSession::all();

    //    // $students = [];

    //     $classOptions = $classes->map(function($class){
    //         $sections = $class->sections->pluck('section')->toArray();
    //         $sectionList = implode(', ', $sections);
    //         return [
    //             'id' => $class->id,
    //              //'name' => $class->class . ' - ' . $sectionList
    //             'name' => $class->class
    //         ];
    //     });
        
    //     $query = StudentsMark::with(['student', 'class', 'subjectlist', 'studentAdmission']);

    //     if($request->filled('student_name')) {
    //         $query->whereHas('student', function ($q) use ($request) {
    //             $q->where('student_name', 'LIKE', '%' . $request->student_name . '%');
    //         });
    //     }

    //     if($request->filled('class_filter')){
    //         $query->where('class_id', $request->class_filter);
    //     }

    //     if($request->filled('subject_filter')){
    //         $query->where('subject_id', $request->subject_filter);
    //     }

    //     if ($request->filled('session_filter')) {
    //         $query->whereHas('studentAdmission', function ($q) use ($request) {
    //             $q->where('session_id', $request->session_filter);
    //         });
    //     }

    //     $marks = $query->paginate(10);

    //     return view('admin.student_marks.index',compact('classes','subjects','classOptions', 'sessions', 'marks', 'academicSessions'));
    // }
    public function index(Request $request)
    {
        $sessions = StudentAdmission::with('session')
            ->select('session_id')
            ->distinct()
            ->get();

        $classes = ClassList::with('sections')->get();
        $subjects = Subject::all();
        $academicSessions = AcademicSession::all();

        $classOptions = $classes->map(function($class){
            $sections = $class->sections->pluck('section')->toArray();
            $sectionList = implode(', ', $sections);
            return [
                'id' => $class->id,
                'name' => $class->class
            ];
        });

        $query = StudentsMark::with(['student', 'class', 'subjectlist', 'studentAdmission']);

        if($request->filled('student_name')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('student_name', 'LIKE', '%' . $request->student_name . '%');
            });
        }

        if($request->filled('class_filter')){
            $query->where('class_id', $request->class_filter);
        }

        if($request->filled('subject_filter')){
            $query->where('subject_id', $request->subject_filter);
        }

        if ($request->filled('session_filter')) {
            $query->whereHas('studentAdmission', function ($q) use ($request) {
                $q->where('session_id', $request->session_filter);
            });
        }

        $marks = $query->paginate(10);

        // Move group + transformation logic from Blade to here
        $groupedMarks = $marks->getCollection()->groupBy(function($item) {
            return $item->studentAdmission->student_id . '_' .
                $item->studentAdmission->session_id . '_' .
                $item->studentAdmission->class_id;
        });

        // Replace original collection with empty one to avoid double data
        $marks->setCollection(collect());

        return view('admin.student_marks.index', compact('classes', 'subjects', 'classOptions', 'sessions',
            'marks', 'groupedMarks', 'academicSessions'
        ));
    }


    public function getStudentsBySession(Request $request)
    {
        $sessionId = $request->sessionId;

        $admissions = StudentAdmission::with('student')
                        ->where('session_id', $sessionId)
                        ->whereHas('student')
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
    public function getEditData($id) {
        try {
            $mark = StudentsMark::with(['student', 'class', 'subjectlist', 'studentAdmission'])
                        ->where('id', $id)
                        ->first();
            if (!$mark) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mark record not found.'
                ], 404);
            }

            // You can format response data here if needed
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $mark->id,
                    'session_id' => optional($mark->studentAdmission)->session_id,
                    'student_id' => $mark->student_id,
                    'class_id' => $mark->class_id,
                    'subject_id' => $mark->subject_id,
                    'term_one_out_off' => $mark->term_one_out_off,
                    'term_one_stu_marks' => $mark->term_one_stu_marks,
                    'term_two_out_off' => $mark->term_two_out_off,
                    'term_two_stu_marks' => $mark->term_two_stu_marks,
                    'mid_term_out_off' => $mark->mid_term_out_off,
                    'mid_term_stu_marks' => $mark->mid_term_stu_marks,
                    'final_exam_out_off' => $mark->final_exam_out_off,
                    'final_exam_stu_marks' => $mark->final_exam_stu_marks,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching data.',
                'error' => $e->getMessage()
            ], 500);
        }  
    }


    public function storeStudentMarks(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:academic_sessions,id', // Added
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
            $errors['message'] = 'Term 1 marks required.';
        }
        if ($request->term_two_out_off && $request->term_two_stu_marks === null) {
            $errors['message'] = 'Term 2 marks required.';
        }
        if ($request->mid_term_out_off && $request->mid_term_stu_marks === null) {
            $errors['message'] = 'Mid Term marks required.';
        }
        if ($request->final_exam_out_off && $request->final_exam_stu_marks === null) {
            $errors['message'] = 'Final Exam marks required.';
        }

        if(
            empty($request->term_one_out_off) &&
            empty($request->term_two_out_off) &&
            empty($request->mid_term_out_off) &&
            empty($request->final_exam_out_off)
        ) {
            $errors['message'] = 'Select at least one term.';
        }

        if($request->term_one_out_off && $request->term_one_stu_marks > $request->term_one_out_off){
            $errors['message'] = 'Term 1 marks cannot be greater than term 1 out off.';
        }

        if($request->term_two_out_off && $request->term_two_stu_marks > $request->term_two_out_off){
            $errors['message'] = 'Term 2 marks cannot be greater than term 2 out off.';
        }

        if($request->mid_term_out_off && $request->mid_term_stu_marks > $request->mid_term_out_off){
            $errors['message'] = 'Mid term marks cannot be greater than mid term out off.';
        }

        if($request->final_exam_out_off && $request->final_exam_stu_marks > $request->final_exam_out_off){
            $errors['message'] = 'Final exam marks cannot be greater than final exam out off.';
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => $errors['message']
            ], 422);
        }

        // Correct admission lookup with session
        $admission = StudentAdmission::where('student_id', $validated['student_id'])
                            ->where('class_id', $validated['class_id'])
                            ->where('session_id', $validated['session_id'])
                            ->first();

       if (!$admission) {
            return response()->json([
                'success' => false,
                'message' => 'Student admission record not found.'
            ], 422);
        }

        $validated['student_admission_id'] = $admission->id;

        $existing = StudentsMark::where('student_admission_id', $validated['student_admission_id'])
                        ->where('subject_id', $validated['subject_id'])
                        ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Marks already entered for this subject in this session. You can only edit the existing entry.'
            ], 422);
        }

        StudentsMark::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Marks stored successfully!'
        ]);
    }

    // public function update(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'id' => 'required|exists:students_marks,id',
    //             'session_id'    => 'required|exists:academic_sessions,id',
    //             'student_id'    => 'required|exists:students,id',
    //             'class_id'      => 'required|exists:class_lists,id',
    //             'subject_id'    => 'required|exists:subjects,id',
    //             'term_one_out_off'      => 'nullable|integer',
    //             'term_one_stu_marks'    => 'nullable|numeric|required_with:term_one_out_off|max:100',
    //             'term_two_out_off'      => 'nullable|integer',
    //             'term_two_stu_marks'    => 'nullable|numeric|required_with:term_two_out_off|max:100',
    //             'mid_term_out_off'      => 'nullable|integer',
    //             'mid_term_stu_marks'    => 'nullable|numeric|required_with:mid_term_out_off|max:100',
    //             'final_exam_out_off'    => 'nullable|integer',
    //             'final_exam_stu_marks'  => 'nullable|numeric|required_with:final_exam_out_off|max:100',
    //         ]);

    //         $errors = [];

    //         if ($request->term_one_out_off && $request->term_one_stu_marks === null) {
    //             $errors['term_one_stu_marks'] = 'Term 1 marks required.';
    //         }
    //         if ($request->term_two_out_off && $request->term_two_stu_marks === null) {
    //             $errors['term_two_stu_marks'] = 'Term 2 marks required.';
    //         }
    //         if ($request->mid_term_out_off && $request->mid_term_stu_marks === null) {
    //             $errors['mid_term_stu_marks'] = 'Mid Term marks required.';
    //         }
    //         if ($request->final_exam_out_off && $request->final_exam_stu_marks === null) {
    //             $errors['final_exam_stu_marks'] = 'Final Exam marks required.';
    //         }

    //         if(
    //             empty($request->term_one_out_off) &&
    //             empty($request->term_two_out_off) &&
    //             empty($request->mid_term_out_off) &&
    //             empty($request->final_exam_out_off)
    //         ) {
    //             $errors['at_least_one_term'] = 'Select at least one term.';
    //         }

    //         if($request->term_one_out_off && $request->term_one_stu_marks > $request->term_one_out_off){
    //             $errors['term_one_stu_marks'] = 'Term 1 marks cannot be greater than term 1 out off.';
    //         }

    //         if($request->term_two_out_off && $request->term_two_stu_marks > $request->term_two_out_off){
    //             $errors['term_two_stu_marks'] = 'Term 2 marks cannot be greater than term 2 out off.';
    //         }

    //         if($request->mid_term_out_off && $request->mid_term_stu_marks > $request->mid_term_out_off){
    //             $errors['mid_term_stu_marks'] = 'Mid term marks cannot be greater than mid term out off.';
    //         }

    //         if($request->final_exam_out_off && $request->final_exam_stu_marks > $request->final_exam_out_off){
    //             $errors['final_exam_stu_marks'] = 'Final exam marks cannot be greater than final exam out off.';
    //         }

    //         if ($errors) {
    //             return response()->json(['success' => false, 'errors' => $errors], 422);
    //         }

    //         $mark = StudentsMark::findOrFail($validated['id']);

    //         $admission = StudentAdmission::firstOrCreate(
    //             [
    //                 'student_id' => $validated['student_id'],
    //                 'class_id' => $validated['class_id'],
    //                 'session_id' => $validated['session_id'],
    //             ],
    //             ['admission_date' => now()]
    //         );

    //         // Duplicate check
    //         $duplicate = StudentsMark::where('student_admission_id', $admission->id)
    //             ->where('subject_id', $validated['subject_id'])
    //             ->where('id', '!=', $validated['id'])
    //             ->first();

    //         if ($duplicate) {
    //             return response()->json([
    //                 'success' => false,
    //                 'errors' => ['duplicate_error' => 'Marks for this student, subject, and session already exist.']
    //             ], 422);
    //         }

    //         $mark->update([
    //             'student_id'    => $validated['student_id'],
    //             'class_id'      => $validated['class_id'],
    //             'session_id'    => $validated['session_id'],
    //             'student_admission_id'  => $admission->id,
    //             'subject_id'    => $validated['subject_id'],
    //             'term_one_out_off'      => $validated['term_one_out_off'] ?? null,
    //             'term_one_stu_marks'    => $validated['term_one_stu_marks'] ?? null,
    //             'term_two_out_off'      => $validated['term_two_out_off'] ?? null,
    //             'term_two_stu_marks'    => $validated['term_two_stu_marks'] ?? null,
    //             'mid_term_out_off'      => $validated['mid_term_out_off'] ?? null,
    //             'mid_term_stu_marks'    => $validated['mid_term_stu_marks'] ?? null,
    //             'final_exam_out_off'    => $validated['final_exam_out_off'] ?? null,
    //             'final_exam_stu_marks'  => $validated['final_exam_stu_marks'] ?? null,
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Student marks updated successfully.'
    //         ]);
    //     } catch (\Exception $e) {
    //         \Log::error('Marks update failed: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Something went wrong. Please try again later.'
    //         ], 500);
    //     }
    // }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'             => 'required|exists:students_marks,id',
                'session_id'     => 'required|exists:academic_sessions,id',
                'class_id'       => 'required|exists:class_lists,id',
                'student_id'     => 'required|exists:students,id',
                'subject_id'     => 'required|exists:subjects,id',

                'term_one_out_off'      => 'nullable|integer',
                'term_one_stu_marks'    => 'nullable|numeric|required_with:term_one_out_off',

                'term_two_out_off'      => 'nullable|integer',
                'term_two_stu_marks'    => 'nullable|numeric|required_with:term_two_out_off',

                'mid_term_out_off'      => 'nullable|integer',
                'mid_term_stu_marks'    => 'nullable|numeric|required_with:mid_term_out_off',

                'final_exam_out_off'    => 'nullable|integer',
                'final_exam_stu_marks'  => 'nullable|numeric|required_with:final_exam_out_off',
            ]);

            // --- Custom logic checks ---
            $errors = [];

            if (
                empty($request->term_one_out_off) &&
                empty($request->term_two_out_off) &&
                empty($request->mid_term_out_off) &&
                empty($request->final_exam_out_off)
            ) {
                $errors['message'] = 'Select at least one term.';
            }

            if ($request->term_one_out_off && $request->term_one_stu_marks === null) {
                $errors['message'] = 'Term 1 marks required.';
            }
            if ($request->term_two_out_off && $request->term_two_stu_marks === null) {
                $errors['message'] = 'Term 2 marks required.';
            }
            if ($request->mid_term_out_off && $request->mid_term_stu_marks === null) {
                $errors['message'] = 'Mid Term marks required.';
            }
            if ($request->final_exam_out_off && $request->final_exam_stu_marks === null) {
                $errors['message'] = 'Final Exam marks required.';
            }

            if ($request->term_one_out_off && $request->term_one_stu_marks > $request->term_one_out_off) {
                $errors['message'] = 'Term 1 marks cannot be greater than term 1 out off.';
            }
            if ($request->term_two_out_off && $request->term_two_stu_marks > $request->term_two_out_off) {
                $errors['message'] = 'Term 2 marks cannot be greater than term 2 out off.';
            }
            if ($request->mid_term_out_off && $request->mid_term_stu_marks > $request->mid_term_out_off) {
                $errors['message'] = 'Mid term marks cannot be greater than mid term out off.';
            }
            if ($request->final_exam_out_off && $request->final_exam_stu_marks > $request->final_exam_out_off) {
                $errors['message'] = 'Final exam marks cannot be greater than final exam out off.';
            }

            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => $errors['message']
                ], 422);
            }

            // --- Check student admission ---
            $admission = StudentAdmission::where('student_id', $validated['student_id'])
                ->where('class_id', $validated['class_id'])
                ->where('session_id', $validated['session_id'])
                ->first();

            if (!$admission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student admission record not found.'
                ], 422);
            }

            $validated['student_admission_id'] = $admission->id;

            // --- Duplicate check ---
            $existing = StudentsMark::where('student_admission_id', $validated['student_admission_id'])
                ->where('subject_id', $validated['subject_id'])
                ->where('id', '!=', $validated['id']) // exclude current record
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Marks already exist for this subject and session. Please update the existing one.'
                ], 422);
            }

            // --- Update record ---
            $mark = StudentsMark::findOrFail($validated['id']);

            $mark->update([
                'student_id'             => $validated['student_id'],
                'class_id'               => $validated['class_id'],
                'session_id'             => $validated['session_id'],
                'student_admission_id'   => $validated['student_admission_id'],
                'subject_id'             => $validated['subject_id'],

                'term_one_out_off'       => $validated['term_one_out_off'] ?? null,
                'term_one_stu_marks'     => $validated['term_one_stu_marks'] ?? null,

                'term_two_out_off'       => $validated['term_two_out_off'] ?? null,
                'term_two_stu_marks'     => $validated['term_two_stu_marks'] ?? null,

                'mid_term_out_off'       => $validated['mid_term_out_off'] ?? null,
                'mid_term_stu_marks'     => $validated['mid_term_stu_marks'] ?? null,

                'final_exam_out_off'     => $validated['final_exam_out_off'] ?? null,
                'final_exam_stu_marks'   => $validated['final_exam_stu_marks'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student marks updated successfully.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Marks update failed: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

   

    public function delete(Request $request){
        $user = StudentsMark::find($request->id); 
    
        if (!$user) {
            return response()->json([
                'status'    => 404,
                'message'   => 'user not found.',
            ]);
        }
    
        $user->delete(); 
        return response()->json([
            'status'    => 200,
            'message'   => 'Student Mark deleted successfully.',
        ]);
    }

    public function export(Request $request)
    {
        $studentName = $request->input('student_name');
        $classId = $request->input('class_filter');
        $subjectId = $request->input('subject_filter');
        $sessionId = $request->input('session_filter');

        $query = StudentsMark::with([
            'student',
            'class',
            'subjectlist',
            'studentAdmission.class',
            'studentAdmission.academicsession'
        ]);

        // Join with student name if filter is applied
        if (!empty($studentName)) {
            $query->whereHas('student', function ($q) use ($studentName) {
                $q->where('student_name', 'like', '%' . $studentName . '%');
            });
        }

        // Filter by class
        if (!empty($classId)) {
            $query->where('class_id', $classId);
        }

        // Filter by subject
        if (!empty($subjectId)) {
            $query->where('subject_id', $subjectId);
        }

        //Filter by session
        if (!empty($sessionId)) {
            $query->whereHas('studentAdmission', function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId);
            });
        }

        $marks = $query->latest()->get();

        if ($marks->isEmpty()) {
            return redirect()->back()->with('error', 'No records found to export.');
        }

        $filename = 'student_marks_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $delimiter = ",";
        $f = fopen('php://memory', 'w');

        // Header row
        fputcsv($f, [
            'Student Name',
            'Student ID',
            'Class',
            'Subject',
            'Academic Session',
            'Term One Marks',
            'Term One Out Of',
            'Term Two Marks',
            'Term Two Out Of',
            'Mid Term Marks',
            'Mid Term Out Of',
            // 'Final Exam Marks',
            // 'Final Exam Out Of',
            // 'Created Date'
        ], $delimiter);

        foreach ($marks as $mark) {
            fputcsv($f, [
                $mark->student->student_name ?? '',
                $mark->student->student_id ?? '',
                $mark->class->class ?? '',
                $mark->subjectlist->sub_name ?? '',
                $mark->studentAdmission->academicsession->session_name ?? '',
                $mark->term_one_stu_marks,
                $mark->term_one_out_off,
                $mark->term_two_stu_marks,
                $mark->term_two_out_off,
                $mark->mid_term_stu_marks,
                $mark->mid_term_out_off,
                // $mark->final_exam_stu_marks ?? '',
                // $mark->final_exam_out_off ?? '', 
                // optional($mark->created_at)->format('d-m-Y h:i A')
            ], $delimiter);
        }

        // Output file
        fseek($f, 0);
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        fpassthru($f);
        exit;
    }


}