<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\{ClassList, Subject, Student, StudentAdmission,ClassWiseSubject, StudentsMark, AcademicSession, TeacherClass};

class StudentMarkListController extends Controller
{

    public function index(Request $request)
    {
        $admin = auth()->guard('admin')->user();
        $isTeacher = ($admin && $admin->user_type === 'Teacher');

        // Fetch sessions (available for all users)
        $sessions = StudentAdmission::with('session')
            ->select('session_id')
            ->distinct()
            ->get();

        // Fetch classes based on user type
        $classesQuery = ClassList::with('sections');
        if ($isTeacher) {
            $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->unique()->toArray(); // Ensure unique IDs
            $classesQuery->whereIn('id', $assignedClassIds);
        }
        $classes = $classesQuery->get();

        // Prepare subjects for the initial modal load (filtered by teacher's classes or all unique class-wise subjects for admin)
        $subjectsQuery = ClassWiseSubject::with('subject');
        if ($isTeacher) {
            $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->unique()->toArray(); // Ensure unique IDs
            $subjectsQuery->whereIn('class_id', $assignedClassIds);
        }

        $subjects = $subjectsQuery->get()
            ->filter(fn($item) => $item->subject !== null) // Remove entries if subject is soft-deleted or null
            ->unique('subject_id') // Crucially, get unique subjects by their ID
            ->map(function ($item) { // Map to a simpler structure for the view
                return (object)['id' => $item->subject->id, 'sub_name' => $item->subject->sub_name];
            })
            ->values(); // Reset array keys after unique and map

        $academicSessions = AcademicSession::all();

        // Prepare class options for filtering/modal (if needed)
        $classOptions = $classes->map(function ($class) {
            return [
                'id' => $class->id,
                'name' => $class->class
            ];
        });

        // Build base marks query
        $query = StudentsMark::with(['student', 'class', 'subjectlist', 'studentAdmission.session', 'studentAdmission.class']);

        // Apply teacher-specific filtering for the main marks table
        if ($isTeacher) {
            $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->unique()->toArray();
            $query->whereIn('class_id', $assignedClassIds);
        }

        // Apply filters from request
        if ($request->filled('student_name')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('student_name', 'LIKE', '%' . $request->student_name . '%');
            });
        }

        if ($request->filled('class_filter')) {
            $query->where('class_id', $request->class_filter);
        }

        if ($request->filled('subject_filter')) {
            $query->where('subject_id', $request->subject_filter);
        }

        if ($request->filled('session_filter')) {
            $query->whereHas('studentAdmission', function ($q) use ($request) {
                $q->where('session_id', $request->session_filter);
            });
        }
        
        // First get all records (you can apply limit if needed for performance)
        $allMarks = $query->latest('id')->get()
            ->filter(fn ($item) => $item->studentAdmission !== null);

        // Group by student-session-class combination
        $grouped = $allMarks->groupBy(fn ($item) =>
            $item->studentAdmission->student_id . '_' .
            $item->studentAdmission->session_id . '_' .
            $item->studentAdmission->class_id
        );

        // Paginate by grouped student sections (not individual marks)
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $pagedData = $grouped->slice(($currentPage - 1) * $perPage, $perPage);

        $groupedMarks = new LengthAwarePaginator(
            $pagedData,
            $grouped->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        return view('admin.student_marks.index', compact(
            'classes', 'subjects', 'classOptions', 'sessions', 'groupedMarks', 'academicSessions'
        ));
    }


    public function getStudentsBySession(Request $request)
    {
        $sessionId = $request->sessionId;
        $admin = auth()->guard('admin')->user();
        $isTeacher = ($admin && $admin->user_type === 'Teacher');

        $query = StudentAdmission::where('session_id', $sessionId)
                    ->with('student')
                    ->select('student_id')
                    ->distinct();

        // For teachers, further filter students by assigned classes in this session
        if ($isTeacher) {
            $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->unique()->toArray();
            $query->whereIn('class_id', $assignedClassIds);
        }

        $students = $query->get()
            ->filter(fn($admission) => $admission->student !== null) // Filter out if student record is missing
            ->map(function($admission) {
                return [
                    'id' => $admission->student->id, // Assuming student model has an 'id'
                    'name' => $admission->student->student_name // Assuming student model has a 'student_name'
                ];
            });

        return response()->json(['success' => true, 'students' => $students]);
    }

    public function getClassBySessionAndStudent(Request $request)
    {
        $student_id = $request->student_id;
        $session_id = $request->session_id;

        $admission = StudentAdmission::where('student_id', $student_id)
            ->where('session_id', $session_id)
            ->first();

        if (!$admission) {
            return response()->json(['success' => false, 'message' => 'No record found for this student in this session.']);
        }

        $class_id = $admission->class_id;

        // Fetch subjects specifically for this class_id
        $classSubjects = ClassWiseSubject::with('subject')
            ->where('class_id', $class_id)
            ->get()
            ->filter(fn($s) => $s->subject !== null); // Ensure subject relation exists

        $subjects = $classSubjects->map(function ($row) {
            return [
                'id' => $row->subject->id,
                'name' => $row->subject->sub_name,
            ];
        });

        // Fetch the class name for the response
        $class_name = optional($admission->class)->class; // Use optional to prevent error if class relation is null

        return response()->json([
            'success' => true,
            'subjects' => $subjects,
            'classes' => [
                ['id' => $admission->class_id, 'name' => $class_name]
            ],
        ]);
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
            'session_id' => 'required|exists:academic_sessions,id',
            'class_id' => 'required|exists:class_lists,id',
            'student_id' => 'required|exists:students,id',

            'subject_id' => 'required|array|min:1',
            'subject_id.*' => 'required|exists:subjects,id',

            'mid_term_out_off' => 'required|array',
            'mid_term_out_off.*' => 'nullable|integer',

            'mid_term_stu_marks' => 'required|array',
            'mid_term_stu_marks.*' => 'nullable|numeric',

            'final_exam_out_off' => 'required|array',
            'final_exam_out_off.*' => 'nullable|integer',

            'final_exam_stu_marks' => 'required|array',
            'final_exam_stu_marks.*' => 'nullable|numeric',
        ]);

        // Get student_admission_id
        $admission = StudentAdmission::where('student_id', $request->student_id)
            ->where('class_id', $request->class_id)
            ->where('session_id', $request->session_id)
            ->first();

        if (!$admission) {
            return response()->json([
                'success' => false,
                'message' => 'Student admission record not found.'
            ], 422);
        }

        $errors = [];

        foreach ($request->subject_id as $index => $subjectId) {
            $midOutOf = $request->mid_term_out_off[$index];
            $midMarks = $request->mid_term_stu_marks[$index];
            $finalOutOf = $request->final_exam_out_off[$index];
            $finalMarks = $request->final_exam_stu_marks[$index];

            if (empty($midOutOf) && empty($finalOutOf)) {
                $errors[] = "Select at least one term for subject index $index.";
                continue;
            }

            if ($midOutOf && $midMarks === null) {
                $errors[] = "Mid Term marks required for subject index $index.";
            }

            if ($finalOutOf && $finalMarks === null) {
                $errors[] = "Final Exam marks required for subject index $index.";
            }

            if ($midOutOf && $midMarks > $midOutOf) {
                $errors[] = "Mid Term marks cannot be greater than out of for subject index $index.";
            }

            if ($finalOutOf && $finalMarks > $finalOutOf) {
                $errors[] = "Final Exam marks cannot be greater than out of for subject index $index.";
            }

            // Check if mark already exists
            $existing = StudentsMark::where('student_admission_id', $admission->id)
                ->where('subject_id', $subjectId)
                ->first();

            if ($existing) {
                // $errors[] = "Marks already entered for subject (ID: $subjectId). Edit existing entry.";
                $subjectName = Subject::find($subjectId)?->sub_name ?? 'Unknown Subject';
                $errors[] = "Marks already entered for subject: {$subjectName}. Edit existing entry.";
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => implode(' ', $errors),
            ], 422);
        }

        // Save all valid entries
        foreach ($request->subject_id as $index => $subjectId) {
            StudentsMark::create([
                'student_admission_id' => $admission->id,
                'session_id' => $request->session_id,
                'class_id' => $request->class_id,
                'student_id' => $request->student_id,
                'subject_id' => $subjectId,

                'mid_term_out_off' => $request->mid_term_out_off[$index],
                'mid_term_stu_marks' => $request->mid_term_stu_marks[$index],

                'final_exam_out_off' => $request->final_exam_out_off[$index],
                'final_exam_stu_marks' => $request->final_exam_stu_marks[$index],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Marks stored successfully!'
        ]);
    }


    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'             => 'required|exists:students_marks,id',
                'session_id'     => 'required|exists:academic_sessions,id',
                'class_id'       => 'required|exists:class_lists,id',
                'student_id'     => 'required|exists:students,id',
                'subject_id'     => 'required|exists:subjects,id',



                'mid_term_out_off'      => 'nullable|integer',
                'mid_term_stu_marks'    => 'nullable|numeric|required_with:mid_term_out_off',

                'final_exam_out_off'    => 'nullable|integer',
                'final_exam_stu_marks'  => 'nullable|numeric|required_with:final_exam_out_off',
            ]);

            // --- Custom logic checks ---
            $errors = [];

            if (
    
                empty($request->mid_term_out_off) &&
                empty($request->final_exam_out_off)
            ) {
                $errors['message'] = 'Select at least one term.';
            }

           
            if ($request->mid_term_out_off && $request->mid_term_stu_marks === null) {
                $errors['message'] = 'Mid Term marks required.';
            }
            if ($request->final_exam_out_off && $request->final_exam_stu_marks === null) {
                $errors['message'] = 'Final Exam marks required.';
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
        $admin = auth()->guard('admin')->user();
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

         // If teacher, restrict to only assigned classes
        if ($admin && $admin->user_type === 'Teacher') {
            $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->toArray();
            $query->whereIn('class_id', $assignedClassIds);
        }

        if (!empty($studentName)) {
            $query->whereHas('student', function ($q) use ($studentName) {
                $q->where('student_name', 'like', '%' . $studentName . '%');
            });
        }

        if (!empty($classId)) {
            $query->where('class_id', $classId);
        }

        if (!empty($subjectId)) {
            $query->where('subject_id', $subjectId);
        }

        if (!empty($sessionId)) {
            $query->whereHas('studentAdmission', function ($q) use ($sessionId) {
                $q->where('session_id', $sessionId);
            });
        }

        $allMarks = $query->get();

        if ($allMarks->isEmpty()) {
            return redirect()->back()->with('error', 'No records found to export.');
        }

        $allSubjects = $allMarks->pluck('subjectlist.sub_name')->filter()->unique()->map(function ($item) {
            return strtoupper($item);
        })->sort()->values()->toArray();
        
        $grouped = $allMarks->groupBy(function ($item) {
            return $item->student_id;
        });

        $filename = 'student_marks_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $delimiter = ",";
        $f = fopen('php://memory', 'w');

       
        $header = ['NAME', 'STUDENT ID', 'CLASS', 'SESSION'];
        $header = array_merge($header, $allSubjects, ['TOTAL', 'Academic %']);
        fputcsv($f, $header, $delimiter);

        foreach ($grouped as $student_id => $marksByStudent) {
            $sessionGrouped = $marksByStudent->groupBy(function ($item) {
                return $item->studentAdmission->session_id;
            });

            $firstRow = true;
            foreach ($sessionGrouped as $sessionId => $studentMarks) {
                $firstMark = $studentMarks->first();
                $studentObj = $firstMark->student;
                $classObj = $firstMark->studentAdmission->class;
                $sessionObj = $firstMark->studentAdmission->academicsession;

                $stuName = $firstRow ? $studentObj->student_name : '';
                $stuId = $firstRow ? $studentObj->student_id : '';
                $firstRow = false;

                $className = $classObj->class ?? '';
                $sessionName = $sessionObj->session_name ?? '';

                $subjectTotals = [];
                foreach ($allSubjects as $sub) {
                    $subjectTotals[$sub] = 0;
                }

                $totalObtained = 0;
                $totalOutOf = 0;

                foreach ($studentMarks as $mark) {
                    $subjectName = strtoupper($mark->subjectlist->sub_name ?? '');

                    $obtained = ($mark->mid_term_stu_marks ?? 0) +
                                ($mark->final_exam_stu_marks ?? 0);

                    $outOf =($mark->mid_term_out_off ?? 0) +
                            ($mark->final_exam_out_off ?? 0);

                    if (isset($subjectTotals[$subjectName])) {
                        $subjectTotals[$subjectName] = $obtained;
                    }

                    $totalObtained += $obtained;
                    $totalOutOf += $outOf;
                }

                $academicPercentage = $totalOutOf > 0 ? round(($totalObtained / $totalOutOf) * 100, 2) . '%' : '0%';

                $row = [$stuName, $stuId, $className, $sessionName];
                foreach ($allSubjects as $subject) {
                    $row[] = $subjectTotals[$subject] ?? 0;
                }

                $row[] = $totalObtained;
                $row[] = $academicPercentage;

                fputcsv($f, $row, $delimiter);
            }
        }

        fseek($f, 0);
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        fpassthru($f);
        exit;
    }





}