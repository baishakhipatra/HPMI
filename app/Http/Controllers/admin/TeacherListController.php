<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\{Admin, ClassList, ClassWiseSubject, Subject, TeacherSubject, TeacherClass};

class TeacherListController extends Controller
{
    //
    public function index(Request $request) 
    {
        $keyword = $request->input('keyword');
        $query = Admin::where('user_type', 'Teacher')->with(['teacherClasses.ClassList', 'teacherSubjects.subject']);

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
        $classLists = ClassList::all();
        return view('admin.teacher_management.create',compact('user_id', 'classLists'));
    }

    public function store(Request $request) {
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'user_id'            => 'required|string|unique:admins,user_id',
            'user_type'          => 'required|in:Teacher,Employee,Admin',
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:admins,email',
            'mobile'             => [
                'required',
                'digits:10',
                Rule::unique('admins')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'date_of_birth'      => 'nullable|date',
            'date_of_joining'    => 'nullable|date',
            'qualifications'     => 'nullable|string|max:255',
            'address'            => 'nullable|string',
            'subjects_taught'    => 'nullable|array',
            'subjects_taught.*'  => 'nullable|exists:subjects,id',
            'classes_assigned'   => 'nullable|array',
            'classes_assigned.*' => 'nullable|exists:class_lists,id',
            'password'           => 'required|string|min:6',
        ]);

        // Custom DOB < DOJ validation
        $validator->after(function ($validator) use ($request) {
            if ($request->date_of_birth && $request->date_of_joining) {
                if ($request->date_of_birth >= $request->date_of_joining) {
                    $validator->errors()->add('date_of_birth', 'Date of Birth must be earlier than Date of Joining.');
                }
            }
        });

        // Return with errors if any
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        //Create the teacher (without class/subject)
        $teacher = Admin::create([
            'user_id'         => $request->user_id,
            'user_type'       => $request->user_type,
            'name'            => $request->name,
            'email'           => $request->email,
            'mobile'          => $request->mobile,
            'date_of_birth'   => $request->date_of_birth,
            'date_of_joining' => $request->date_of_joining,
            'qualifications'  => $request->qualifications,
            'address'         => $request->address,
            'password'        => Hash::make($request->password),
            'status'          => 1,
        ]);

        //  Save class associations
        if (is_array($request->classes_assigned)) {
            foreach ($request->classes_assigned as $classId) {
                TeacherClass::create([
                    'teacher_id' => $teacher->id,
                    'class_id'   => $classId,
                ]);
            }
        }

        // Save subject associations
        if (is_array($request->subjects_taught)) {
            foreach ($request->subjects_taught as $subjectId) {
                TeacherSubject::create([
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subjectId,
                ]);
            }
        }

        return redirect()->route('admin.teacher.index')->with('success', 'Teacher created successfully');
    }


    public function edit($id) {
        $data = Admin::findOrFail($id);
        $classLists = ClassList::all();

        $selectedClassIds = TeacherClass::where('teacher_id', $data->id)->pluck('class_id')->toArray();
        $selectedSubjectIds = TeacherSubject::where('teacher_id', $data->id)->pluck('subject_id')->toArray();
        $selectedClassIds = TeacherClass::where('teacher_id', $data->id)->pluck('class_id')->toArray();
        return view('admin.teacher_management.edit', compact('data', 'selectedSubjectIds', 'selectedClassIds', 'classLists'));
    }

    // public function update(Request $request) {
    //     $request->validate([
    //         'name'      => 'required|string|max:255',
    //         'address'   => 'nullable|string',
    //         'mobile'    => 'required|digits:10|unique:admins,mobile,' . $request->id,
    //         'email'     => 'required|email|unique:admins,email,' . $request->id,
    //     ]);

    //     $admin = Admin::findOrFail($request->id);
    //     $admin->update([
    //         'name'             => $request->name,
    //         //'user_name'        => $request->user_name,
    //         'user_type'        => $request->user_type ?? $admin->user_type,
    //         'mobile'           => $request->mobile,
    //         'email'            => $request->email,
    //         'date_of_birth'    => $request->date_of_birth,
    //         'date_of_joining'  => $request->date_of_joining,
    //         'qualifications'   => $request->qualifications,
    //         'subjects_taught'  => $request->subjects_taught,
    //         'classes_assigned' => $request->classes_assigned,
    //         'address'          => $request->address,
    //     ]);
    //     return redirect()->route('admin.teacher.index')->with('success', 'Teacher updated successfully!');
    // }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:admins,email,' . $request->id,
            'mobile'             => [
                'required',
                'digits:10',
                Rule::unique('admins')->ignore($request->id)->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'date_of_birth'      => 'nullable|date',
            'date_of_joining'    => 'nullable|date',
            'qualifications'     => 'nullable|string|max:255',
            'address'            => 'nullable|string',
            'subjects_taught'    => 'nullable|array',
            'subjects_taught.*'  => 'nullable|exists:subjects,id',
            'classes_assigned'   => 'nullable|array',
            'classes_assigned.*' => 'nullable|exists:class_lists,id',
        ]);

        // Custom DOB check
        $validator->after(function ($validator) use ($request) {
            if ($request->date_of_birth && $request->date_of_joining) {
                if ($request->date_of_birth >= $request->date_of_joining) {
                    $validator->errors()->add('date_of_birth', 'Date of Birth must be earlier than Date of Joining.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $admin = Admin::findOrFail($request->id);
        $admin->update([
            'name'             => $request->name,
            'email'            => $request->email,
            'mobile'           => $request->mobile,
            'date_of_birth'    => $request->date_of_birth,
            'date_of_joining'  => $request->date_of_joining,
            'qualifications'   => $request->qualifications,
            'address'          => $request->address,
        ]);

        // Update Class Relations
        TeacherClass::where('teacher_id', $admin->id)->delete();
        if (is_array($request->classes_assigned)) {
            foreach ($request->classes_assigned as $classId) {
                TeacherClass::create([
                    'teacher_id' => $admin->id,
                    'class_id'   => $classId,
                ]);
            }
        }

        // Update Subject Relations
        TeacherSubject::where('teacher_id', $admin->id)->delete();
        if (is_array($request->subjects_taught)) {
            foreach ($request->subjects_taught as $subjectId) {
                TeacherSubject::create([
                    'teacher_id' => $admin->id,
                    'subject_id' => $subjectId,
                ]);
            }
        }

        return redirect()->route('admin.teacher.index')->with('success', 'Teacher updated successfully');
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
        $classIds = $request->input('class_ids');

        if (is_array($classIds) && count($classIds) > 0) {
            $classWiseSubjects = ClassWiseSubject::with(['subject', 'classList'])
                ->whereIn('class_id', $classIds)
                ->get();
            // dd($classWiseSubjects);
            // Filter out entries where subject is null
            $validSubjects = $classWiseSubjects->filter(function ($item) {
                return !is_null($item->subject);
            })->values(); // Reset keys after filter

            if ($validSubjects->isNotEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Subjects fetched successfully.',
                    'data' => $validSubjects
                ], 200);
            } else {
                return response()->json([
                    'status' => true,
                    'message' => 'No subjects found for the selected class(es).',
                    'data' => []
                ], 200); // Not Found
            }
        } else {
            return response()->json([
                'status' => true,
                'message' => 'No class IDs provided.',
                'data' => []
            ], 200); // Bad Request
        }
    }



}
