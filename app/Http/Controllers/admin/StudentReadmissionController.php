<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Models\{Student,AcademicSession, ClassList, StudentAdmission, };
use Illuminate\Validation\Rule;

class StudentReadmissionController extends Controller
{

    public function index(Request $request) {
        $students  = collect();
        $selectedStudent = null;
        $admissionHistories = [];

        $admin = auth()->guard('admin')->user();

        if($request->filled('keyword')) {
            $keyword = $request->keyword;

            //Build base query
            $query = Student::query();

            //apply restriction
            if($admin && $admin->user_type === 'Teacher') {
                $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->toArray();

                $query->whereHas('admissions', function ($q) use ($assignedClassIds) {
                    $q->whereIn('class_id', $assignedClassIds);
                });
            }

            $query->where('student_name', 'like', '%' . $keyword . '%');

            $students = $query->get();

            if($students->count() === 1 && !$request->filled('student_id')) {
                $selectedStudent = $students->first();
            }

            // If student_id passed, get exact student
            if($request->filled('student_id')) {
                $selectedStudent = Student::where('student_id', $request->student_id)->first();
            }

            //Load admission histories only if a valid student is selected
            if($selectedStudent) {
                $admissionHistories = StudentAdmission::with(['session', 'class'])
                                        ->where('student_id', $selectedStudent->id)
                                        ->orderBy('created_at', 'desc')
                                        ->get();
            }
        }

        return view('admin.student_management.index', compact('students', 'selectedStudent', 'admissionHistories'));
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
    // public function admissionHistory(Request $request)
    // {
    //     $request->validate([
    //         'student_id' => 'required|exists:students,student_id'
    //     ]);

    //     $student = Student::where('student_id', $request->student_id)->firstOrFail();

    //     $admissionHistories = StudentAdmission::with(['session', 'class'])
    //         ->where('student_id', $student->id)
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     return view('admin.student_management.readmission.index', [
    //         'students' => collect(),
    //         'selectedStudent' => $student,
    //         'admissionHistories' => $admissionHistories
    //     ]);
    // }


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
}
