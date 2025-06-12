<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{ClassList, SectionList, ClassWiseSubject, Subject};
use DB;

class ClassListController extends Controller
{

    public function index(Request $request)
    {
        if (isset($request->update_id)) {
            $update_data = ClassList::find($request->update_id);
            if (!$update_data) {
                return abort(404);
            }
        }else{
            $update_data = null;
        }
        // dd($request->update_id);
        $classlist = ClassList::with('sections')->get();
        return view('admin.class_lists.class-list', compact('classlist','update_data'));
    }

    public function create(){
        return view('admin.classcreate');
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'class' => 'required|string|max:255|unique:class_lists,class',
            'section.*' => 'required|string|max:255',
        ]);


        $sections = array_map('strtolower', array_map('trim', $request->section));
        if (count($sections) !== count(array_unique($sections))) {
            return redirect()->back()->withErrors(['section' => 'Duplicate section names found.'])->withInput();
        }

        $class = ClassList::create([
            'class' => $request->class,
            'status' => 1,
        ]);

        if (!empty($request->section)) {
            foreach ($request->section as $newSectionName) {
                if (!empty($newSectionName)) {
                    SectionList::create([
                        'section' => $newSectionName,
                        'class_list_id' => $class->id,
                    ]);
                }
            }
        }

        return redirect()->route('admin.classlist')->with('success', 'Class and sections added successfully.');
    }

    public function edit($id)
    {
        $classData = ClassList::with('sections')->findOrFail($id);

        $classlist = ClassList::select('class', \DB::raw('MAX(status) as status'))
            ->groupBy('class')
            ->get()
            ->map(function ($item) {
                $firstRecord = ClassList::where('class', $item->class)->first();
                $item->sections = SectionList::where('class_list_id', $firstRecord->id)->get();
                $item->id = $firstRecord ? $firstRecord->id : null;
                return $item;
            });

        return view('admin.class_lists.class-list', compact('classlist', 'classData'));
    }


    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'class' => 'required|string|max:255',
    //         'existing_sections.*' => 'nullable|string|max:255',
    //         'section.*' => 'nullable|string|max:255',
    //     ]);

    //      $sections = [];

    //     if ($request->existing_sections) {
    //         foreach ($request->existing_sections as $existingSection) {
    //             if (!empty($existingSection)) {
    //                 $sections[] = strtolower(trim($existingSection));
    //             }
    //         }
    //     }

    //     if ($request->section) {
    //         foreach ($request->section as $newSection) {
    //             if (!empty($newSection)) {
    //                 $sections[] = strtolower(trim($newSection));
    //             }
    //         }
    //     }

    //     // check for duplicates
    //     if (count($sections) !== count(array_unique($sections))) {
    //         return redirect()->back()->withErrors(['section' => 'Duplicate entry not allowed. Each section name must be unique.'])->withInput();
    //     }

    //     // update class name
    //     $class = ClassList::findOrFail($id);
    //     $class->class = $request->class;
    //     $class->save();

    //     // update existing sections
    //     if ($request->existing_section_ids) {
    //         foreach ($request->existing_section_ids as $key => $sectionId) {
    //             $section = SectionList::find($sectionId);
    //             if ($section) {
    //                 $section->section = $request->existing_sections[$key];
    //                 $section->save();
    //             }
    //         }
    //     }

    //     // add new sections
    //     if ($request->section) {
    //         foreach ($request->section as $sectionName) {
    //             if (!empty($sectionName)) {
    //                 SectionList::create([
    //                     'class_list_id' => $id,
    //                     'section' => $sectionName,
    //                 ]);
    //             }
    //         }
    //     }

    //     // handle deleted sections
    //     if ($request->deleted_section_ids) {
    //         $deletedIds = explode(',', $request->deleted_section_ids);
    //         SectionList::whereIn('id', $deletedIds)->delete();
    //     }

    //     return redirect()->route('admin.classlist')->with('success', 'Class and sections updated successfully.');
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'class' => 'required|string|max:255',
            'existing_sections.*' => 'nullable|string|max:255',
            'section.*' => 'nullable|string|max:255',
        ]);

        $class = ClassList::findOrFail($id);
        $class->class = $request->class;
        $class->save();


        if (!empty($request->deleted_section_ids)) {
            $deletedIds = explode(',', $request->deleted_section_ids);
            SectionList::whereIn('id', $deletedIds)->delete();
        }

        if (!empty($request->existing_section)) {
            foreach ($request->existing_section as $sectionId => $sectionName) {
                $section = SectionList::find($sectionId);
                if ($section) {
                    $section->section = $sectionName;
                    $section->save();
                }
            }
        }

       if (!empty($request->section)) {
            foreach ($request->section as $sectionName) {
                if (!empty($sectionName)) {
                    $section = new SectionList();
                    $section->class_list_id = $class->id;
                    $section->section = $sectionName;
                    $section->save();
                }
            }
        }


        return redirect()->route('admin.classlist')->with('success', 'Class updated successfully');
    }


    public function status($id)
    {
        $user = ClassList::findOrFail($id);

        $user->status = $user->status ? 0 : 1;
        $user->save();
        return response()->json([
            'status'  => 200,
            'message' => 'Status updated successfully'
        ]);
    }

    public function delete(Request $request)
    {
        $classlist = ClassList::find($request->id); 
    
        // if (!$classlist) {
        //     return response()->json([
        //         'status'    => 404,
        //         'message'   => 'user not found.',
        //     ]);
        // }
    
        $classlist->delete(); 
        return response()->json([
            'status'    => 200,
            'message'   => 'class deleted successfully.',
        ]);
    }

    public function subjectsList($id) {
        $classData = ClassList::findOrFail($id);
        $keyword = request()->input('keyword');
        $classSubjects = ClassWiseSubject::with('subject')
                ->where('class_id', $id)
                ->whereHas('subject', function ($query) use ($keyword) {
                    if(!empty($keyword)) {
                        $query->where('sub_name', 'like', '%'. $keyword . '%');
                    }
                })->paginate(10);
        
        $assignedSubjectIds = ClassWiseSubject::where('class_id', $id)->pluck('subject_id');
        // $allSubjects =  Subject::whereNotIn('id', function ($query) use ($id) {
        //             $query->select('class_id')
        //                 ->from('class_wise_subjects')
        //                 ->where('subject_id', $id);
        //             })->get();
        $allSubjects = Subject::whereNotIn('id', $assignedSubjectIds)->get();
        return view('admin.class_lists.subjects-list', compact('classData', 'classSubjects', 'allSubjects'));
    }

    public function addSubjectToclass(Request $request) {
        $checkIfExists = ClassWiseSubject::where([
                'subject_id' => $request->subjectId,
                'class_id' => $request->classId,
        ])->first();

        if ($checkIfExists) {
            return redirect()->route('admin.class.subjects', $request->classId)->with('error', 'Selected subject is already added to the class.');
        }
        ClassWiseSubject::create([
            'subject_id' => $request->subjectId,
            'class_id' => $request->classId,
        ]);
        return redirect()->route('admin.class.subjects', $request->classId)->with('success', 'Selected subject is successfully added to the class.');
    }
    
    public function deleteSubjectToclass(Request $request) {
        $user = ClassWiseSubject::find($request->id); 
    
        if (!$user) {
            return response()->json([
                'status'    => 404,
                'message'   => 'Classwise subject not found.',
            ]);
        }
    
        $user->delete(); 
        return response()->json([
            'status'    => 200,
            'message'   => 'Classwise subject deleted successfully.',
        ]);
    }
    
}
