<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Subject;

class SubjectListController extends Controller
{
    //
    public function index(Request $request) {
        // Get search keyword from request
        $keyword = $request->input('keyword');
        $subject_id = $request->input('edit_subject') ?? null;

        // Fetch subjects with optional search
        $subjects = Subject::when($keyword, function ($query, $keyword) {
            $query->where('sub_name', 'like', '%'. $keyword . '%')
                ->orWhere('sub_code', 'like', '%'. $keyword . '%')
                ->orWhere('description', 'like', '%'. $keyword . '%');
        })
        ->orderBy('id', 'desc')
        ->paginate(10);

        $subject = new Subject();

        $editableSubjectDetails = null;
        if ($subject_id){
            $editableSubjectDetails = Subject::findOrFail($subject_id);
        }

        // Return to view with subjects
        return view('admin.subject_list.index', compact('subjects', 'subject', 'editableSubjectDetails'));
    }

    public function update(Request $request){
        $validated = $request->validate([
            'edit_sub_name' => 'required|string|max:255',
            'edit_sub_code' => 'required|string|max:50',
            'edit_description' => 'nullable|string',
        ]);

        Subject::where('id', $request->edit_subject_id)->update([
            'sub_name' => $request->edit_sub_name,
            'sub_code' => $request->edit_sub_code,
            'description' => $request->edit_description
        ]);

        return redirect()->route('admin.subjectlist.index')->with('success', 'Subject is updated successfully.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_name' => 'required|string|max:255',
            'sub_code' => 'required|string|max:50|unique:subjects,sub_code',
            // 'sub_code' => [
            //     'required',
            //     'string',
            //     'max:50',
            //     Rule::unique('subjects', 'sub_code')->whereNull('deleted_at'),
            // ],
            Rule::unique('itinerary_galleries', 'title')->ignore($request->id),
            'description' => 'nullable|string',
        ]);

        Subject::create($validated);

        return redirect()->route('admin.subjectlist.index')->with('success', 'Subject created successfully.');
    }

    public function status($id)
    {
        $user = Subject::findOrFail($id);

        $user->status = $user->status ? 0 : 1;
        $user->save();
        return response()->json([
            'status'  => 200,
            'message' => 'Status updated successfully'
        ]);
    }

    public function delete(Request $request){
        //dd($request->all());
        $subject = Subject::find($request->id); 
    
        if (!$subject) {
            return response()->json([
                'status'    => 404,
                'message'   => 'subject not found.',
            ]);
        }
    
        $subject->delete();
        // De-allocate subjects from associated tables
        ClassWiseSubject::where('subject_id', $request->id)->delete();

        return response()->json([
            'status'    => 200,
            'message'   => 'Subject deleted successfully.',
        ]);
    }
}
