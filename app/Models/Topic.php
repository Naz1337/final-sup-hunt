<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'research_area',
        'lecturer_id',
        'student_id',
        'status',
        'feedback',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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