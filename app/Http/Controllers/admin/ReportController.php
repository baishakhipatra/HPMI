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

            $passCount = $items->where('final_exam_stu_marks', '>=', 50)->count();

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

        $admin = auth()->guard('admin')->user();
        $query = StudentAdmission::where('session_id', $request->session_id);

        if ($admin && $admin->user_type === 'Teacher') {
            $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->toArray();
            $query->whereIn('class_id', $assignedClassIds);
        }

        $classIds = $query->pluck('class_id')->unique();

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

       $admin = auth()->guard('admin')->user();

        // Teacher class access check
        if ($admin && $admin->user_type === 'Teacher') {
            $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->toArray();
            if (!in_array($request->class_id, $assignedClassIds)) {
                return response()->json(['subjects' => []], 403);
            }
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

        
        $admin = auth()->guard('admin')->user();

        if ($admin && $admin->user_type === 'Teacher') {
            $assignedClassIds = $admin->teacherClasses()->pluck('class_id')->toArray();
            if (!in_array($request->class_id, $assignedClassIds)) {
                return response()->json(['students' => []], 403);
            }
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
                'overall_percentage' => 0,
                'overall_status' => 'N/A'
            ]
        ];

        $overallTotalObtainedFromSubjectAverages = 0;
        $overallTotalPossibleFromSubjectAverages = 0;
        $totalSubjectsCount = 0;

        foreach ($marks as $mark) {
            $subjectName = $mark->subjectlist->sub_name ?? 'Unknown Subject';

            $midMarks = $mark->mid_term_stu_marks ?? 0;
            $midOutOf = $mark->mid_term_out_off ?? 0;
            $finalMarks = $mark->final_exam_stu_marks ?? 0;
            $finalOutOf = $mark->final_exam_out_off ?? 0;

            $subjectTotalMarks = $midMarks + $finalMarks;
            $subjectTotalPossible = $midOutOf + $finalOutOf;

            $subjectAverageMarks = 0;
            $subjectAverageOutOf = 0;
            $subjectPercentage = 0;

            if ($subjectTotalPossible > 0) {
                $subjectAverageOutOf = ($midOutOf + $finalOutOf) / 2;
                $subjectAverageMarks = ($midMarks + $finalMarks) / 2;
                $subjectPercentage = round(($subjectTotalMarks / $subjectTotalPossible) * 100, 2);

                $totalSubjectsCount++;

                $overallTotalObtainedFromSubjectAverages += $subjectAverageMarks;
                $overallTotalPossibleFromSubjectAverages += $subjectAverageOutOf;
            }

            // Grade Label Based on Subject Percentage
            $subjectStatus = calStatusLabel($subjectPercentage);

            $reportCardData['marks'][] = [
                'subject' => $subjectName,
                'mid_term_marks' => $midMarks,
                'mid_term_out_off' => $midOutOf,
                'final_exam_marks' => $finalMarks,
                'final_exam_out_off' => $finalOutOf,
                'subject_average_marks' => round($subjectAverageMarks, 2),
                'subject_average_out_of' => round($subjectAverageOutOf, 2),
                'subject_percentage' => $subjectPercentage,
                'status' => $subjectStatus,
            ];
        }

        // Finalize Overall Summary
        $reportCardData['summary']['total_marks_obtained'] = round($overallTotalObtainedFromSubjectAverages, 2);
        $reportCardData['summary']['total_out_of_marks'] = round($overallTotalPossibleFromSubjectAverages, 2);

        if ($reportCardData['summary']['total_out_of_marks'] > 0) {
            $reportCardData['summary']['overall_percentage'] = round(($reportCardData['summary']['total_marks_obtained'] / $reportCardData['summary']['total_out_of_marks']) * 100, 2);
        } else {
            $reportCardData['summary']['overall_percentage'] = 0;
        }

        // Grade Label for Overall Performance
        $reportCardData['summary']['overall_status'] = calStatusLabel($reportCardData['summary']['overall_percentage']);

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
        $studentId = $request->input('student_id');

        $query = StudentsMark::with(['student', 'class', 'subjectlist']);

        if ($sessionId) {
            $studentIdsInSession = StudentAdmission::where('session_id', $sessionId)->pluck('student_id');
            $query->whereIn('student_id', $studentIdsInSession);
        }

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        $marks = $query->get();

        if ($marks->count() > 0) {
            $delimiter = ",";
            $totalMarksObtained = 0;
            $totalPossibleMarks = 0;

            foreach ($marks as $mark) {
                $totalMarksObtained += ($mark->mid_term_stu_marks ?? 0);
                $totalMarksObtained += ($mark->final_exam_stu_marks ?? 0);
                $totalPossibleMarks += ($mark->mid_term_out_off ?? 0);
                $totalPossibleMarks += ($mark->final_exam_out_off ?? 0);
            }

            $overallPercentage = ($totalPossibleMarks > 0) ? ($totalMarksObtained / $totalPossibleMarks) * 100 : 0;
            $overallPercentage = round($overallPercentage, 2);
            $overallStatus = calStatusLabel($overallPercentage);

            $firstMark = $marks->first();
            $studentNameForFile = 'report';
            if ($firstMark && $firstMark->student && $firstMark->student->student_name) {
                $studentNameForFile = str_replace(' ', '_', $firstMark->student->student_name);
            }

            $filename = "student_report_card_" . $studentNameForFile . "_" . date('Y-m-d') . ".csv";
            $f = fopen('php://memory', 'w');
            // Step 1: Get session names for student_ids
            $studentSessions = StudentAdmission::with('session')
                ->whereIn('student_id', $marks->pluck('student_id')->unique())
                ->get()
                ->mapWithKeys(function ($admission) {
                    return [$admission->student_id => $admission->session->session_name ?? 'N/A'];
                });

            // Step 2: Group marks by session name
            $marksGroupedBySession = $marks->groupBy(function ($mark) use ($studentSessions) {
                return $studentSessions[$mark->student_id] ?? 'N/A';
            })->sortKeys(); 

            // Step 3: Write session-wise CSV
            foreach ($marksGroupedBySession as $sessionName => $sessionMarks) {
                // Session title row
                fputcsv($f, ["Session: $sessionName"], $delimiter);

                // Table headers under each session
                $headers = [
                    'Student Name',
                    'Class',
                    'Subject',
                    'Mid Term Marks',
                    'Final Exam Marks',
                    'Grade',
                    'Status'
                ];
                fputcsv($f, $headers, $delimiter);

                foreach ($sessionMarks as $mark) {
                    $studentName = $mark->student->student_name ?? 'N/A';
                    $className = $mark->class->class ?? 'N/A';
                    $subjectName = $mark->subjectlist->sub_name ?? 'N/A';

                    $midMarks = $mark->mid_term_stu_marks ?? 0;
                    $midOutOf = $mark->mid_term_out_off ?? 0;
                    $finalMarks = $mark->final_exam_stu_marks ?? 0;
                    $finalOutOf = $mark->final_exam_out_off ?? 0;

                    $totalSubjectObtained = $midMarks + $finalMarks;
                    $totalSubjectOutOf = $midOutOf + $finalOutOf;
                    $subjectPercentage = ($totalSubjectOutOf > 0) ? ($totalSubjectObtained / $totalSubjectOutOf) * 100 : 0;
                    $subjectPercentage = round($subjectPercentage, 2);

                    $grade = calGradeLabel($subjectPercentage);
                    $status = calStatusLabel($subjectPercentage);

                    $line = [
                        $studentName,
                        $className,
                        $subjectName,
                        $midMarks,
                        $finalMarks,
                        $grade,
                        $status
                    ];

                    fputcsv($f, $line, $delimiter);
                }

                // Add blank row between sessions
                fputcsv($f, [], $delimiter);
            }

            fputcsv($f, ['Total Marks Obtained:', $totalMarksObtained, '', '', '', '', ''], $delimiter);
            fputcsv($f, ['Overall Percentage:', "$overallPercentage%", '', '', '', '', ''], $delimiter);
            // fputcsv($f, ['Overall Status:', $overallStatus, '', '', '', '', ''], $delimiter);

            fseek($f, 0);
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            fpassthru($f);
            exit;
        } else {
            return redirect()->back()->with('error', 'No records found for the selected student/filters to export.');
        }
    }




}
