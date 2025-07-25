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
    public function index(Request $request) 
    {
        $keyword = $request->input('keyword');
            $query = Admin::where(function ($q) {
                $q->where('user_type', 'Teacher')
                ->orWhere('designation_id', '1');
        })->with(['teacherSubjects']);
        
        $query->when($keyword, function ($q) use ($keyword) {
            $q->where(function($subQuery) use ($keyword) {
                $subQuery->where('name', 'like', '%'. $keyword . '%')
                            ->orWhere('email', 'like', '%'. $keyword . '%')
                            ->orWhere('mobile', 'like', '%'. $keyword . '%')
                            ->orWhere('qualifications', 'like', '%' . $keyword . '%')
                            ->orWhere('address', 'like', '%' . $keyword . '%')  
                            ->orWhere('user_id', 'like', '%' . $keyword .  '%');                         
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
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'user_id'            => 'required|string|unique:admins,user_id',
            'user_type'          => 'required|in:Teacher,Employee,Admin',
            'designation_id'     => 'nullable|exists:designations,id',
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
            'subjects_taught.*'  => 'exists:class_wise_subjects,id',
            'classes_assigned'   => 'required|array',
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

            if ($request->date_of_birth && $request->date_of_birth > now()->toDateString()) {
                $validator->errors()->add('date_of_birth', 'Date of Birth cannot be in the future.');
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
            'designation_id'  => $request->designation_id,
        ]);
        // dd($request->all());
        //  Save class associations
        if (!empty($request->subjects_taught) && count($request->subjects_taught) > 0) {
            foreach ($request->subjects_taught as $classWiseSubjectId) {
                $getsubject = ClassWiseSubject::select(['class_id', 'subject_id'])->find($classWiseSubjectId);

                if ($getsubject) {
                    TeacherClass::create([
                        'teacher_id' => $teacher->id,
                        'class_id'   => $getsubject->class_id,
                    ]);
                    TeacherSubject::create([
                        'teacher_id' => $teacher->id,
                        'subject_id' => $getsubject->subject_id,
                        'class_id'   => $getsubject->class_id,
                    ]);
                }
            }
        }
        //dd($request->all());


        return redirect()->route('admin.teacher.index')->with('success', 'Teacher created successfully');
    }

    //for show details
    public function show($id) {
        $teacher = Admin::findOrFail($id);

        //get class names from TeacherClass
        $classIds   = TeacherClass::where('teacher_id', $id)->pluck('class_id');
        $classes    = ClassList::whereIn('id', $classIds)->pluck('class')->toArray();

        //Get subject names from TeacherSubject
        $teacherSubjects  = TeacherSubject::where('teacher_id', $id)
                                    ->with(['subjectList', 'classList'])
                                    ->get();

        $subjects  = [];
        foreach($teacherSubjects as $item) {
            if($item->subjectList && $item->classList) {
                $subjects[] = $item->classList->class. '-' . $item->subjectList->sub_name;
            }
        }
        return view('admin.teacher_management.show', compact('teacher', 'classes', 'subjects'));
    }


    public function edit($id) {
        $data = Admin::findOrFail($id);
        $classLists = ClassList::all();

        $selectedClassIds = TeacherClass::where('teacher_id', $data->id)->pluck('class_id')->toArray();
        $teacherSubjects = TeacherSubject::where('teacher_id', $data->id)->get();
        $selectedClassWiseSubjectIds = [];
        foreach($teacherSubjects as $teacherSubject) {
            $selectedClassWiseSubjectIds[] = ClassWiseSubject::where('subject_id', $teacherSubject->subject_id)
                      ->where('class_id', $teacherSubject->class_id)
                      ->value('id');
        }   
        //$selectedSubjectIds = TeacherSubject::where('teacher_id', $data->id)->pluck('subject_id')->toArray();
        $selectedSubjectIds = ClassWiseSubject::whereIn('subject_id',
            TeacherSubject::where('teacher_id', $data->id)->pluck('subject_id')
        )->pluck('id')->toArray();
        $selectedClassIds = TeacherClass::where('teacher_id', $data->id)->pluck('class_id')->toArray();
        return view('admin.teacher_management.edit', compact('data', 'selectedClassWiseSubjectIds', 'selectedClassIds', 'classLists'));
    }

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
            'subjects_taught.*'  => 'nullable|exists:class_wise_subjects,id',
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
          if (is_array($request->subjects_taught) && count($request->subjects_taught) > 0) {
            TeacherClass::where('teacher_id', $admin->id)->delete();
            TeacherSubject::where('teacher_id', $admin->id)->delete();

            foreach ($request->subjects_taught as $classWiseSubjectId) {
                $getsubject = ClassWiseSubject::select(['class_id', 'subject_id'])->where('id', $classWiseSubjectId)->first();
                TeacherClass::create([
                    'teacher_id' => $admin->id,
                    'class_id'   => $getsubject->class_id,
                ]);
                TeacherSubject::create([
                    'teacher_id' => $admin->id,
                    'subject_id' => $getsubject->subject_id,
                    'class_id'   => $getsubject->class_id,
                ]);
            }
        }

        return redirect()->route('admin.teacher.index')->with('success', 'Teacher updated successfully');
    }


    public function status($id)
    {
        $user = Admin::findOrFail($id);

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

   public function export(Request $request)
    {
        $keyword = $request->input('keyword');

        $query = Admin::where('user_type', 'Teacher')
                    ->with(['teacherSubjects.subjectList', 'teacherSubjects.classList']);

        // Filter by keyword
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('user_id', 'like', '%' . $keyword . '%')
                ->orWhere('name', 'like', '%' . $keyword . '%')
                ->orWhere('email', 'like', '%' . $keyword . '%')
                ->orWhere('mobile', 'like', '%' . $keyword . '%')
                ->orWhere('qualifications', 'like', '%' . $keyword . '%')
                ->orWhere('address', 'like', '%' . $keyword . '%');
            });
        }

        $teachers = $query->get();

        if ($teachers->count() > 0) {
            $delimiter = ",";
            $filename = "teachers_export_" . date('Y-m-d') . ".csv";
            $f = fopen('php://memory', 'w');

            // CSV Header
            $headers = [
                'Teacher ID',
                'Name',
                'Email',
                'Phone',
                'Date of Birth',
                'Date of Joining',
                'Address',
                'Qualification',
                'Subjects (Class-wise)',
                'Classes',
            ];
            fputcsv($f, $headers, $delimiter);

            foreach ($teachers as $teacher) {
                // Get unique class names
                $classes = $teacher->teacherSubjects->pluck('classList.class')->unique()->filter()->values()->toArray();
                $classesStr = implode(', ', $classes);

                // Get subject-class combinations
                $subjectCombos = [];
                foreach ($teacher->teacherSubjects as $ts) {
                    if ($ts->subjectList && $ts->classList) {
                        $subjectCombos[] = $ts->classList->class . ' - ' . $ts->subjectList->sub_name;
                    }
                }
                $subjectStr = implode(', ', array_unique($subjectCombos));

                $lineData = [
                    $teacher->user_id,
                    $teacher->name,
                    $teacher->email,
                    $teacher->mobile,
                    $teacher->date_of_birth,
                    $teacher->date_of_joining,
                    $teacher->address,
                    $teacher->qualifications,
                    $subjectStr,
                    $classesStr,
                ];

                fputcsv($f, $lineData, $delimiter);
            }

            fseek($f, 0);
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            fpassthru($f);
            exit;
        } else {
            return redirect()->back()->with('error', 'No records found to export.');
        }
    }

    
  public function getSubjectsByClass(Request $request)
    {
        $classIds = $request->input('class_ids');

        if (is_array($classIds) && count($classIds) > 0) {
            $classWiseSubjects = ClassWiseSubject::with(['subject', 'classList'])
                ->whereIn('class_id', $classIds)
                ->get();
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
