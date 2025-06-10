<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Teacher, Admin};

class TeacherListController extends Controller
{
    //
    public function index(Request $request) {
        $query = Admin::where('user_type', 'Teacher');

        $keyword = $request->input('keyword');

        $query->when($keyword, function ($q) use ($keyword) {
            $q->where(function($subQuery) use ($keyword) {
                $subQuery->where('name', 'like', '%'. $keyword . '%')
                    ->orWhere('email', 'like', '%'. $keyword . '%')
                    ->orWhere('teacher_id', 'like', '%'. $keyword . '%')
                    ->orWhere('phone', 'like', '%'. $keyword . '%')
                    ->orWhere('role', 'like', '%' . $keyword . '%');
            });
        });

        $teachers = $query->latest('id')->paginate(10);
        return view('admin.teacher_management.teacherList', compact('teachers'));
    }

    public function create() {
        return view('admin.teacher_management.teacherListCreate');
    }

    public function store(Request $request) {
        $request->validate([
            'teacher_id' => 'nullable|string',
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'phone' => 'required|string|max:20',
            'date_of_birth'     => 'nullable|date',
            'date_of_joining'   => 'nullable|date',
            'qualifications'    => 'nullable|string',
            'subjects_taught'   => 'nullable|string',
            'classes_assigned'  => 'nullable|string',
            'role' => 'required|string',
        ]);

        Admin::create($request->only([
            'teacher_id', 'name', 'email', 'phone',
            'date_of_birth', 'date_of_joining', 'qualifications',
            'subjects_taught', 'classes_assigned', 'role'
        ]));

        return redirect()->route('admin.teacherlist')->with('success', 'Teacher created successfully');
    }

    public function edit($id) {
        //dd($id);
        $data = Admin::findOrFail($id);
        return view('admin.teacher_management.teacherListEdit', compact('data'));
    }

    public function update(Request $request, $id) {
        
        $request->validate([
            'teacher_id' => 'nullable|string',
            'name'  => 'required|string|max:255',
            'phone' => 'required|digits:10|unique:teachers,mobile,' . $id,
            'email' => 'required|email|unique:teachers,email,' . $id,
            'date_of_birth'     => 'nullable|date',
            'date_of_joining'   => 'nullable|date',
            'qualifications'    => 'nullable|string',
            'subjects_taught'   => 'nullable|string',
            'classes_assigned'  => 'nullable|string',
            'role' => 'required|string',
        ]);

        $admin = Admin::findOrFail($id);
        $admin->update([
            'teacher_id'   => $request->teacher_id,
            'name'   => $request->name,
            'phone'  => $request->phone,
            'email'  => $request->email,
            'date_of_birth'     => $request->date_of_birth,
            'date_of_joining'   => $request->date_of_joining,
            'qualifications'    => $request->qualifications,
            'user_type' => $request->user_type,
            'subjects_taught'   => $request->subjects_taught,
            'classes_assigned'  => $request->classes_assigned,
            'role'   => $request->role,
        ]);
        return redirect()->route('admin.teacherlist')->with('success', 'Teacher updated successfully!');
    }

    public function delete(Request $request){
        $teacher = Admin::find($request->id); 
    
        if (!$teacher) {
            return response()->json([
                'status'    => 404,
                'message'   => 'Teacher not found.',
            ]);
        }
    
        $teacher->delete(); 
        return response()->json([
            'status'    => 200,
            'message'   => 'Teacher deleted successfully.',
        ]);
    }
}
