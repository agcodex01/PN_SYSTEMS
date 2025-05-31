<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternGrade extends Model
{
    use HasFactory;

    protected $table = 'intern_grades';

    protected $casts = [
        'grades' => 'array',
        'final_grade' => 'decimal:1' // Optional: Cast final_grade to decimal with 1 place
    ];

    protected $fillable = [
        'school_id',
        'class_id',
        'intern_id',
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
        return $this->belongsTo(ClassModel::class, 'class_id', 'id');
    }

    public function intern()
    {
        return $this->belongsTo(PNUser::class, 'intern_id', 'user_id');
    }

    // Assuming 'grades' is a JSON column storing individual grades
    public function calculateFinalGradeFromJson()
    {
        // Calculate weighted average and round to nearest integer
        return round(
            ($this->grades['ict_learning_competency'] * 0.4) +
            ($this->grades['twenty_first_century_skills'] * 0.3) +
            ($this->grades['expected_outputs_deliverables'] * 0.3)
        );
    }

    // Accessors to read individual grades from the grades JSON column
    public function getIctLearningCompetencyAttribute()
    {
        return $this->grades['ict_learning_competency'] ?? null;
    }

    public function getTwentyFirstCenturySkillsAttribute()
    {
        return $this->grades['twenty_first_century_skills'] ?? null;
    }

    public function getExpectedOutputsDeliverablesAttribute()
    {
        return $this->grades['expected_outputs_deliverables'] ?? null;
    }
} 