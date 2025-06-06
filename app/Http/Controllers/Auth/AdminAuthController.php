<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    //

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'user_name' => 'required|string|max:255',
            'user_type' => 'required|in:admin,teacher,employee',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
        ]);

        Admin::create([
            'name'     => $request->name,
            'user_name' => $request->user_name,
            'user_type' => ucwords($request->user_type),
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('auth-login-basic')->withErrors('Registration successful but login failed');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email-username' => 'required',
            'password' => 'required',
        ], [
            'email-username.required' => 'Please enter your email or username.',
            'password.required' => 'Please enter your password.',
        ]);
        $login_input = $request->input('email-username');
        $password = $request->input('password');

        // First try login assuming input is email
        if (Auth::guard('admin')->attempt(['email' => $login_input, 'password' => $password])) {
            return redirect()->route('admin.dashboard');
        }

        // If email login failed, try with username (name)
        if (Auth::guard('admin')->attempt(['user_name' => $login_input, 'password' => $password])) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email-user_name' => 'Invalid credentials']);
    }

    public function profile()
    {
        $admin = auth()->guard('admin')->user();
        return view('admin.profile-update.profile',compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'user_name' => 'required|string|max:255|unique:admins,user_name,' . $admin->id,
            'email' => 'required|email|unique:admins,email,' . $admin->id,
        ]);

        $admin->update($request->only(['name','user_name','email']));

        return redirect()->back()->with('success','profile updated successfully');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        // Invalidate the session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth-login-basic');
    }

    //for reset password
    public function resetPassword(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
            'password'  => 'required|confirmed|min:6',
        ]);

        Admin::where('email', $request->email)
            ->update(['password'  => Hash::make($request->password)]);

        return redirect()->route('auth-login-basic')->with('success', 'Password has been reset successfully!');
    }
}
