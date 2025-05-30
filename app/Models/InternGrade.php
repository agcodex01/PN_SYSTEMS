<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternGrade extends Model
{
    use HasFactory;

    protected $table = 'intern_grades';

    protected $fillable = [
        'school_id',
        'class_id',
        'user_id',
        'company_name',
        'grade',
        'status',
        'created_at',
        'updated_at'
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'school_id');
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }

    public function intern()
    {
         return $this->belongsTo(PNUser::class, 'user_id', 'user_id');
    }

} 