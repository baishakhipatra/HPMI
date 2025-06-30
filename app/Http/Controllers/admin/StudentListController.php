<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\{Student, AcademicSession, ClassList, SectionList, StudentAdmission, progressList,StudentProgressCategory,StudentProgressMarking};

class StudentListController extends Controller
{
   
    public function index(Request $request) 
    {
        $keyword = $request->input('keyword');

        $query = Student::with('admission.session');

        $query->when($keyword, function ($q) use ($keyword) {
            $q->where(function($subQuery) use ($keyword) {
                $subQuery->where('student_name', 'like', '%'. $keyword . '%')
                        ->orWhere('student_id', 'like', '%'. $keyword . '%')
                        ->orWhere('gender', 'like', '%'. $keyword . '%')
                        ->orWhere('parent_name', 'like', '%'. $keyword . '%')
                        ->orWhere('father_name', 'like', '%'. $keyword . '%')
                        ->orWhere('mother_name', 'like', '%'. $keyword . '%')
                        ->orWhere('aadhar_no', 'like', '%'. $keyword . '%')
                        ->orWhere('blood_group', 'like', '%'. $keyword . '%')
                        ->orWhere('height', 'like', '%'. $keyword . '%')
                        ->orWhere('weight', 'like', '%'. $keyword . '%')
                        ->orWhere('email', 'like', '%'. $keyword . '%')
                        ->orWhere('phone_number', 'like', '%'. $keyword . '%')
                        ->orWhere('address', 'like', '%'. $keyword . '%');
            });
            // ->orWhereHas('session', function ($sessionQuery) use ($keyword) {
            //     $sessionQuery->where('session_name', 'like', '%'. $keyword . '%');
            // });
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
            'student_name'    => 'required|string|max:255',
            // 'date_of_birth'   => 'required|date',
            'date_of_birth'   => [
                'required',
                'date',
                'before:today',
                function($attribute, $value, $fail) use ($request) {
                    if($request->admission_date) {
                        $dobTimestamp  = strtotime($value);
                        $admissionTimestamp = strtotime($request->admission_date);
                        if($dobTimestamp > $admissionTimestamp) {
                            $fail('The date of birth can not after the admission date');
                        }
                    }
                },
            ],
            'gender'          => 'required|in:Male,Female,Other',
            'parent_name'     => 'required|string|max:255',
            'email'           => 'nullable|email',
            'phone_number'    => ['nullable', 'regex:/^[0-9]{10}$/'],
            'address'         => 'nullable|string|max:255',
            'admission_date'  => 'required|date',
            'class_id'        => 'required|exists:class_lists,id',
            'section_id'      => 'required|string',
            'roll_number'     => 'nullable|integer',
            'session_id'      => 'required|exists:academic_sessions,id',

            // Optional fields
            'aadhar_no'       => [
                'nullable',
                'regex:/^[0-9]{12}$/',
                Rule::unique('students')->whereNull('deleted_at'),
            ],
            'blood_group'     => 'nullable|string|max:10',
            'height'          => 'nullable|string',
            'weight'          => 'nullable|string',
            'father_name'     => 'nullable|string|max:255',
            'mother_name'     => 'nullable|string|max:255',
            'divyang'         => 'required|in:Yes,No',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ],[
            'phone_number.regex' => 'Phone number should be exactly 10 digits.',
            'aadhar_no.regex'    => 'Aadhaar number should be exactly 12 digits.',
        ]);

        try {
            $generatedId = Student::generateStudentUid();

            $imagePath = null;
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $file      = $request->file('image');
                $fileName  = time() . rand(10000, 99999) . '.' . $file->extension();
                $filePath  = 'uploads/students/' . $fileName;
                $file->move(public_path('uploads/students'), $fileName);
                $imagePath = $filePath;
            }
            $student = Student::create([
                'student_id'     => $generatedId, // 
                'student_name'   => $request->student_name,
                'date_of_birth'  => $request->date_of_birth,
                'gender'         => $request->gender,
                'parent_name'    => $request->parent_name,
                'email'          => $request->email,
                'phone_number'   => $request->phone_number,
                'address'        => $request->address,
                'aadhar_no'      => $request->aadhar_no,
                'blood_group'    => $request->blood_group,
                'height'         => $request->height,
                'weight'         => $request->weight,
                'father_name'    => $request->father_name,
                'mother_name'    => $request->mother_name,
                'divyang'        => $request->divyang,
                'image'          => $imagePath, // Store image path if exists
            ]);

            // Create admission
            $admission = StudentAdmission::create([
                'student_id'     => $student->id,
                'session_id'     => $request->session_id,
                'class_id'       => $request->class_id,
                'section'        => $request->section_id,
                'roll_number'    => $request->roll_number,
                'admission_date' => $request->admission_date,
            ]);

            // Optional: update admission_id in students table
            $student->update(['student_admission_id' => $admission->id]);

            return redirect()->route('admin.studentlist')->with('success', 'Student created successfully.');
        } catch (\Exception $e) {
            //dd($e->getMessage());
            return back()->with('error', 'Failed to create student.')->withInput();
        }
    }

   public function show($id)
    {
        $student = Student::with([
            'admission.session',
            'admission.class'
        ])->findOrFail($id);

        return view('admin.student_management.view', compact('student'));
    }


    public function edit($id)
    {
        $student = Student::with('admission')->findOrFail($id);
        $sessions = AcademicSession::all();
        $classrooms = ClassList::where('status',1)->orderBy('class','ASC')->get();
        return view('admin.student_management.update_student', compact('student','classrooms','sessions'));
    }

  
   public function update(Request $request, $id)
    {
        $request->validate([
            'student_name'    => 'required|string|max:255',
            'date_of_birth'   => [
                'required',
                'date',
                'before:today',
                function($attribute, $value, $fail) use ($request) {
                    if ($request->admission_date) {
                        $dobTimestamp = strtotime($value);
                        $admissionTimestamp = strtotime($request->admission_date);
                        if ($dobTimestamp > $admissionTimestamp) {
                            $fail('The date of birth cannot be after the admission date.');
                        }
                    }
                },
            ],
            'gender'          => 'required|in:Male,Female,Other',
            'parent_name'     => 'required|string|max:255',
            'email'           => 'nullable|email',
            'phone_number'    => ['nullable', 'regex:/^[0-9]{10}$/'],
            'address'         => 'nullable|string|max:255',
            'admission_date'  => 'required|date',
            'class_id'        => 'required|exists:class_lists,id',
            'section_id'      => 'required|string',
            'roll_number'     => [
                'nullable',
                'integer',
                Rule::unique('student_admissions')
                    ->ignore($request->admission_id)
                    ->where(function ($query) use ($request) {
                        return $query->where('class_id', $request->class_id)
                                    ->where('section', $request->section_id);
                    }),
            ],
            'aadhar_no' => [
                'nullable',
                'regex:/^[0-9]{12}$/',
                Rule::unique('students')->ignore($id)->whereNull('deleted_at'),
            ],
            'blood_group'  => 'nullable|string|max:10',
            'height'       => 'nullable|string',
            'weight'       => 'nullable|string',
            'father_name'  => 'nullable|string|max:255',
            'mother_name'  => 'nullable|string|max:255',
            'divyang'      => 'required|in:Yes,No',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'phone_number.regex' => 'Phone number should be exactly 10 digits.',
            'aadhar_no.regex'    => 'Aadhaar number should be exactly 12 digits.',
        ]);

        try {
            $student = Student::findOrFail($id);
            $imagePath = $student->image;

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                if (!empty($imagePath) && file_exists(public_path($imagePath))) {
                    unlink(public_path($imagePath));
                }

                $file      = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename  = time() . rand(10000, 99999) . '.' . $extension;
                $filePath  = 'uploads/students/' . $filename;
                $file->move(public_path('uploads/students'), $filename);
                $imagePath = $filePath;
            }

            // Update all fields including image
            $student->update([
                'student_name'  => $request->student_name,
                'date_of_birth' => $request->date_of_birth,
                'gender'        => $request->gender,
                'parent_name'   => $request->parent_name,
                'email'         => $request->email,
                'phone_number'  => $request->phone_number,
                'address'       => $request->address,
                'aadhar_no'     => $request->aadhar_no,
                'blood_group'   => $request->blood_group,
                'height'        => $request->height,
                'weight'        => $request->weight,
                'father_name'   => $request->father_name,
                'mother_name'   => $request->mother_name,
                'divyang'       => $request->divyang,
                'image'         => $imagePath, // include updated path
            ]);

            $admission = StudentAdmission::find($request->admission_id);
            if ($admission) {
                $admission->update([
                    'session_id'     => $request->session_id,
                    'class_id'       => $request->class_id,
                    'section'        => $request->section_id,
                    'roll_number'    => $request->roll_number,
                    'admission_date' => $request->admission_date,
                ]);
            } else {
                StudentAdmission::create([
                    'student_id'     => $id,
                    'session_id'     => $request->session_id,
                    'class_id'       => $request->class_id,
                    'section'        => $request->section_id,
                    'roll_number'    => $request->roll_number,
                    'admission_date' => $request->admission_date,
                ]);
            }

            return redirect()->route('admin.studentlist')->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong while updating the student.');
        }
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

        $imagePath = $student->image;
    
        $student->delete(); 

        // Delete image from public directory if it exists
        if (!empty($imagePath) && file_exists(public_path($imagePath))) {
            unlink(public_path($imagePath));
        }
        return response()->json([
            'status'    => 200,
            'message'   => 'Studentlist deleted successfully.',
        ]);
    }

    // public function admissionHistory($id)
    // {
    //     $student = Student::findOrFail($id);
    //     $sessions = AcademicSession::all();
    //     $classes = ClassList::all();
    //     $admissionHistories = StudentAdmission::with(['student','class','session'])
    //                         ->where('student_id', $id)
    //                         ->orderBy('created_at', 'desc')
    //                         ->get();
    //     return view('admin.student_management.admission_history',compact('student','admissionHistories','sessions','classes'));
    // }

    
    // public function admissionhistoryUpdate(Request $request)
    // {
    //     // dd($request->all());
    //     $request->validate([
    //         'id' => 'required|exists:student_admissions,id',
    //         'session_id' => 'required|integer',
    //         'class_id' => 'required|integer',
    //         'section_id' => 'required',
    //         'roll_number' => 'required|numeric',
    //         'admission_date' => 'required|date',
    //     ]);

    //     $history = StudentAdmission::findOrFail($request->id);

    //     $alreadyAdmitted = StudentAdmission::where('student_id', $history->student_id)
    //         ->where('session_id', $request->session_id)
    //         ->where('id', '!=', $history->id)
    //         ->exists();

    //     if ($alreadyAdmitted) {
    //         return redirect()->back()->withErrors(['session_id' => 'Student already admitted in this session.'])
    //             ->withInput();
    //     }
    //     $history->update([
    //         'session_id' => $request->session_id,
    //         'class_id' => $request->class_id,
    //         'section' => $request->section_id,
    //         'roll_number' => $request->roll_number,
    //         'admission_date' => $request->admission_date,
    //     ]);

    //     return redirect()->back()->with('success', 'Admission history updated successfully.');
    // }


    // public function reAdmissionForm($id)
    // {
    //     $student = Student::findOrFail($id);
    //     $classes = ClassList::all();
    //     $sessions = AcademicSession::all();

    //     return view('admin.student_management.re-admission', compact('student','classes','sessions'));
    // }

    // public function reAdmissionStore(Request $request, $id)
    // {
    //     $student = Student::findOrFail($id);
    //     //dd($request->all());
    //     $request->validate([
    //         'session_id' => 'required',
    //         'class_id'  => 'required',
    //         'section_id'  => 'required',
    //         //'roll_number' => 'required',
    //         'roll_number' => [
    //             'required',
    //             'integer',
    //             Rule::unique('student_admissions')->where(function ($query) use ($request) {
    //                 return $query->where('class_id', $request->class_id)
    //                             ->where('section', $request->section_id);
    //             }),
    //         ],
    //         'admission_date' => 'required|date',
    //     ]);

    //     $alreadyAdmitted = StudentAdmission::where('student_id', $student->id)
    //                     ->where('session_id', $request->session_id)
    //                     ->exists();

    //     if ($alreadyAdmitted) {
    //         return redirect()->back()->withErrors(['session_id' => 'Student already admitted in this session.'])
    //                                 ->withInput();
    //     }

    //     StudentAdmission::create([
    //         'student_id' => $student->id,
    //         'session_id' => $request->session_id,
    //         'class_id'  => $request->class_id,
    //         'section' => $request->section_id,
    //         'roll_number' => $request->roll_number,
    //         'admission_date' => $request->admission_date,
    //     ]);
    //     return redirect()->route('admin.student.admissionhistory', $student->id)->with('success', 'Re-admission Done Successfully');
    // }


    public function export(Request $request)
    {
        $keyword = $request->input('keyword');

        $query = Student::query();

        // Search by keyword in multiple fields
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('student_name', 'like', '%' . $keyword . '%')
                    ->orWhere('student_id', 'like', '%'. $keyword . '%')
                    ->orWhere('gender', 'like', '%'. $keyword . '%')
                    ->orWhere('parent_name', 'like', '%'. $keyword . '%')
                    ->orWhere('father_name', 'like', '%'. $keyword . '%')
                    ->orWhere('mother_name', 'like', '%'. $keyword . '%')
                    ->orWhere('aadhar_no', 'like', '%'. $keyword . '%')
                    ->orWhere('blood_group', 'like', '%'. $keyword . '%')
                    ->orWhere('height', 'like', '%'. $keyword . '%')
                    ->orWhere('weight', 'like', '%'. $keyword . '%')
                    ->orWhere('email', 'like', '%'. $keyword . '%')
                    ->orWhere('phone_number', 'like', '%'. $keyword . '%')
                    ->orWhere('address', 'like', '%'. $keyword . '%');             
             });
            // ->orWhereHas('session', function ($sessionQuery) use ($keyword) {
            //     $sessionQuery->where('session_name', 'like', '%'. $keyword . '%');
            // });
        }

        $students = $query->with(['admission.academicsession'])->latest()->get();

        if ($students->count() > 0) {
            $delimiter = ",";
            $filename = "students_export_" . date('Y-m-d') . ".csv";

            $f = fopen('php://memory', 'w');

            // CSV column headers
            $headers = ['Student Name', 'Student ID', 'Gender', 'Father Name', 'Mother Name' ,'Parent Name', 'Aadhaar number',
            'Email', 'Phone Number', 'Address', 'Date of Birth','Academic Session', 'Blood Group', 'Height', 'Weight', ];
            fputcsv($f, $headers, $delimiter);

            foreach ($students as $student) {
                $lineData = [
                    $student->student_name,
                    $student->student_id,
                    $student->gender,
                    $student->father_name,
                    $student->mother_name,
                    $student->parent_name,
                    $student->aadhar_no,
                    $student->email,
                    $student->phone_number,
                    $student->address,
                    //$student->academic_session_id,
                    $student->date_of_birth ? date('d-m-Y',strtotime($student->date_of_birth)) : '',
                    optional($student->admission->academicsession)->session_name, 
                    $student->blood_group,
                    $student->height,
                    $student->weight,
                    // optional($student->created_at)->format('d-m-Y h:i A'),
                ];
                fputcsv($f, $lineData, $delimiter);
            }

            // Rewind and output
            fseek($f, 0);
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            fpassthru($f);
            exit;
        } else {
            return redirect()->back()->with('error', 'No records found to export.');
        }
    }

    public function studentProgressList($student_id, $current_session){

        $student = Student::with('admissions.session')->find($student_id);
        $AcademicSession = AcademicSession::where('session_name', $current_session)->first();
        if (!$student || !$AcademicSession) {
            abort(404, 'Student not found');
        }
        $academic_session_id = $AcademicSession->id;
        // $student_progress_category = StudentProgressCategory::orderBy('field', 'ASC')->get()
        //     ->groupBy('value')
        //     ->map(function ($items) {
        //         return $items->pluck('value')->toArray(); // get only values per field
        //     })
        //     ->toArray();
        $student_progress_category = StudentProgressCategory::orderBy('field', 'ASC')->get()
            ->groupBy('field'); 

            foreach($student_progress_category as $key=>$item){
                StudentProgressMarking::updateOrCreate([
                    'student_id' =>$student_id,
                    'admission_session_id' =>$AcademicSession->id,
                    'progress_category' => ucwords($key)
                ],[

                ]);
            }
        $getDetails = StudentProgressMarking::where('student_id',$student_id)->where('admission_session_id',$AcademicSession->id)->get();

        $savedScores = StudentProgressMarking::where('student_id', $student_id)
            ->where('admission_session_id', $academic_session_id)
            ->get()
            ->groupBy('progress_category')
            ->map(function ($items) {
                return $items->pluck('formative_first_phase', 'progress_value')->toArray();
            })
            ->toArray();


       // dd($savedScores);
        $sessionMap = $student->admissions->mapWithKeys(function ($admission) {
            return [$admission->session->session_name ?? 'Unknown' => $admission->id];
        })->toArray();

        return view('admin.student_management.student_progress_marking', compact('sessionMap','student','current_session','getDetails','academic_session_id','student_progress_category','savedScores'));
    }
    
    // public function ProgressUpdatePhase(Request $request)
    // {
    //     $request->validate([
    //         'student_id' => 'required|integer',
    //         'session_id' => 'required|integer',
    //         'category' => 'required|string',
    //         'phase' => 'required|string|in:formative_first_phase,formative_second_phase,formative_third_phase',
    //         'value' => 'required|string'
    //     ]);

    //     $updated = StudentProgressMarking::where([
    //         'student_id' => $request->student_id,
    //         'admission_session_id' => $request->session_id,
    //         'progress_category' => $request->category,
    //     ])->update([
    //         $request->phase => $request->value
    //     ]);

    //     return response()->json([
    //         'success' => $updated ? true : false
    //     ]);
    // }

    public function ProgressUpdatePhase(Request $request)
    {
        $request->validate([
            'student_id'    => 'required|integer',
            'session_id'    => 'required|integer',
            'category'      => 'nullable|string',
            'value'         => 'nullable|string', 
            'phase'         => 'nullable|string',
            'add_comments'  => 'nullable|string',
        ]);


        $updateData = [
            'formative_first_phase' => $request->value
        ];

        if (empty($updateData['formative_first_phase']) && !$request->filled('add_comments')) {
            return response()->json(['success' => false, 'message' => 'No data to update.']);
        }


        if ($request->filled('phase') && $request->filled('value') && $request->filled('category')) {
            StudentProgressMarking::updateOrCreate(
                [
                    'student_id'              => $request->student_id,
                    'admission_session_id'    => $request->session_id,
                    'progress_category'       => ucwords($request->category),
                    'progress_value'          => ucwords($request->phase),
                ],
                $updateData
            );
        }

        
        if ($request->filled('add_comments')) {

            StudentProgressMarking::where('student_id', $request->student_id)
                ->where('admission_session_id', $request->session_id)
                ->update(['add_comments' => $request->add_comments]);
        }

        return response()->json(['success' => true]);
    }




}
