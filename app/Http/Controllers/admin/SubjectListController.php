<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectListController extends Controller
{
    //
    public function index(Request $request) {
        // Get search keyword from request
        $keyword = $request->input('keyword');

        // Fetch subjects with optional search
        $subjects = Subject::when($keyword, function ($query, $keyword) {
            $query->where('sub_name', 'like', '%'. $keyword . '%')
                ->orWhere('sub_code', 'like', '%'. $keyword . '%')
                ->orWhere('description', 'like', '%'. $keyword . '%');
        })
        ->orderBy('id', 'desc')
        ->paginate(10);

        $subject = new Subject();

        // Return to view with subjects
        return view('admin.subject_list.index', compact('subjects', 'subject'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_name' => 'required|string|max:255',
            'sub_code' => 'required|string|max:50|unique:subjects,sub_code',
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
        $user = Subject::find($request->id); 
    
        if (!$user) {
            return response()->json([
                'status'    => 404,
                'message'   => 'user not found.',
            ]);
        }
    
        $user->delete(); 
        return response()->json([
            'status'    => 200,
            'message'   => 'Subject deleted successfully.',
        ]);
    }
}
