<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentProgressCategory;

class StudentProgressMarkingController extends Controller
{
   public function studentProgress(Request $request)
    {
      // $student = Student::findOrFail($id);
       $progressList = StudentProgressCategory::all();

        $updateData = null;
        $updateDataValues = [];

        if ($request->has('update_id')) {
            $updateData = StudentProgressCategory::find($request->update_id);

            if ($updateData) {
                $updateDataValues = StudentProgressCategory::where('field', $updateData->field)->get();
            }
        }

        return view('admin.student_management.progress_list', compact('progressList', 'updateData', 'updateDataValues'));
    }

    public function studentProgressStore(Request $request)
    {
        $request->validate([
            'field' => 'required|string|max:255',
            'value' => 'required|array|min:1',
            'value.*' => 'required|string|max:255',
        ]);
        foreach ($request->value as $val) {
            StudentProgressCategory::create([
                'field' => $request->field,
                'value' => $val,
                'status' => 1,
            ]);
        }

        return redirect()->route('admin.student.progresslist')->with('success', 'Progress category added successfully.');
    }

    public function studentProgressUpdate(Request $request, $id){
        $request->validate([
            'field' => 'required|string|max:255',
            'value' => 'nullable|array',
            'value.*' => 'nullable|string|max:255',
            'existing_value' => 'nullable|array',
            'existing_value.*' => 'nullable|string|max:255',
            ],
        [
            'value.*.required' => 'Section Is Required'
        ]);

        $existingItem = StudentProgressCategory::findOrFail($id);


        if ($request->has('existing_value')) {
            foreach ($request->existing_value as $valueId => $val) {
                $item = StudentProgressCategory::find($valueId);
                if ($item) {
                    $item->update([
                        'field' => $request->field,
                        'value' => $val
                    ]);
                }
            }
        }

        if ($request->deleted_value_ids) {
            $ids = explode(',', $request->deleted_value_ids);
            StudentProgressCategory::whereIn('id', $ids)->delete();
        }

        if ($request->has('value')) {
            foreach ($request->value as $val) {
                if ($val !== null && $val !== '') {
                    StudentProgressCategory::create([
                        'field' => $request->field,
                        'value' => $val,
                        'status' => 1,
                    ]);
                }
            }
        }

        return redirect()->route('admin.student.progresslist')->with('success', 'Progress category updated.');
    }

    public function studentProgressStatusToggle($id)
    {
        $fieldItem = StudentProgressCategory::findOrFail($id);
        $newStatus = !$fieldItem->status;
        StudentProgressCategory::where('field', $fieldItem->field)->update(['status' => $newStatus]);

        return response()->json(['status' => 200, 'message' => 'Status updated successfully']);
    }

    public function studentProgressDelete(Request $request)
    {
        $item = StudentProgressCategory::findOrFail($request->id);
        StudentProgressCategory::where('field', $item->field)->delete();

        return response()->json(['status' => 200, 'message' => 'Field and its values deleted']);
    }

}
