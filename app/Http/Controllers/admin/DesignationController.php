<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Admin, DesignationPermission, Designation};

class DesignationController extends Controller
{
    //

    public function index() {
       $designations =  Designation::orderBy('id')->get();
       return view('admin.designations.index', compact('designations'));
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

}
