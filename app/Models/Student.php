<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'matric_id',
        'name',
        'email',
        'program',
        'phone',
        'password',
        'is_first_login'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the topic associated with the student.
     */
    public function topic()
    {
        return $this->hasOne(Topic::class);
    }

    /**
     * Get all topics submitted by the student.
     */
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function activeTopic()
    {
        return $this->topics()
                   ->whereIn('status', ['pending', 'approved'])
                   ->latest()
                   ->first();
    }
} 