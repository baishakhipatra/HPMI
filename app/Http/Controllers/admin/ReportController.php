<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Student, ClassList, Subject, StudentsMark, StudentAdmission, AcademicSession};

class ReportController extends Controller
{
    public function index()
    {
        $studentsCount = Student::count();
        $classesCount = ClassList::count();
        $subjectsCount = Subject::count();
        $marksCount = StudentsMark::count();

    
        $sessionIds = StudentAdmission::pluck('session_id')->unique();
        $sessions = AcademicSession::whereIn('id', $sessionIds)->pluck('session_name', 'id');

        return view('admin.reports.index', compact(
            'studentsCount', 'classesCount', 'subjectsCount', 'marksCount',
            'sessions'
        ));
    }

    public function getChartData(Request $request)
    {
       
        $query = StudentsMark::query()->with([
            'studentAdmission.academicSession', 
            'class',
            'subjectlist' 
        ]);

        if ($request->filled('session_id')) {
            $query->whereHas('studentAdmission', function ($admissionQuery) use ($request) {
                $admissionQuery->where('session_id', $request->session_id);
            });
        }

    
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

    
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $marks = $query->get();

        if ($marks->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        
        $grouped = $marks->groupBy(function ($mark) {
      
            return $mark->class->class ?? 'Unknown Class';
        });

        $chartData = [];

        foreach ($grouped as $className => $items) {
         
            $totalMarks = $items->sum('final_exam_stu_marks');
            $count = $items->count();

            $passCount = $items->where('final_exam_stu_marks', '>=', 33)->count();

            $chartData[] = [
                'class' => $className,
                'avg_marks' => $count > 0 ? round($totalMarks / $count, 2) : 0,
                'pass_percentage' => $count > 0 ? round(($passCount / $count) * 100, 2) : 0,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $chartData
        ]);
    }

    public function getClassesBySession(Request $request)
    {
        if (!$request->filled('session_id')) {
            return response()->json(['classes' => []]);
        }

        $classIds = StudentAdmission::where('session_id', $request->session_id)
                                    ->pluck('class_id')
                                    ->unique();

        $classes = ClassList::whereIn('id', $classIds)
                            ->select('id', 'class as name')
                            ->get();

        return response()->json(['classes' => $classes]);
    }

    public function getSubjectsByClassAndSession(Request $request)
    {
       
        if (!$request->filled('class_id') || !$request->filled('session_id')) {
            return response()->json(['subjects' => []]);
        }

        $studentIdsInSession = StudentAdmission::where('session_id', $request->session_id)->pluck('student_id');

        $subjectIds = StudentsMark::where('class_id', $request->class_id)
            ->whereIn('student_id', $studentIdsInSession)
            ->pluck('subject_id')
            ->unique();

        $subjects = Subject::whereIn('id', $subjectIds)
            ->select('id', 'sub_name as name')
            ->get();

        return response()->json(['subjects' => $subjects]);
    }

    public function getStudentsByClassAndSession(Request $request)
    {
       
        if (!$request->filled('session_id') || !$request->filled('class_id')) {
            return response()->json(['students' => []]);
        }

        
        $studentIds = StudentAdmission::where('session_id', $request->session_id)
                                    ->where('class_id', $request->class_id)
                                    ->pluck('student_id')
                                    ->unique();

      
        $students = Student::whereIn('id', $studentIds)
                            ->select('id', 'student_name as name') 
                            ->get();

        return response()->json(['students' => $students]);
    }


    public function getStudentReportCard(Request $request)
    {
  
        if (!$request->filled('session_id') || !$request->filled('class_id') || !$request->filled('student_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Please select a Session, Class, and Student.'
            ]);
        }

        $sessionId = $request->session_id;
        $classId = $request->class_id;
        $studentId = $request->student_id;
        $subjectId = $request->subject_id; 

        $student = Student::find($studentId);
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.'
            ]);
        }

     
        $admission = StudentAdmission::with(['academicSession', 'class'])
                                    ->where('student_id', $studentId)
                                    ->where('session_id', $sessionId)
                                    ->where('class_id', $classId)
                                    ->first();

        if (!$admission) {
            return response()->json([
                'success' => false,
                'message' => 'Student admission record not found for the selected session and class.'
            ]);
        }


        $marksQuery = StudentsMark::with('subjectlist') 
                                ->where('student_id', $studentId)
                                ->where('class_id', $classId);

        if ($subjectId) {
            $marksQuery->where('subject_id', $subjectId);
        }

        $marks = $marksQuery->get();

        $reportCardData = [
            'student_name' => $student->student_name,
            'session_name' => $admission->academicSession->session_name ?? 'N/A',
            'class_name' => $admission->class->class ?? 'N/A',
            'roll_number' => $admission->roll_number, 
            'marks' => [], 
            'summary' => [
                'total_marks_obtained' => 0,
                'total_out_of_marks' => 0,
                'average_percentage' => 0,
                'pass_status' => 'Fail' 
            ]
        ];

        $totalMidTermMarks = 0;
        $totalFinalExamMarks = 0;
        $totalMidTermOutOff = 0;
        $totalFinalExamOutOff = 0;
        $passedSubjectsCount = 0;
        $totalSubjectsCount = $marks->count();

        foreach ($marks as $mark) {
            $subjectName = $mark->subjectlist->sub_name ?? 'Unknown Subject';

            
            $totalMidTermMarks += $mark->mid_term_stu_marks;
            $totalFinalExamMarks += $mark->final_exam_stu_marks;
            $totalMidTermOutOff += $mark->mid_term_out_off;
            $totalFinalExamOutOff += $mark->final_exam_out_off;

            $subjectPass = $mark->final_exam_stu_marks >= 33; 
            if ($subjectPass) {
                $passedSubjectsCount++;
            }

            $reportCardData['marks'][] = [
                'subject' => $subjectName,
                'mid_term_marks' => $mark->mid_term_stu_marks,
                'mid_term_out_off' => $mark->mid_term_out_off,
                'final_exam_marks' => $mark->final_exam_stu_marks,
                'final_exam_out_off' => $mark->final_exam_out_off,
                'status' => $subjectPass ? 'Pass' : 'Fail',
            ];
        }

       
        $overallTotalObtained = $totalFinalExamMarks;
        $overallTotalOutOff = $totalFinalExamOutOff;

        if ($overallTotalOutOff > 0) {
            $reportCardData['summary']['average_percentage'] = round(($overallTotalObtained / $overallTotalOutOff) * 100, 2);
        }

   
        if ($reportCardData['summary']['average_percentage'] >= 33) {
            $reportCardData['summary']['pass_status'] = 'Pass';
        }
       

        return response()->json([
            'success' => true,
            'data' => $reportCardData
        ]);
    }

    public function export(Request $request)
    {
        $sessionId = $request->input('session_id');
        $classId = $request->input('class_id');
        $subjectId = $request->input('subject_id');

        $query = StudentsMark::with(['student', 'class', 'subjectlist']);

        if ($sessionId) {
            $studentIds = StudentAdmission::where('session_id', $sessionId)->pluck('student_id');
            $query->whereIn('student_id', $studentIds);
        }

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        $marks = $query->get();

        if ($marks->count() > 0) {
            $delimiter = ",";
            $filename = "student_report_card_" . date('Y-m-d') . ".csv";
            $f = fopen('php://memory', 'w');

          
            $headers = [
                'Student Name',
                'Class',
                'Subject',
                'Mid Term Marks',
                'Mid Term Out Of',
                'Final Exam Marks',
                'Final Exam Out Of',
                'Status'
            ];
            fputcsv($f, $headers, $delimiter);

            foreach ($marks as $mark) {
                $studentName = $mark->student->student_name ?? 'N/A';
                $className = $mark->class->class ?? 'N/A';
                $subjectName = $mark->subjectlist->sub_name ?? 'N/A';

                $midMarks = $mark->mid_term_stu_marks ?? 0;
                $midOutOf = $mark->mid_term_out_off ?? 0;
                $finalMarks = $mark->final_exam_stu_marks ?? 0;
                $finalOutOf = $mark->final_exam_out_off ?? 0;

                $status = ($finalMarks >= 33) ? 'Pass' : 'Fail';

                $line = [
                    $studentName,
                    $className,
                    $subjectName,
                    $midMarks,
                    $midOutOf,
                    $finalMarks,
                    $finalOutOf,
                    $status
                ];

                fputcsv($f, $line, $delimiter);
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


}
