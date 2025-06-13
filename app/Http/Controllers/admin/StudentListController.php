<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\AcademicSession;
use App\Models\ClassList;
use App\Models\SectionList;
use App\Models\StudentAdmission;

class StudentListController extends Controller
{
    public function index(Request $request) 
    {
        //dd($request->all());
        // if (Auth::guard('admin')->user()->user_type !== 'Admin') {
        //     abort(403, 'Unauthorized access.');
        // }

        $keyword = $request->input('keyword');
        $query = Student::query();

        $query->when($keyword, function ($q) use ($keyword) {
            $q->where(function($subQuery) use ($keyword) {
                $subQuery->where('student_name', 'like', '%'. $keyword . '%')
                            ->orWhere('student_id', 'like', '%'. $keyword . '%');
            });
        });
        
        $students = $query->latest('id')->paginate(10);

        return view('admin.student_management.studentlist', compact('students'));
    }

    public function create(){
        $sessions = AcademicSession::get();
        $classrooms = ClassList::where('status',1)->orderBy('class','ASC')->get();
        return view('admin.student_management.create_student',compact('sessions','classrooms'));
    }
    public function getSections(Request $request)
    {
        $classId = $request->classId;
        $SectionList = SectionList::where('class_list_id',$classId)->orderBy('section', 'ASC')->get();
       return response()->json([
        'success' => true,
        'sections' => $SectionList
    ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'phone_number' =>  ['required', 'regex:/^[0-9]{10}$/'],
            'parent_name' => 'required|string|max:255',
            'email'=> 'required|string',
            'address' => 'required|string',
            'session_id' => 'required|exists:academic_sessions,id',
            'class_id' => 'required|exists:class_lists,id',
            'section_id' => 'required',
            'roll_number' => 'required|integer',
            'admission_date' => 'required|date',
        ]);

        try {
            $student = Student::where('student_name', $request->student_name)
                ->where('date_of_birth', $request->date_of_birth)
                ->first();

            if (!$student) {
                $student = new Student();
                $student->student_id = Student::generateStudentUid();
                $student->student_name = $request->student_name;
                $student->gender = $request->gender;
                $student->date_of_birth = $request->date_of_birth;
                $student->phone_number = $request->phone_number;
                $student->parent_name = $request->parent_name;
                $student->email = $request->email;
                $student->address = $request->address;
                $student->save();
            }
            $alreadyAdmitted = StudentAdmission::where('student_id', $student->id)
                ->where('session_id', $request->session_id)
                ->exists();

            if ($alreadyAdmitted) {
                return back()->with('error', 'This student is already admitted in the selected session.');
            }

            StudentAdmission::create([
                'student_id' => $student->id,
                'session_id' => $request->session_id,
                'class_id' => $request->class_id,
                'section' => $request->section_id,
                'roll_number' => $request->roll_number,
                'admission_date' => $request->admission_date,
            ]);

            return redirect()->route('admin.studentlist')->with('success', 'Student admission successful!');

        } catch (\Exception $e) {
            \Log::error('Student Admission Error: '.$e->getMessage());
            return back()->with('error', 'Something went wrong while processing admission.');
        }
    }


    public function edit($id)
    {
        $student = Student::findOrFail($id);
        return view('admin.student_management.update_student', compact('student'));
    }

    public function update(Request $request,$id)
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required',
            'parent_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255', 
            'admission_date' => 'required|date',
            'class' => 'required|string|max:50',
            'section' => 'required|string|max:50',
            'roll_number' => 'required|integer',
        ]);

        $student = Student::findOrFail($id);
        $student->update($validated);

        return redirect()->route('admin.studentlist')
            ->with('success', 'Student updated successfully');
    }

    public function status($id)
    {
        $user = Student::findOrFail($id);

        $user->status = $user->status ? 0 : 1;
        $user->save();
        return response()->json([
            'status'  => 200,
            'message' => 'Status updated successfully'
        ]);
    }

    public function delete(Request $request)
    {
        $student = Student::find($request->id); 
    
        if (!$student) {
            return response()->json([
                'status'    => 404,
                'message'   => 'user not found.',
            ]);
        }
    
        $student->delete(); 
        return response()->json([
            'status'    => 200,
            'message'   => 'user deleted successfully.',
        ]);
    }

    public function admissionHistory($id)
    {
        $student = Student::findOrFail($id);
        $sessions = AcademicSession::all();
        $classes = ClassList::all();
        $admissionHistories = StudentAdmission::with(['student','class','session'])
                            ->where('student_id', $id)
                            ->orderBy('created_at', 'desc')
                            ->get();
        return view('admin.student_management.admission_history',compact('student','admissionHistories','sessions','classes'));
    }

    public function admissionhistoryUpdate(Request $request)
    {
        $request->validate([
            'id' => 'nullable',
            'session_id' => 'nullable',
            'class_id' => 'nullable',
            'section' => 'nullable',
            'roll_number' => 'nullable',
            'admission_date' => 'nullable|date',
        ]);

        $history = StudentAdmission::findOrFail($request->id);

        $history->update([
            'session_id' => $request->session_id,
            'class_id' => $request->class_id,
            'section' => $request->section,
            'roll_number' => $request->roll_number,
            'admission_date' => $request->admission_date,
        ]);

        return redirect()->back()->with('success', 'Admission history updated successfully.');
    }


    public function reAdmissionForm($id)
    {
        $student = Student::findOrFail($id);
        $classes = ClassList::all();
        $sessions = AcademicSession::all();

        return view('admin.student_management.re-admission', compact('student','classes','sessions'));
    }

    public function reAdmissionStore(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        //dd($request->all());
        $request->validate([
            'session_id' => 'required',
            'class_id'  => 'required',
            'section_id'  => 'required',
            'roll_number' => 'required',
            'admission_date' => 'required|date',
        ]);

        StudentAdmission::create([
            'student_id' => $student->id,
            'session_id' => $request->session_id,
            'class_id'  => $request->class_id,
            'section' => $request->section_id,
            'roll_number' => $request->roll_number,
            'admission_date' => $request->admission_date,
        ]);
        return redirect()->route('admin.student.admissionhistory', $student->id)->with('success', 'Re-admission Done Successfully');
    }
}
