<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\{Admin, ClassList, ClassWiseSubject, Subject};

class TeacherListController extends Controller
{
    //
    public function index(Request $request) 
    {
        $keyword = $request->input('keyword');
        $query = Admin::where('user_type', 'Teacher')->with(['class', 'subject']);

        $query->when($keyword, function ($q) use ($keyword) {
            $q->where(function($subQuery) use ($keyword) {
                $subQuery->where('name', 'like', '%'. $keyword . '%')
                            ->orWhere('email', 'like', '%'. $keyword . '%')
                            ->orWhere('qualifications', 'like', '%' . $keyword . '%')
                            ->orWhere('address', 'like', '%' . $keyword . '%');                           
            });
        });
        
        $admins = $query->latest('id')->paginate(10);

        return view('admin.teacher_management.index', compact('admins'));
    }
    public function create(){
        $user_id = generateTeacherId();
        return view('admin.teacher_management.create',compact('user_id'));
    }

    public function store(Request $request) {
        //dd($request->all());
        $request->validate([
            // 'user_id'          => 'nullable|string|unique:admins,user_id',
            'user_id'          => 'required|string|unique:admins,user_id',
            'user_type'        => 'required|in:Teacher,Employee,Admin',
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:admins,email',
            'mobile'           => 'required|digits:10|unique:admins,mobile',
            // 'mobile' => [
            // 'required',
            // 'digits:10',
            //     Rule::unique('mobile')->where(function ($query) {
            //         return $query->whereNull('deleted_at');
            //     }),
            // ],
            'date_of_birth'    => 'nullable|date',
            'date_of_joining'  => 'nullable|date',
            'qualifications'   => 'nullable|string|max:255',
            'address'          => 'nullable|string',
            'subjects_taught'  => 'nullable|string|max:255',
            'classes_assigned' => 'nullable|string|max:255',
            'password'         => 'required|string|min:6',
        ]);
         //dd($request->all());
        Admin::create([
            'user_id'          => $request->user_id,
            'user_type'        => $request->user_type,
            'name'             => $request->name,
            'email'            => $request->email,
            'mobile'           => $request->mobile,
            'date_of_birth'    => $request->date_of_birth,
            'date_of_joining'  => $request->date_of_joining,
            'qualifications'   => $request->qualifications,
            'address'          => $request->address,
            'subjects_taught'  => $request->subjects_taught,
            'classes_assigned' => $request->classes_assigned,
            'password'         => Hash::make($request->password),
            'user_type'        => $request->user_type,
            'status'           => 1,
        ]);
        //dd($request->subjects_taught);

        return redirect()->route('admin.teacher.index')->with('success', 'Teacher created successfully');
    }

    public function edit($id) {
        $data = Admin::findOrFail($id);
        return view('admin.teacher_management.edit', compact('data'));
    }

    public function update(Request $request) {
        $request->validate([
            'name'      => 'required|string|max:255',
            'address'   => 'nullable|string',
            'mobile'    => 'required|digits:10|unique:admins,mobile,' . $request->id,
            'email'     => 'required|email|unique:admins,email,' . $request->id,
        ]);

        $admin = Admin::findOrFail($request->id);
        $admin->update([
            'name'             => $request->name,
            //'user_name'        => $request->user_name,
            'user_type'        => $request->user_type ?? $admin->user_type,
            'mobile'           => $request->mobile,
            'email'            => $request->email,
            'date_of_birth'    => $request->date_of_birth,
            'date_of_joining'  => $request->date_of_joining,
            'qualifications'   => $request->qualifications,
            'subjects_taught'  => $request->subjects_taught,
            'classes_assigned' => $request->classes_assigned,
            'address'          => $request->address,
        ]);
        return redirect()->route('admin.teacher.index')->with('success', 'Teacher updated successfully!');
    }

    public function status($id)
    {
        $user = Admin::findOrFail($id);

        // if($user->user_type == 'admin') {
        //     return response()->json(['status' => 403, 'message' => 'Cannot change status of teacher']);
        // }

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
            'message'   => 'Teacher deleted successfully.',
        ]);
    }

    
   public function getSubjectsByClass(Request $request)
    {
        $classId = $request->class_id;

        // Get all class_id and subject_id pairs that are already assigned
        $assignedPairs = Admin::whereNotNull('classes_assigned')
            ->whereNotNull('subjects_taught')
            ->get()
            ->map(function ($admin) {
                return [
                    'class_id' => $admin->classes_assigned,
                    'subject_id' => $admin->subjects_taught,
                ];
            });

        // Get subjects assigned to this class
        $classWiseSubjects = ClassWiseSubject::with('subject')
            ->where('class_id', $classId)
            ->get()
            ->pluck('subject')
            ->filter(function ($subject) use ($classId, $assignedPairs) {
                if (!$subject) return false;

                // Check if this class-subject pair is already assigned
                foreach ($assignedPairs as $pair) {
                    if ($pair['class_id'] == $classId && $pair['subject_id'] == $subject->id) {
                        return false; // Already assigned
                    }
                }

                return true;
            })
            ->values();

        return response()->json($classWiseSubjects);
    }


}
