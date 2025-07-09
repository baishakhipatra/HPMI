<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use App\Imports\StudentsImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;


use App\Models\{Student, AcademicSession, ClassList, SectionList, StudentAdmission, progressList,StudentProgressCategory,StudentProgressMarking};

class StudentListController extends Controller
{
   
    public function index(Request $request) 
    {
        $admin = auth()->guard('admin')->user();
        $keyword = $request->input('keyword');

        // Start a base query
        $query = Student::with('admission.session')
            ->when($admin && $admin->user_type === 'Teacher', function ($q) use ($admin) {
                $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->toArray();
                $q->whereHas('admission', function($q) use ($assignedClassIds) {
                    $q->whereIn('class_id', $assignedClassIds);
                });
            })
            ->when($keyword, function ($q) use ($keyword) {
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
            });


        $students = $query->latest('id')->paginate(10);

        return view('admin.student_management.studentlist', compact('students'));
    }


    public function create(){
        createNewSession();
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
            'phone_number'    => ['required', 'regex:/^[0-9]{10}$/'],
            'address'         => 'required|string|max:255',
            'admission_date'  => 'required|date',
            'class_id'        => 'required|exists:class_lists,id',
            'section_id'      => 'required|string',
            'roll_number'     => 'required|integer',
            'session_id'      => 'required|exists:academic_sessions,id',

            // Optional fields
            'aadhar_no'       => [
                'required',
                'regex:/^[0-9]{12}$/',
                Rule::unique('students')->whereNull('deleted_at'),
            ],
            'blood_group'     => 'nullable|string|max:10',
            'height'          => 'nullable|string',
            'weight'          => 'nullable|string',
            'father_name'     => 'required|string|max:255',
            'mother_name'     => 'required|string|max:255',
            'divyang'         => 'required|in:Yes,No',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ],[
            'phone_number.regex' => 'Phone number should be exactly 10 digits.',
            'aadhar_no.regex'    => 'Aadhaar number should be exactly 12 digits.',
        ]);

        try {
            // Prepare values for student_id generation
            $admissionYear  = date('Y', strtotime($request->admission_date));
            $class          = ClassList::find($request->class_id);
            $classAlias     = $class->class;
            $rollNo         = $request->roll_number;

            $generatedId = Student::generateStudentUid($admissionYear, $classAlias, $rollNo);

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
            'phone_number'    => ['required', 'regex:/^[0-9]{10}$/'],
            'address'         => 'required|string|max:255',
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
            'father_name'  => 'required|string|max:255',
            'mother_name'  => 'required|string|max:255',
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

    

    public function export(Request $request)
    {
        $keyword = $request->input('keyword');

        $admin = auth()->guard('admin')->user();

        $query = Student::with(['admission.academicsession'])
            ->when($admin && $admin->user_type === 'Teacher', function ($q) use ($admin) {
                $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->toArray();
                $q->whereHas('admission', function ($q) use ($assignedClassIds) {
                    $q->whereIn('class_id', $assignedClassIds);
                });
            })
            ->when($keyword, function ($q) use ($keyword) {
                $q->where(function($subQuery) use ($keyword) {
                    $subQuery->where('student_name', 'like', '%' . $keyword . '%')
                        ->orWhere('student_id', 'like', '%' . $keyword . '%')
                        ->orWhere('gender', 'like', '%' . $keyword . '%')
                        ->orWhere('parent_name', 'like', '%' . $keyword . '%')
                        ->orWhere('father_name', 'like', '%' . $keyword . '%')
                        ->orWhere('mother_name', 'like', '%' . $keyword . '%')
                        ->orWhere('aadhar_no', 'like', '%' . $keyword . '%')
                        ->orWhere('blood_group', 'like', '%' . $keyword . '%')
                        ->orWhere('height', 'like', '%' . $keyword . '%')
                        ->orWhere('weight', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%')
                        ->orWhere('phone_number', 'like', '%' . $keyword . '%')
                        ->orWhere('address', 'like', '%' . $keyword . '%');
                });
            });

        $students = $query->latest()->get();


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

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $file = $request->file('excel_file');
            $data = Excel::toArray([], $file);
            $rows = $data[0];

            $skippedRows = [];
            $successCount = 0;
            foreach (array_slice($rows, 1) as $index => $row) {
               
                $rowNumber = $index + 2;
                $row = array_map('trim', $row);

                if (count($row) < 18) {
                    $skippedRows[] = "Row $rowNumber: Incomplete data (less than 18 columns)";
                    continue;
                }

                // Required field checks
                $requiredIndexes = [0, 2, 5, 6, 7, 8, 17]; // name, phone, admission date, section, aadhaar, dob, session
                $missingFields = [];

                foreach ($requiredIndexes as $i) {
                    if (!isset($row[$i]) || $row[$i] === '') {
                        $missingFields[] = "Column index $i";
                    }
                }

                if (!empty($missingFields)) {
                    $skippedRows[] = "Row $rowNumber: Missing fields - " . implode(', ', $missingFields);
                    continue;
                }
    
                // Parse dates
                try {
                    $admission_date = Carbon::instance(Date::excelToDateTimeObject($row[5]))->format('Y-m-d');
                    $date_of_birth  = Carbon::instance(Date::excelToDateTimeObject($row[17]))->format('Y-m-d');
                } catch (\Exception $e) {
                    $skippedRows[] = "Row $rowNumber: Invalid date format - " . $e->getMessage();
                    continue;
                }
              
                // Uniqueness checks
           
                if (Student::where('phone_number', $row[2])->exists()) {
                    $skippedRows[] = "Row $rowNumber: Duplicate phone number";
                    continue;
                }
          
                if (!empty($row[1]) && Student::where('email', $row[1])->exists()) {
                    $skippedRows[] = "Row $rowNumber: Duplicate email";
                    continue;
                }
                            
                if (Student::where('aadhar_no', $row[7])->exists()) {
                    $skippedRows[] = "Row $rowNumber: Duplicate Aadhaar";
                    continue;
                }
               
                $session = AcademicSession::where('session_name', $row[17])->first();
                if(!$session) {
                    $session = createNewSession($row[17]);
                    if(!$session) {
                        $skippedRows[] = "Row $rowNumber: Failed to create academic session '$row[17]'";
                        continue;
                    }
                }

                $class = ClassList::where('class', $row[3])->first();
                if (!$class) {
                    $skippedRows[] = "Row $rowNumber: Class '{$row[3]}' not found";
                    continue;
                }

                $admissionYear = date('Y', strtotime($admission_date));
                $classAlias = $class->class; // may need to convert to Roman
                $rollNo = $row[4];
                $studentUniqueId = Student::generateStudentUid($admissionYear, $classAlias, $rollNo);

                // Save student
                $student = Student::create([
                    'student_id'     => $studentUniqueId,
                    'student_name'   => $row[0],
                    'email'          => $row[1] ?? null,
                    'phone_number'   => $row[2],
                    'class'          => $class->class,
                    'roll_number'    => $rollNo,
                    'admission_date' => $admission_date,
                    'section'        => $row[6],
                    'aadhar_no'      => $row[7],
                    'gender'         => $row[8],
                    'parent_name'    => $row[9] ?? null,
                    'address'        => $row[10] ?? null,
                    'father_name'    => $row[11] ?? null,
                    'mother_name'    => $row[12] ?? null,
                    'divyang'        => $row[13] ?? 'No',
                    'blood_group'    => $row[14] ?? null,
                    'height'         => $row[15] ?? null,
                    'weight'         => $row[16] ?? null,
                    'date_of_birth'  => $date_of_birth,
                    // Add more fields here if needed
                ]);
                //save studentadmission
                $admission = StudentAdmission::create([
                    'student_id'     => $student->id,
                    'session_id'     => $session->id,
                    'class_id'       => $class->id,
                    'section'        => $row[6],
                    'roll_number'    => $rollNo,
                    'admission_date' => $admission_date,
                ]);

                $student->update(['student_admission_id' => $admission->id]);
                $successCount++;
            }

            return response()->json([
                'message' => "$successCount student(s) imported successfully.",
                'errors' => $skippedRows,
            ]);

        } catch (\Exception $e) {
            \Log::error('Import error: ' . $e->getMessage());
            dd($e->getMessage());
            return response()->json([
                'errors' => ['error' => ['Unexpected error during import. Please check the file.']]
            ], 500);
        }
    }
}
