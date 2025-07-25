<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\{Student,AcademicSession, ClassList, StudentAdmission, };
use Illuminate\Validation\Rule;

class StudentReadmissionController extends Controller
{

    // public function index(Request $request) {
    //     $students  = collect();
    //     $selectedStudent = null;
    //     $admissionHistories = [];

    //     $admin = auth()->guard('admin')->user();

    //     if($request->filled('keyword')) {
    //         $keyword = $request->keyword;

    //         //Build base query
    //         $query = Student::query();

    //         //apply restriction
    //         if($admin && $admin->user_type === 'Teacher') {
    //             $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->toArray();

    //             $query->whereHas('admissions', function ($q) use ($assignedClassIds) {
    //                 $q->whereIn('class_id', $assignedClassIds);
    //             });
    //         }

    //         $query->where('student_name', 'like', '%' . $keyword . '%');

    //         $students = $query->get();

    //         if($students->count() === 1 && !$request->filled('student_id')) {
    //             $selectedStudent = $students->first();
    //         }

    //         // If student_id passed, get exact student
    //         if($request->filled('student_id')) {
    //             $selectedStudent = Student::where('student_id', $request->student_id)->first();
    //         }

    //         //Load admission histories only if a valid student is selected
    //         if($selectedStudent) {
    //             $admissionHistories = StudentAdmission::with(['session', 'class'])
    //                                     ->where('student_id', $selectedStudent->id)
    //                                     ->orderBy('created_at', 'desc')
    //                                     ->get();
    //         }
    //     }

    //     return view('admin.student_management.index', compact('students', 'selectedStudent', 'admissionHistories'));
    // }
    
    public function index(Request $request)
    {
        $students = collect();
        $class = null;
        $admin = auth()->guard('admin')->user();

        $classes = ClassList::all(); // all class options
        $sessions = AcademicSession::all(); // all session options

        if ($request->filled('class_id')) {
            $class = ClassList::find($request->class_id);

            //  Restrict to teacher's assigned classes
            if ($admin && $admin->user_type === 'Teacher') {
                $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->toArray();

                if (!in_array($request->class_id, $assignedClassIds)) {
                    return back()->withErrors(['class_id' => 'You are not authorized to access this class.']);
                }
            }

            $students = Student::with(['latestAdmission' => function ($q) {
                    $q->with('session');
                }])
                ->whereHas('admissions', function ($q) use ($request) {
                    $q->where('class_id', $request->class_id);
                })
                ->get()
                ->filter(function ($stu) use ($request) {
                    return optional($stu->latestAdmission)->class_id == $request->class_id;
                });

            //  Prepare promotion-related info
            // foreach ($students as $stu) {
            //     $fromClassId = optional($stu->latestAdmission)->class_id;
            //     $fromSessionId = optional($stu->latestAdmission)->session_id;

            //     // Next session
            //     $nextSession = AcademicSession::where('id', '>', $fromSessionId)->orderBy('id')->first();
            //     $stu->to_session = $nextSession;

            //     // To class = current and next
            //     $fromClass = ClassList::find($fromClassId);
            //     $nextClass = ClassList::where('id', '>', $fromClassId)->orderBy('id')->first();
            //     $stu->available_classes = collect([$fromClass, $nextClass])->filter();
            //     $stu->to_sections = $stu->available_classes->flatMap(fn ($cls) => $cls->sections);
            //     $stu->previous_session_name = optional($stu->latestAdmission->session)->session_name;
            // }
            foreach ($students as $stu) {
                $fromClassId = optional($stu->latestAdmission)->class_id;
                $fromSessionId = optional($stu->latestAdmission)->session_id;

                // Next session
                $nextSession = AcademicSession::where('id', '>', $fromSessionId)->orderBy('id')->first();
                $stu->to_session = $nextSession;

                // From and next classes
                $fromClass = ClassList::find($fromClassId);
                $nextClass = ClassList::where('id', '>', $fromClassId)->orderBy('id')->first();

                $stu->to_class = $fromClass; // <-- This fixes the view display
                $stu->available_classes = collect([$fromClass, $nextClass])->filter();
                $stu->to_sections = $stu->available_classes->flatMap(fn ($cls) => $cls->sections);

                $stu->previous_session_name = optional($stu->latestAdmission->session)->session_name;
            }

        }

        return view('admin.student_management.index', compact('students', 'class', 'classes', 'sessions'));
    }



    public function autocomplete(Request $request)
    {
        $keyword = $request->get('keyword');

        $students = Student::query()
            ->where('student_name', 'like', $keyword . '%')
            ->select('student_id', 'student_name')
            ->limit(10)
            ->get();

        return response()->json($students);
    }




    public function admissionHistory(Request $request)
    {
        $student = Student::with('admissions')->findOrFail($request->student_id);
        $sessions = AcademicSession::all();
        $classes = ClassList::all();
        $admissionHistories = StudentAdmission::with(['class', 'session'])
                            ->where('student_id', $student->id)
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('admin.student_management.admission_history', compact('student', 'admissionHistories', 'sessions', 'classes'));
    }


    public function admissionhistoryUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:student_admissions,id',
            'session_id' => 'required|integer',
            'class_id' => 'required|integer',
            'section_id' => 'required',
            'roll_number' => 'required|numeric',
            'admission_date' => 'required|date',
        ]);

        $history = StudentAdmission::findOrFail($request->id);

        $alreadyAdmitted = StudentAdmission::where('student_id', $history->student_id)
            ->where('session_id', $request->session_id)
            ->where('id', '!=', $history->id)
            ->exists();

        if ($alreadyAdmitted) {
            return back()->withErrors(['session_id' => 'Student already admitted in this session.'])->withInput();
        }

        $history->update([
            'session_id' => $request->session_id,
            'class_id' => $request->class_id,
            'section' => $request->section_id,
            'roll_number' => $request->roll_number,
            'admission_date' => $request->admission_date,
        ]);

        return back()->with('success', 'Admission history updated successfully.');
    }

    public function reAdmissionForm($id)
    {
        createNewSession();
        $student = Student::findOrFail($id);
        $classes = ClassList::all();
        $sessions = AcademicSession::all();

        return view('admin.student_management.re-admission', compact('student', 'classes', 'sessions'));
    }

    public function reAdmissionStore(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'session_id' => 'required',
            'class_id'  => 'required',
            'section_id'  => 'required',
            'roll_number' => [
                'required',
                'integer',
                Rule::unique('student_admissions')->where(function ($query) use ($request) {
                    return $query->where('class_id', $request->class_id)
                                ->where('section', $request->section_id);
                }),
            ],
            'admission_date' => 'required|date',
        ]);

        $alreadyAdmitted = StudentAdmission::where('student_id', $student->id)
                        ->where('session_id', $request->session_id)
                        ->exists();

        if ($alreadyAdmitted) {
            return back()->withErrors(['session_id' => 'Student already admitted in this session.'])->withInput();
        }

        StudentAdmission::create([
            'student_id' => $student->id,
            'session_id' => $request->session_id,
            'class_id'  => $request->class_id,
            'section' => $request->section_id,
            'roll_number' => $request->roll_number,
            'admission_date' => $request->admission_date,
        ]);

        return redirect()->route('admin.student.readmission.index', ['keyword' => $student->student_name])->with('success', 'Re-admission done successfully.');
    }

    // public function bulkStore(Request $request)
    // {
    //     $admission_date = now();
    //     $errors = [];

    //     foreach ($request->students as $index => $studentData) {
    //         $session_id = $studentData['to_session'] ?? null;

    //         if (
    //             $session_id &&
    //             !empty($studentData['section']) &&
    //             !empty($studentData['roll_number'])
    //         ) {
    //             // Prevent duplicate session admission in another class
    //             $existing = StudentAdmission::where('student_id', $studentData['student_id'])
    //                 ->where('session_id', $session_id)
    //                 ->where('class_id', '!=', $studentData['to_class_id']) // different class
    //                 ->first();

    //             if ($existing) {
    //                 $errors[] = "Student ID {$studentData['student_id']} is already admitted in session ID {$session_id} but in a different class.";
    //                 continue;
    //             }

    //             // Skip if already admitted in same session and class (duplicate check)
    //             $exists = StudentAdmission::where('student_id', $studentData['student_id'])
    //                 ->where('session_id', $session_id)
    //                 ->where('class_id', $studentData['to_class_id'])
    //                 ->exists();

    //             if (!$exists) {
    //                 StudentAdmission::create([
    //                     'student_id' => $studentData['student_id'],
    //                     'session_id' => $session_id,
    //                     'class_id' => $studentData['to_class_id'],
    //                     'section' => $studentData['section'],
    //                     'roll_number' => $studentData['roll_number'],
    //                     'admission_date' => $admission_date,
    //                 ]);
    //             }
    //         }
    //     }

    //     if (!empty($errors)) {
    //         return back()->withErrors($errors);
    //     }

    //     return back()->with('success', 'Selected students promoted successfully.');
    // }

    public function bulkStore(Request $request)
    {
        $admission_date = now();
        $errors = [];

        foreach ($request->students as $studentId => $studentData) {
            $session_id = $studentData['to_session'] ?? null;
            $class_id = $studentData['to_class_id'] ?? null;
            $section = $studentData['section'] ?? null;
            $roll = $studentData['roll_number'] ?? null;

            // Validation
            if (!$session_id || !$class_id || !$section || !$roll) {
                $errors[$studentId] = 'Please fill all required fields: class, section, and roll number.';
                continue;
            }

            if (!is_numeric($roll)) {
                $errors[$studentId] = 'Roll number must be a number.';
                continue;
            }

            // Duplicate check
            $exists = StudentAdmission::where('session_id', $session_id)
                ->where('class_id', $class_id)
                ->where('section', $section)
                ->where('roll_number', $roll)
                ->exists();

            if ($exists) {
                $errors[$studentId] = "Roll number {$roll} already exists for the selected class, section, and session.";
                continue;
            }

            // Skip if already admitted
            $alreadyAdmitted = StudentAdmission::where('student_id', $studentData['student_id'])
                ->where('session_id', $session_id)
                ->exists();

            if ($alreadyAdmitted) continue;

            // Store
            StudentAdmission::create([
                'student_id' => $studentData['student_id'],
                'session_id' => $session_id,
                'class_id' => $class_id,
                'section' => $section,
                'roll_number' => $roll,
                'admission_date' => $admission_date,
            ]);
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        return back()->with('success', 'Selected students promoted successfully.');
    }




}
