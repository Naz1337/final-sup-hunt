<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Topic;
use App\Models\Appointment;
use App\Models\Task;

class DashboardController extends Controller
{
    public function index()
    {
        $lecturer = Auth::guard('lecturer')->user();
        
        // Get tasks for lecturers
        $tasks = Task::where(function($query) {
                    $query->where('for_role', 'All')
                          ->orWhere('for_role', 'Lecturer');
                })
                ->where(function($query) {
                    $query->where('start_date', '<=', now())
                          ->where('end_date', '>=', now());
                })
                ->orderBy('start_date')
                ->get();

        // Get pending topics count
        $pendingTopicsCount = Topic::where('lecturer_id', $lecturer->id)
                                 ->where('status', 'pending')
                                 ->count();

        // Get pending appointments count
        $pendingAppointmentsCount = Appointment::where('lecturer_id', $lecturer->id)
                                             ->where('status', 'pending')
                                             ->count();

        // Get total supervised students
        $totalStudents = Topic::where('lecturer_id', $lecturer->id)
                             ->where('status', 'approved')
                             ->count();

        return view('lecturer.dashboard', compact(
            'tasks',
            'pendingTopicsCount',
            'pendingAppointmentsCount',
            'totalStudents'
        ));
    }
} 