<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Admin, DesignationPermission, Designation, Permission};

class DesignationController extends Controller
{
    //

    public function index(Request $request) {

        $keyword = $request->input('keyword');

        $designations =  Designation::when($keyword, function($query, $keyword){
                            $query->where('name', 'like', '%' . $keyword .'%');
                            })->orderBy('id','desc')->paginate(10);
        return view('admin.designations.index', compact('designations', 'keyword'));
    }

    public function status($id)
    {
        $user = Designation::findOrFail($id);

        $user->status = $user->status ? 0 : 1;
        $user->save();
        return response()->json([
            'status'  => 200,
            'message' => 'Status updated successfully'
        ]);
    }

    public function permissions(Request $request, $id) {
        $designation = Designation::findOrFail($id);
        $permissions = Permission::orderBy('parent_name')->get();
        $assignedPermissions = $designation->permissions->pluck('id')->toArray();

        return view('admin.designations.permissions', compact('designation', 'permissions', 'assignedPermissions'));
    }

    public function updatePermissions(Request $request) {
        $request->validate([
            'designation_id'    => 'required|exists:designations,id',
            'permissions'       => 'array',
            'permissions.*'     => 'exists:permissions,id',
        ]);

        $designation = Designation::findOrFail($request->designation_id);
        $permissionIds = $request->input('permissions', []);

        $designation->permissions()->sync($permissionIds);

        return redirect()->route('admin.designation.list')->with('success', 'Permissions updated successfully.');
    }

    public function updatePermissionAjax(Request $request) {
        //dd($request->all());
        $request->validate([
            'designation_id' => 'required|exists:designations,id',
            'permission_id' => 'required|exists:permissions,id',
            'checked' => 'required|boolean',
        ]);

        $designation = Designation::findOrFail($request->designation_id);
        $permissionId = $request->permission_id;

        if($request->checked) {
            // Add permission if not already attached
            $designation->permissions()->syncWithoutDetaching([$permissionId]);
        } else{
             // Remove permission
             $designation->permissions()->detach([$permissionId]);
        }

        return response()->json(['success' => true]);

    }

}
