<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'lecturer_id',
        'student_id',
        'title',
        'description',
        'date',
        'time',
        'location',
        'meeting_link',
        'status',
        'feedback'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime'
    ];

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
} 