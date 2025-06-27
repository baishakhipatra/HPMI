<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;

class UserListController extends Controller
{
    //
    public function index(Request $request) 
    {
        //dd($request->all());
        // if (Auth::guard('admin')->user()->user_type !== 'Admin') {
        //     abort(403, 'Unauthorized access.');
        // }

        $keyword = $request->input('keyword');
        $query = Admin::where('user_type', 'Employee');

        $query->when($keyword, function ($q) use ($keyword) {
            $q->where(function($subQuery) use ($keyword) {
                $subQuery->where('name', 'like', '%'. $keyword . '%')
                            ->orWhere('email', 'like', '%'. $keyword . '%')
                            ->orWhere('mobile', 'like', '%'. $keyword . '%')
                            ->orWhere('qualifications', 'like', '%' . $keyword . '%')
                            ->orWhere('address', 'like', '%' . $keyword . '%')  
                            ->orWhere('user_id', 'like', '%' . $keyword .  '%');                         
            });
        });
        
        $admins = $query->latest('id')->paginate(10);

        return view('admin.user_management.index', compact('admins'));
    }
    public function create(){
        $user_id = generateEmployeeId();
        return view('admin.user_management.create',compact('user_id'));
    }

    public function store(Request $request) {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            // 'user_id'          => 'nullable|string|unique:admins,user_id',
            'user_id'          => 'required|string|unique:admins,user_id',
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:admins,email',
            //'mobile'           => 'required|digits:10|unique:admins,mobile',
            'mobile' => [
            'required',
            'digits:10',
                Rule::unique('admins')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'date_of_birth'    => 'nullable|date',
            'date_of_joining'  => 'nullable|date',
            'qualifications'   => 'nullable|string|max:255',
            'address'          => 'nullable|string',
            'subjects_taught'  => 'nullable|string|max:255',
            'password'         => 'required|string|min:6',
        ]);

        //custom validation that dob should not exceed doj
        $validator->after(function ($validator) use ($request) {
            if( $request->date_of_birth && $request->date_of_joining) {
                if($request->date_of_birth > $request->date_of_joining) {
                    $validator->errors()->add('date_of_birth', 'Date of Birth cannot be later than Date of Joining.');
                }
            }
        });

        //If validation fails
        if($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Admin::create([
            'user_id'          => $request->user_id,
            'name'             => $request->name,
            'email'            => $request->email,
            'mobile'           => $request->mobile,
            'date_of_birth'    => $request->date_of_birth,
            'date_of_joining'  => $request->date_of_joining,
            'qualifications'   => $request->qualifications,
            'address'          => $request->address,
            // 'subjects_taught'  => $request->subjects_taught,
            'password'         => Hash::make($request->password),
            'user_type'        => 'Employee',
            'status'           => 1,
        ]);
        //dd('Hi');

        return redirect()->route('admin.employee.index')->with('success', 'Employee created successfully');
    }


    public function show($id) {
        $employee = Admin::findOrFail($id);
        return view('admin.user_management.show', compact('employee'));
    }

    public function edit($id) {
        $data = Admin::findOrFail($id);
        return view('admin.user_management.edit', compact('data'));
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address'   => 'nullable|string',
            'mobile'             => [
                'required',
                'digits:10',
                Rule::unique('admins')->ignore($request->id)->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'email'     => 'required|email|unique:admins,email,' . $request->id,
        ]);

        // Custom DOB check
        $validator->after(function ($validator) use ($request) {
            if ($request->date_of_birth && $request->date_of_joining) {
                if ($request->date_of_birth >= $request->date_of_joining) {
                    $validator->errors()->add('date_of_birth', 'Date of Birth must be earlier than Date of Joining.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $admin = Admin::findOrFail($request->id);
        $admin->update([
            'name'             => $request->name,
            //'user_name'        => $request->user_name,
            'user_type'        => $request->user_type ?? $admin->user_type,
            'mobile'           => $request->mobile,
            'email'            => $request->email,
            'date_of_birth'    => $request->date_of_birth,
            'date_of_joining'  => $request->date_of_joining,
            'qualifications'   => $request->qualifications,
            'address'          => $request->address,
        ]);
        return redirect()->route('admin.employee.index')->with('success', 'Employee updated successfully!');
    }

    public function status($id)
    {
        $user = Admin::findOrFail($id);

        // if($user->user_type == 'admin') {
        //     return response()->json(['status' => 403, 'message' => 'Cannot change status of employee']);
        // }

        $user->status = $user->status ? 0 : 1;
        $user->save();
        return response()->json([
            'status'  => 200,
            'message' => 'Status updated successfully'
        ]);
    }

    public function delete(Request $request){
        $user = Admin::find($request->id); 
    
        if (!$user) {
            return response()->json([
                'status'    => 404,
                'message'   => 'user not found.',
            ]);
        }
    
        $user->delete(); 
        return response()->json([
            'status'    => 200,
            'message'   => 'user deleted successfully.',
        ]);
    }

    public function export(Request $request)
    {
        $keyword = $request->input('keyword');

        $query = Admin::where('user_type', 'Employee');

        // Filter by keyword
        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('user_id', 'like', '%' . $keyword . '%')
                ->orWhere('name', 'like', '%' . $keyword . '%')
                ->orWhere('email', 'like', '%' . $keyword . '%')
                ->orWhere('mobile', 'like', '%' . $keyword . '%')
                ->orWhere('qualifications', 'like', '%' . $keyword . '%')
                ->orWhere('address', 'like', '%' . $keyword . '%');
            });
        }

        $employees = $query->get();

        if ($employees->count() > 0) {
            $delimiter = ",";
            $filename = "employees_export_" . date('Y-m-d') . ".csv";
            $f = fopen('php://memory', 'w');

            // CSV Header
            $headers = [
                'Employee ID',
                'Name',
                'Email',
                'Phone',
                'Date of Birth',
                'Date of Joining',
                'address',
                'Qualification',
            ];
            fputcsv($f, $headers, $delimiter);

            foreach ($employees as $employee) {
               
                $lineData = [
                    $employee->user_id,
                    $employee->name,
                    $employee->email,
                    $employee->mobile,
                    $employee->date_of_birth,
                    $employee->date_of_joining,
                    $employee->address,
                    $employee->qualifications,
                ];

                fputcsv($f, $lineData, $delimiter);
            }

            fseek($f, 0);
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '";');
            fpassthru($f);
            exit;
        } else {
            return redirect()->back()->with('error', 'No records found to export.');
        }
    }
}