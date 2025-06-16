<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'for_role'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    // Helper method to get status badge color
    public function getStatusColorClass()
    {
        return $this->status === 'in-progress' 
            ? 'bg-blue-100 text-blue-800' 
            : 'bg-green-100 text-green-800';
    }

    // Helper method to get role badge color
    public function getRoleColorClass()
    {
        switch ($this->for_role) {
            case 'Student':
                return 'bg-blue-100 text-blue-800';
            case 'Lecturer':
                return 'bg-pink-100 text-pink-800';
            default:
                return 'bg-purple-100 text-purple-800';
        }
    }
} 