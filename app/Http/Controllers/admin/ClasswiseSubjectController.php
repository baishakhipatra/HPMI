<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassList;
use App\Models\Subject;
use App\Models\ClassWiseSubject;

class ClasswiseSubjectController extends Controller
{
    public function index(Request $request)
    {
        $class_id = $request->input('update_id');
        $class = ClassList::findOrFail($class_id);
        $subjects = Subject::where('status', 1)->get();

        $assignedSubjects = ClassWiseSubject::where('class_id', $class_id)
                                            ->pluck('subject_id')
                                            ->toArray();

        return view('admin.classWiseSubject.index', compact('class', 'subjects', 'assignedSubjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:class_lists,id',
            'subject_ids' => 'required|array',
        ]);

        // Delete old assignments
        ClassWiseSubject::where('class_id', $request->class_id)->delete();

        foreach ($request->subject_ids as $subject_id) {
            ClassWiseSubject::create([
                'class_id' => $request->class_id,
                'subject_id' => $subject_id,
            ]);
        }

        return redirect()->route('admin.classlist')->with('success', 'Subjects assigned successfully.');
    }
}
