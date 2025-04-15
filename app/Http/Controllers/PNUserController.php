<?php

namespace App\Http\Controllers;

use App\Models\PNUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TempPasswordMail;
use Illuminate\Support\Str;

class PNUserController extends Controller
{
    // Display the list of users
    public function index()
    {
        $users = PNUser::all();
        return view('admin.pnph_users.index', compact('users'));
    }

    // Show the form for creating a new user
    public function create()
    {
        return view('admin.pnph_users.create');
    }

    // Store a newly created user in the database
    public function store(Request $request)
    {
        // Validate the user input
        $request->validate([
            'user_id' => 'required|unique:pnph_users,user_id',
            'user_lname' => 'required',
            'user_fname' => 'required',
            'user_mInitial' => 'nullable',
            'user_suffix' => 'nullable',
            'user_email' => 'required|email|unique:pnph_users,user_email',
            'user_role' => 'required',
        ]);
    
        // Generate a temporary password
        $password = Str::random(8); // Generate an 8-character random password
    
        // Create the user in the database
        $user = PNUser::create([
            'user_id' => $request->user_id,
            'user_lname' => $request->user_lname,
            'user_fname' => $request->user_fname,
            'user_mInitial' => $request->user_mInitial,
            'user_suffix' => $request->user_suffix,
            'user_email' => $request->user_email,
            'user_role' => $request->user_role,
            'user_password' => Hash::make($password), // Hash the password before saving
            'status' => 'active', // Default status
        ]);
    
        // Optionally, send an email with the temporary password
        Mail::to($user->user_email)->send(new TempPasswordMail($user, $password));
    
        // Redirect to the user list page with a success message
        return redirect()->route('admin.pnph_users.index')->with('success', 'User created successfully.');
    }

    

    // Show the form for editing an existing user
    public function edit($user_id)
    {
        $user = PNUser::find($user_id);
        return view('admin.pnph_users.edit', compact('user'));
    }

    // Update the user in the database
    public function update(Request $request, $user_id)
    {
        // Validate the user input
        $request->validate([
            'user_fname' => 'required',
            'user_lname' => 'required',
            'user_mInitial' => 'nullable',
            'user_suffix' => 'nullable',
            'user_email' => 'required|email|unique:pnph_users,user_email,' . $user_id . ',user_id',
            'user_role' => 'required',
            'status' => 'required|in:active,deactivated',
        ]);
    
        // Find the user by user_id
        $user = PNUser::findOrFail($user_id);
    
        // Update the user
        $user->update($request->all());
    
        // Redirect back with a success message
        return redirect()->route('admin.pnph_users.index')->with('success', 'User updated successfully.');
    }

    // Delete a user
    public function destroy($user_id)
    {
        $user = PNUser::find($user_id);
        $user->delete();

        return redirect()->route('admin.pnph_users.index')->with('success', 'User deleted successfully.');
    }
}
