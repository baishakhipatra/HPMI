<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Student, AcademicSession, StudentProgressMarking, StudentAdmission, ClassList, StudentProgressCategory};

class StudentProgressAddController extends Controller
{

    public function selectionPage()
    {
        // only sessions that appear in student_admissions
        $sessionIds = StudentAdmission::pluck('session_id')->unique()->toArray();
        $sessions = AcademicSession::whereIn('id', $sessionIds)
                    ->orderBy('session_name','desc')
                    ->get();

        return view('admin.student_management.select_student_session', compact('sessions'));
    }

    public function getClassesBySession(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        // pull class_ids out of admissions for that session
        $query = StudentAdmission::where('session_id', $request->session_id);

        // If teacher, restrict to their assigned classes
        if ($admin && $admin->user_type === 'Teacher') {
            $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->toArray();
            $query->whereIn('class_id', $assignedClassIds);
        }

        $classIds = $query->pluck('class_id')->unique()->toArray();

        $classes = ClassList::whereIn('id', $classIds)->get();

        return response()->json([
            'success' => true,
            'classes' => $classes
        ]);
    }

    public function getStudentsByClass(Request $request)
    {

        $admin = auth()->guard('admin')->user();

        // Check if teacher and restrict class access
        if ($admin && $admin->user_type === 'Teacher') {
            $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->toArray();

            if (!in_array($request->class_id, $assignedClassIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not assigned to this class.'
                ], 403);
            }
        }
        // filter admissions by both session & class
        $studentIds = StudentAdmission::where('session_id', $request->session_id)
                    ->where('class_id', $request->class_id)
                    ->pluck('student_id')
                    ->unique()
                    ->toArray();

        $students = Student::whereIn('id', $studentIds)->get();

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    public function goToMarking(Request $request)
    {
        $request->validate([
            'session_id' => 'required|integer',
            'class_id'   => 'required|integer',
            'student_id' => 'required|integer',
        ]);

        $session = AcademicSession::findOrFail($request->session_id);

        return redirect()->route(
            'admin.student.progressmarkinglist',
            [$request->student_id, $session->session_name]
        );
    }

    // public function studentProgressList($student_id, $current_session)
    // {
    //     $student = Student::with('admissions.session')->find($student_id);
    //     $AcademicSession = AcademicSession::where('session_name', $current_session)->first();
    //     if (!$student || !$AcademicSession) {
    //         abort(404, 'Student not found');
    //     }
    //     $academic_session_id = $AcademicSession->id;
    //     // $student_progress_category = StudentProgressCategory::orderBy('field', 'ASC')->get()
    //     //     ->groupBy('value')
    //     //     ->map(function ($items) {
    //     //         return $items->pluck('value')->toArray(); // get only values per field
    //     //     })
    //     //     ->toArray();
    //     $student_progress_category = StudentProgressCategory::orderBy('field', 'ASC')->get()
    //         ->groupBy('field'); 

    //         foreach($student_progress_category as $key=>$item){
    //             StudentProgressMarking::updateOrCreate([
    //                 'student_id' =>$student_id,
    //                 'admission_session_id' =>$AcademicSession->id,
    //                 'progress_category' => ucwords($key)
    //             ],[

    //             ]);
    //         }
    //     $getDetails = StudentProgressMarking::where('student_id',$student_id)->where('admission_session_id',$AcademicSession->id)->get();

    //     $savedScores = StudentProgressMarking::where('student_id', $student_id)
    //         ->where('admission_session_id', $academic_session_id)
    //         ->get()
    //         ->groupBy('progress_category')
    //         ->map(function ($items) {
    //             return $items->pluck('formative_first_phase', 'progress_value')->toArray();
    //         })
    //         ->toArray();


    //    // dd($savedScores);
    //     $sessionMap = $student->admissions->mapWithKeys(function ($admission) {
    //         return [$admission->session->session_name ?? 'Unknown' => $admission->id];
    //     })->toArray();

    //     return view('admin.student_management.student_progress_marking', compact('sessionMap','student','current_session','getDetails','academic_session_id','student_progress_category','savedScores'));
    // }
    public function studentProgressList($student_id, $current_session)
    {
        $admin = auth()->guard('admin')->user();
        $isTeacher = ($admin && $admin->user_type === 'Teacher');

        $student = Student::with(['admissions.session', 'admissions.class'])->find($student_id);
        $AcademicSession = AcademicSession::where('session_name', $current_session)->first();

        if (!$student || !$AcademicSession) {
            abort(404, 'Student or Session not found');
        }

        $academic_session_id = $AcademicSession->id;

        // Get the student’s current admission record for the session
        $currentAdmission = $student->admissions->where('session_id', $academic_session_id)->first();

        if (!$currentAdmission) {
            abort(404, 'Admission record not found for selected session.');
        }

        // If teacher, check if the student belongs to teacher's assigned class
        if ($isTeacher) {
            $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->toArray();

            if (!in_array($currentAdmission->class_id, $assignedClassIds)) {
                return redirect()->back()->with('error', 'This student is not assigned under your classes.');
            }
        }

        // Load progress categories
        $student_progress_category = StudentProgressCategory::orderBy('field', 'ASC')->get()->groupBy('field');

        // Ensure all progress fields are created for this student/session
        foreach ($student_progress_category as $key => $item) {
            StudentProgressMarking::updateOrCreate(
                [
                    'student_id' => $student_id,
                    'admission_session_id' => $academic_session_id,
                    'progress_category' => ucwords($key)
                ],
                []
            );
        }

        // Fetch details and scores
        $getDetails = StudentProgressMarking::where('student_id', $student_id)
            ->where('admission_session_id', $academic_session_id)
            ->get();

        $savedScores = $getDetails->groupBy('progress_category')
            ->map(function ($items) {
                return $items->pluck('formative_first_phase', 'progress_value')->toArray();
            })
            ->toArray();

        // Session dropdown mapping
        $sessionMap = $student->admissions->mapWithKeys(function ($admission) {
            return [$admission->session->session_name ?? 'Unknown' => $admission->id];
        })->toArray();

        return view('admin.student_management.student_progress_marking', compact(
            'sessionMap',
            'student',
            'current_session',
            'getDetails',
            'academic_session_id',
            'student_progress_category',
            'savedScores'
        ));
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
}
