<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;

class UserListController extends Controller
{
    //
    public function index(Request $request) 
    {
        if (Auth::guard('admin')->user()->user_type !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        $query = Admin::query()->where('user_type', '!=', 'admin');

        if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%")
                ->orWhere('user_name', 'like', "%{$keyword}%");
            });
        }

        $admins = $query->latest()->paginate(10);

        return view('admin.user_management.userList', compact('admins'));
    }
}