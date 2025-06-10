<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class UserListController extends Controller
{
    //
    public function index(Request $request) 
    {
        //dd($request->all());
        // if (Auth::guard('admin')->user()->user_type !== 'Admin') {
        //     abort(403, 'Unauthorized access.');
        // }

        $keyword = $request->input('keyword');
        $query = Admin::where('user_type', '!=', 'Admin');

        $query->when($keyword, function ($q) use ($keyword) {
            $q->where(function($subQuery) use ($keyword) {
                $subQuery->where('name', 'like', '%'. $keyword . '%')
                            ->orWhere('email', 'like', '%'. $keyword . '%')
                            ->orWhere('user_type', 'like', '%' . $keyword . '%');
            });
        });
        
        $admins = $query->latest('id')->paginate(10);

        return view('admin.user_management.userList', compact('admins'));
    }
    public function create(){
        $user_id = $this->generateTeacherId();
        return view('admin.user_management.create',compact('user_id'));
    }
    private function generateTeacherId(){
         do {
            // Count existing Admins and increment for next ID
            $count = Admin::count() + 1;

            // Generate ID in the format TCH0001, TCH0002, etc.
            $teacherId = 'EMP' . str_pad($count, 4, '0', STR_PAD_LEFT);

            // Check uniqueness in the database
            $exists = Admin::where('user_id', $teacherId)->exists();
        } while ($exists);

        return $teacherId;
    }
    public function store(Request $request) {
        // dd($request->all());
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
        $data = Admin::findOrFail($id);
        return view('admin.user_management.userListEdit', compact('data'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255',
            'user_type' => 'required',
            'mobile' => 'required|digits:10|unique:admins,mobile,' . $id,
            'email' => 'required|email|unique:admins,email,' . $id,
        ]);

        $admin = Admin::findOrFail($id);
        $admin->update([
            'name'   => $request->name,
            'user_name' => $request->user_name,
            'user_type' => $request->user_type,
            'mobile' => $request->mobile,
            'email'  => $request->email,
        ]);
        return redirect()->route('admin.userlist')->with('success', 'User updated successfully!');
    }

    public function status($id)
    {
        $user = Admin::findOrFail($id);

        if($user->user_type == 'admin') {
            return response()->json(['status' => 403, 'message' => 'Cannot change status of admin user']);
        }

        $user->status = $user->status ? 0 : 1;
        $user->save();
        return response()->json([
            'status'  => 200,
            'message' => 'Status updated successfully'
        ]);
    }

    public function delete(Request $request){
        $user = Admin::find($request->id); 
    
        if (!$user) {
            return response()->json([
                'status'    => 404,
                'message'   => 'user not found.',
            ]);
        }
    
        $user->delete(); 
        return response()->json([
            'status'    => 200,
            'message'   => 'user deleted successfully.',
        ]);
    }
}