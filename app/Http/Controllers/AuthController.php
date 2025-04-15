<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\PNUser;



class AuthController extends Controller
{
    // Show the login form
    public function showLoginForm()
    {
        return view('login');
    }



    // Handle login
    public function login(Request $request)
    {
        // Validate the login input
        $request->validate([
            'user_id' => 'required',
            'password' => 'required',
        ]);
    
        // Find the user by user_id
        $user = PNUser::where('user_id', $request->user_id)->first();
    
        // Check if the user exists and the password matches
        if (!$user || !Hash::check($request->password, $user->user_password)) {
            return back()->withErrors(['error' => 'Invalid User ID or Password']);
        }

        Auth::login($user);
        session(['user_id' => $user->user_id]);
        // Check if the password is temporary
        if ($user->is_temp_password) {
            return redirect()->route('change-password');
        }
    
        // Log the user in and redirect to the dashboard
        return redirect()->route('admin.pnph_users.admin-dashboard');
    }




    // Handle logout
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    // Show the change password form
    public function showChangePasswordForm()
    {
        return view('change-password');
    }



    // Handle password update
    public function updatePassword(Request $request)
    {
        // Validate the input
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        $user = Auth::user();

        // Check if the current password matches
        if (!Hash::check($request->current_password, $user->user_password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect']);
        }

        // Update the password
        $user->update([
            'user_password' => Hash::make($request->new_password),
            'is_temp_password' => false, // Mark the password as no longer temporary
        ]);

        // Redirect to the dashboard with a success message
        return redirect()->route('admin.pnph_users')->with('success', 'Password updated successfully.');
    }
}