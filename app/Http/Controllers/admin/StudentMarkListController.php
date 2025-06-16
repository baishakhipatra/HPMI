<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ClassList, Subject, Student, StudentAdmission};

class StudentMarkListController extends Controller
{
    public function index()
    {

        $sessions = StudentAdmission::with('academicsession')
              ->select('session_id')
              ->distinct()
              ->get();
        $classes = ClassList::with('sections')->get();
        $subjects = Subject::all();

        $students = Student::all();

        $classOptions = $classes->map(function($class){
            $sections = $class->sections->pluck('section')->toArray();
            $sectionList = implode(', ', $sections);
            return [
                'id' => $class->id,
                'name' => $class->class . ' - ' . $sectionList
            ];
        });
        return view('admin.student_marks.index',compact('classes','subjects','classOptions', 'students', 'sessions'));
    }
}
