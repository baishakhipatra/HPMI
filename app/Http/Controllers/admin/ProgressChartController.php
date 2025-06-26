<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{AcademicSession,ClassList,Subject,Student,StudentsMark,StudentProgressMarking,StudentProgressCategory};

class ProgressChartController extends Controller
{
    public function index(){

        $sessions = AcademicSession::all();
        $classes = ClassList::all();
        $subjects = Subject::all();
        $students = Student::all();

        return view('admin.progress_chart.index', compact('sessions','classes','subjects','students'));
    }

    // public function fetchChartData(Request $request)
    // {
    //     $query = StudentsMark::with('subjectlist');

    //     if ($request->session_id) {
    //         $query->whereHas('studentAdmission', fn($q) => $q->where('session_id', $request->session_id));
    //     }

    //     if ($request->class_id) {
    //         $query->where('class_id', $request->class_id);
    //     }

    //     if ($request->subject_id) {
    //         $query->where('subject_id', $request->subject_id);
    //     }

    //     if ($request->student_id) {
    //         $query->where('student_id', $request->student_id);
    //     }

    //     if ($request->time_period == 'last_6_months') {
    //         $query->where('created_at', '>=', now()->subMonths(6));
    //     }

    //     $marks = $query->get();


    //     $trend = [
    //         'Term 1' => round($marks->avg('term_one_stu_marks'), 2),
    //         'Term 2' => round($marks->avg('term_two_stu_marks'), 2),
    //         'Midterm' => round($marks->avg('mid_term_stu_marks'), 2),
    //         'Final Exam' => round($marks->avg('final_exam_stu_marks'), 2),
    //     ];

    
    //     $subjectPerformance = $marks->groupBy('subjectlist.sub_name')->map(function ($items) {
    //         return round($items->avg(function ($item) {
    //             return (
    //                 ($item->term_one_stu_marks ?? 0) +
    //                 ($item->term_two_stu_marks ?? 0) +
    //                 ($item->mid_term_stu_marks ?? 0) +
    //                 ($item->final_exam_stu_marks ?? 0)
    //             ) / 4;
    //         }), 2);
    //     });

    //     $stats = [
    //         'students_tracked' => $marks->unique('student_id')->count(),
    //         'subjects_monitored' => $marks->unique('subject_id')->count(),
    //         'avg_performance' => round($marks->avg(function ($item) {
    //             return (
    //                 ($item->term_one_stu_marks ?? 0) +
    //                  ($item->term_two_stu_marks ?? 0) +
    //                 ($item->mid_term_stu_marks ?? 0) +
    //                 ($item->final_exam_stu_marks ?? 0)
    //             ) / 4;
    //         }), 2),
    //         'avg_progress' => 0, 
    //     ];

    //     return response()->json([
    //         'trend' => $trend,
    //         'subjectPerformance' => $subjectPerformance,
    //         'stats' => $stats,
    //     ]);
    // }
    public function fetchChartData(Request $request)
    {
        if ($request->chart_type === 'qualitative') {
            $query = StudentProgressMarking::query();

            if ($request->session_id) {
                $query->where('admission_session_id', $request->session_id);
            }

            if ($request->student_id) {
                $query->where('student_id', $request->student_id);
            }

            $markings = $query->get();

            $trend = $markings->groupBy('progress_category')->map(function ($items, $category) {
                return round($items->avg('formative_first_phase'), 2);
            });

            $assessment = $trend; 

            $stats = [
                'students_tracked' => $markings->unique('student_id')->count(),
                'subjects_monitored' => StudentProgressCategory::distinct('field')->count('field'),
                'avg_performance' => 0,
                'avg_progress' => round($markings->avg('formative_first_phase'), 2),
            ];

            return response()->json([
                'qualitativeTrend' => $trend,
                'assessment' => $assessment,
                'stats' => $stats,
            ]);
        }

        $query = StudentsMark::with('subjectlist');

        if ($request->session_id) {
            $query->whereHas('studentAdmission', fn($q) => $q->where('session_id', $request->session_id));
        }

        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->time_period == 'last_6_months') {
            $query->where('created_at', '>=', now()->subMonths(6));
        }

        $marks = $query->get();


        $trend = [
            'Term 1' => round($marks->avg('term_one_stu_marks'), 2),
            'Term 2' => round($marks->avg('term_two_stu_marks'), 2),
            'Midterm' => round($marks->avg('mid_term_stu_marks'), 2),
            'Final Exam' => round($marks->avg('final_exam_stu_marks'), 2),
        ];

        
        $subjectPerformance = $marks->groupBy('subjectlist.sub_name')->map(function ($items) {
            return round($items->avg(function ($item) {
                return (
                    ($item->term_one_stu_marks ?? 0) +
                    ($item->term_two_stu_marks ?? 0) +
                    ($item->mid_term_stu_marks ?? 0) +
                    ($item->final_exam_stu_marks ?? 0)
                ) / 4;
            }), 2);
        });

        $stats = [
            'students_tracked' => $marks->unique('student_id')->count(),
            'subjects_monitored' => $marks->unique('subject_id')->count(),
            'avg_performance' => round($marks->avg(function ($item) {
                return (
                    ($item->term_one_stu_marks ?? 0) +
                    ($item->term_two_stu_marks ?? 0) +
                    ($item->mid_term_stu_marks ?? 0) +
                    ($item->final_exam_stu_marks ?? 0)
                ) / 4;
            }), 2),
            'avg_progress' => 0,
        ];

        return response()->json([
            'trend' => $trend,
            'subjectPerformance' => $subjectPerformance,
            'stats' => $stats,
        ]);
    }
}
