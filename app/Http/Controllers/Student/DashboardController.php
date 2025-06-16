<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $tasks = Task::where('for_role', 'All')
                     ->orWhere('for_role', 'Student')
                     ->orderBy('start_date')
                     ->get();
                     
        return view('student.dashboard', compact('tasks'));
    }
} 