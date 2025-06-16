<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lecturer;
use App\Models\Student;

class CoordinatorController extends Controller
{
    public function dashboard(Request $request)
    {
        // Get search and sort parameters
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'research_group'); // default sort by research group

        // Query for lecturers with search and sort
        $lecturers = Lecturer::query()
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy($sortBy)
            ->get();

        // Query for students with search and sort
        $students = Student::query()
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy($sortBy)
            ->get();

        return view('coordinator.dashboard', compact('lecturers', 'students', 'search', 'sortBy'));
    }
} 