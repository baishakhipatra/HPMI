<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;

class StudentListController extends Controller
{
    public function index(Request $request) 
    {
        //dd($request->all());
        if (Auth::guard('admin')->user()->user_type !== 'Admin') {
            abort(403, 'Unauthorized access.');
        }

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
        return view('admin.student_management.create_student');
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'parent_name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone_number' => 'required|string|min:10|max:10|regex:/^[0-9]{10}$/|unique:students,phone_number',
            'address' => 'required|string|max:255', 
            'admission_date' => 'required|date|after_or_equal:date_of_birth',
            'class' => 'required|string|max:50',
            'section' => 'required|string|max:50',
            'roll_number' => 'required|integer',
            ], [
            'phone_number.regex' => 'The phone number must be exactly 10 digits.',
            'phone_number.min' => 'The phone number must be exactly 10 digits.',
            'phone_number.max' => 'The phone number must be exactly 10 digits.',
            'admission_date.after_or_equal' => 'The admission date must be after or equal to date of birth.'
        ]);

        $studentId = 'STD' . str_pad(Student::max('id') + 1, 4, '0', STR_PAD_LEFT);
        $data = $request->all();
        $data['student_id'] = $studentId;

        Student::create($data);

        return redirect()->route('admin.studentlist')->with('success', 'Student added successfully!');
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
}
