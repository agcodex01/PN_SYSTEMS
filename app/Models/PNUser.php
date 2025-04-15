<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PNUser extends Authenticatable
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'pnph_users';

    // The primary key column
    protected $primaryKey = 'user_id';

    // Disable timestamps if you don't want Eloquent to manage created_at and updated_at
    public $timestamps = true;

    // Specify that the primary key is not auto-incrementing
    public $incrementing = false;

    // Specify the type of the primary key (e.g., string)
    protected $keyType = 'string';

    // Specify which fields are mass assignable
    protected $fillable = [
        'user_id',
        'user_fname',
        'user_lname',
        'user_mInitial',
        'user_suffix',
        'user_email',
        'user_password',
        'user_role',
        'status',
        'is_temp_password'
    ];
}
