<?php

namespace App\Http\Controllers;

use App\Models\PNUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TempPasswordMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class PNUserController extends Controller
{
    // Display the list of users
    public function index(Request $request)
    {
        $roleFilter = $request->input('role');

        $users = PNUser::when($roleFilter, function ($query, $roleFilter) {
            return $query->where('user_role', $roleFilter);
        })->paginate(5);
    
        // Get all distinct roles
        $roles = PNUser::select('user_role')->distinct()->pluck('user_role');
    
        return view('admin.pnph_users.index', compact('users', 'roles', 'roleFilter'), ['title'=> 'Manage Users']);
    }

    // Show the form for creating a new user
    public function create()
    {
        return view('admin.pnph_users.create', ['title'=> 'Create User']);
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
        return view('admin.pnph_users.edit', compact('user'), ['title'=> 'Edit User']);
    }






    // Update the user in the database
    public function update(Request $request, $user_id)
{
            $request->validate([
                'user_fname' => 'required',
                'user_lname' => 'required',
                'user_email' => 'required|email',
                'user_role' => 'required',
                'status' => 'required|in:active,inactive',
            ]);

            $user = PNUser::findOrFail($user_id);
            $user->update([
                'user_fname' => $request->user_fname,
                'user_lname' => $request->user_lname,
                'user_email' => $request->user_email,
                'user_mInitial' => $request->user_mInitial,
                'user_suffix' => $request->user_suffix,
                'user_role' => $request->user_role,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.pnph_users.index')->with('success', 'User updated successfully.');
}



    public function show($user_id)
{
    // Find the user by user_id
    $user = PNUser::findOrFail($user_id);

    // Return a view to display user details
    return view('admin.pnph_users.show', compact('user'), ['title'=> 'View User']);
}


public function dashboard()
{
    // Get the count of users for each role
    $rolesCount = \App\Models\PNUser::select('user_role', \DB::raw('count(*) as total'))
                                    ->groupBy('user_role')
                                    ->pluck('total', 'user_role')
                                    ->toArray();



    return view('admin.dashboard', compact('rolesCount'), ['title'=> 'Dashboard']);
}



}
