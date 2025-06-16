<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Lecturer extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'staff_id',
        'name',
        'email',
        'password',
        'research_group',
        'is_first_login',
        'photo',
        'expertise',
        'teaching_experience',
        'previous_fyp_titles'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_first_login' => 'boolean',
    ];

    public function quota()
    {
        return $this->hasOne(Quota::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
} 