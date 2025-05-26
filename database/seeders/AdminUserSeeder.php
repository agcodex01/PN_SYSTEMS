<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        DB::table('pnph_users')->insert([
            'user_id' => '123',
            'user_fname' => 'System',
            'user_lname' => 'Admin',
            'user_mInitial' => '',
            'user_suffix' => '',
            'user_email' => 'admin@example.com',
            'user_role' => 'Admin',
            'user_password' => Hash::make('admin2025'), // Change this after first login!
            'status' => 'active',
            'is_temp_password' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}