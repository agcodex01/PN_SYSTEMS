<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subject extends Model
{
    protected $fillable = [
        'school_id',
        'offer_code',
        'name',
        'instructor',
        'schedule'
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
 