<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\{Student, AcademicSession, ClassList, SectionList, StudentAdmission, };

class StudentListController extends Controller
{
    // public function index(Request $request) 
    // {
    //     //dd($request->all());
    //     // if (Auth::guard('admin')->user()->user_type !== 'Admin') {
    //     //     abort(403, 'Unauthorized access.');
    //     // }

    //     $keyword = $request->input('keyword');

    //     $query = Student::with('session');

    //     $query->when($keyword, function ($q) use ($keyword) {
    //         $q->where(function($subQuery) use ($keyword) {
    //             $subQuery->where('student_name', 'like', '%'. $keyword . '%')
    //                     ->orWhere('student_id', 'like', '%'. $keyword . '%')
    //                     ->orWhere('gender', 'like', '%'. $keyword . '%')
    //                     ->orWhere('parent_name', 'like', '%'. $keyword . '%')
    //                     ->orWhere('email', 'like', '%'. $keyword . '%')
    //                     ->orWhere('phone_number', 'like', '%'. $keyword . '%')
    //                     ->orWhere('address', 'like', '%'. $keyword . '%');
    //         })
    //         ->orWhereHas('session', function ($sessionQuery) use ($keyword) {
    //             $sessionQuery->where('session_name', 'like', '%'. $keyword . '%');
    //         });
    //     });

    //     $students = $query->latest('id')->paginate(10);

    //     return view('admin.student_management.studentlist', compact('students'));
    // }
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
            'student_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'phone_number' =>  ['required', 'regex:/^[0-9]{10}$/'],
            'parent_name' => 'required|string|max:255',
            'email'=> 'required|string',
            'address' => 'required|string',
            'session_id'   => 'required|exists:academic_sessions,id',
            'class_id' => 'required|exists:class_lists,id',
            'section_id' => 'required',
            //'roll_number' => 'required|integer',
            'roll_number'   => [
                'required',
                'integer',
                Rule::unique('student_admissions')->where( function ($query) use ($request) {
                    return $query->where('class_id', $request->class_id)
                                ->where('section', $request->section_id);
                }),
            ],
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
                //$student->session_id = $request->session_id;
                $student->address = $request->address;
                $student->save();
            }
            $alreadyAdmitted = StudentAdmission::where('student_id', $student->id)
                ->where('session_id', $request->session_id)
                ->exists();

            if ($alreadyAdmitted) {
                return back()->with('error', 'This student is already admitted in the selected session.');
            }

            $admission = StudentAdmission::create([
                'student_id' => $student->id,
                'session_id' => $request->session_id,
                'class_id' => $request->class_id,
                'section' => $request->section_id,
                'roll_number' => $request->roll_number,
                'admission_date' => $request->admission_date,
            ]);

            //Update student with admission ID
            $student->student_admission_id = $admission->id;
            $student->save();

            return redirect()->route('admin.studentlist')->with('success', 'Student admission successful!');

        } catch (\Exception $e) {
            //Log::error('Student Admission Error: '.$e->getMessage());
            //dd($e->getMessage());
            return back()->with('error', 'Something went wrong while processing admission.');
        }
    }


    public function edit($id)
    {
        $student = Student::with('admission')->findOrFail($id);
        $sessions = AcademicSession::all();
        $classrooms = ClassList::where('status',1)->orderBy('class','ASC')->get();
        return view('admin.student_management.update_student', compact('student','classrooms','sessions'));
    }

    // public function update(Request $request,$id)
    // {
    //     $validated = $request->validate([
    //         'student_name' => 'required|string|max:255',
    //         'date_of_birth' => 'required|date',
    //         'gender' => 'required',
    //         'parent_name' => 'required|string|max:255',
    //         'email' => 'nullable|email',
    //         'phone_number' => 'nullable|string|max:20',
    //         'address' => 'nullable|string|max:255', 
    //         'admission_date' => 'required|date',
    //         // 'class' => 'required|string|max:50',
    //         // 'section' => 'required|string|max:50',
    //         // 'roll_number' => 'required|integer',
    //         'class_id' => 'required|exists:class_lists,id',
    //         'section_id' => 'required|string',
    //         'roll_number' => 'required|integer',
    //         'session_id' => 'required|exists:academic_sessions,id'
    //     ]);

    //     $student = Student::findOrFail($id);
    //     $student->update([
    //         'student_name' => $request->student_name,
    //         'date_of_birth' => $request->date_of_birth,
    //         'gender' => $request->gender,
    //         'parent_name' => $request->parent_name,
    //         'email' => $request->email,
    //         'phone_number' => $request->phone_number,
    //         'address' => $request->address,
    //         'academic_session_id' => $request->session_id
    //     ]);
    //     //$student->update($validated);

    //     $admission = StudentAdmission::where('student_id', $id)->first();

    //     if ($admission) {
    //         $admission->update([
    //             'session_id' => $request->session_id,
    //             'class_id' => $request->class_id,
    //             'section' => $request->section_id,
    //             'roll_number' => $request->roll_number,
    //             'admission_date' => $request->admission_date,
    //         ]);
    //     }

    //     return redirect()->route('admin.studentlist')->with('success', 'Student updated successfully');
    // }
    public function update(Request $request, $id)
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required',
            'parent_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'admission_date' => 'required|date',
            'class_id' => 'required|exists:class_lists,id',
            'section_id' => 'required|string',
            //'roll_number' => 'required|integer',
            'roll_number'  => [
                'required',
                'integer',
                Rule::unique('student_admissions')->where( function ($query) use ($request,$id) {
                    return $query->where('student_id','!=', $id)->where('class_id', $request->class_id)
                                ->where('section', $request->section_id);
                }),
            ],
        ]);

        try {
            $student = Student::findOrFail($id);
            $student->update([
                'student_name' => $request->student_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'parent_name' => $request->parent_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'academic_session_id' => $request->session_id,
            ]);
            $admission = StudentAdmission::where('student_id', $id)->first();

            if ($admission) {
                $admission->update([
                    'session_id' => $request->session_id,
                    'class_id' => $request->class_id,
                    'section' => $request->section_id,
                    'roll_number' => $request->roll_number,
                    'admission_date' => $request->admission_date,
                ]);
            } else {
                StudentAdmission::create([
                    'student_id' => $id,
                    'session_id' => $request->session_id,
                    'class_id' => $request->class_id,
                    'section' => $request->section_id,
                    'roll_number' => $request->roll_number,
                    'admission_date' => $request->admission_date,
                ]);
            }

            return redirect()->route('admin.studentlist')->with('success', 'Student updated successfully.');

        } catch (\Exception $e) {
           // \Log::error('Student Update Error: ' . $e->getMessage());
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

    // public function admissionhistoryUpdate(Request $request)
    // {
    //     dd($request->all());
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
    //                     ->where('session_id', $request->session_id)
    //                     ->where('id', '!=', $history->id)
    //                     ->exists();

    //     if ($alreadyAdmitted) {
    //         return redirect()->back()->withErrors(['session_id' => 'Student already admitted in this session.'])
    //                                 ->withInput();
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
    public function admissionhistoryUpdate(Request $request)
    {
        // dd($request->all());
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
            return redirect()->back()->withErrors(['session_id' => 'Student already admitted in this session.'])
                ->withInput();
        }
        $history->update([
            'session_id' => $request->session_id,
            'class_id' => $request->class_id,
            'section' => $request->section_id,
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
            //'roll_number' => 'required',
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
            return redirect()->back()->withErrors(['session_id' => 'Student already admitted in this session.'])
                                    ->withInput();
        }

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

   

    public function export(Request $request)
    {
        $keyword = $request->input('keyword');

        $query = Student::query();

        // Search by keyword in multiple fields
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('student_name', 'like', '%' . $keyword . '%')
                ->orWhere('student_id', 'like', '%' . $keyword . '%')
                ->orWhere('gender', 'like', '%' . $keyword . '%')
                ->orWhere('parent_name', 'like', '%' . $keyword . '%')
                ->orWhere('email', 'like', '%' . $keyword . '%')
                ->orWhere('phone_number', 'like', '%' . $keyword . '%')
                ->orWhere('address', 'like', '%' . $keyword . '%');               
             });
            // ->orWhereHas('session', function ($sessionQuery) use ($keyword) {
            //     $sessionQuery->where('session_name', 'like', '%'. $keyword . '%');
            // });
        }

        $students = $query->latest()->get();

        if ($students->count() > 0) {
            $delimiter = ",";
            $filename = "students_export_" . date('Y-m-d') . ".csv";

            $f = fopen('php://memory', 'w');

            // CSV column headers
            $headers = ['Student Name', 'Student ID', 'Gender', 'Parent Name', 'Email', 'Phone Number', 'Address', 'Date of Birth', 'Academic Session', 'Created Date'];
            fputcsv($f, $headers, $delimiter);

            foreach ($students as $student) {
                $lineData = [
                    $student->student_name,
                    $student->student_id,
                    $student->gender,
                    $student->parent_name,
                    $student->email,
                    $student->phone_number,
                    $student->address,
                    //$student->academic_session_id,
                    optional($student->date_of_birth)->format('d-m-Y'),
                    optional($student->session)->session_name, 
                    optional($student->created_at)->format('d-m-Y h:i A'),
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


}
