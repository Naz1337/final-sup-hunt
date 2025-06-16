<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use Illuminate\Http\Request;

class LecturerProfileController extends Controller
{
    public function index()
    {
        $lecturers = Lecturer::orderBy('name')->get();
        return view('student.lecturer.index', compact('lecturers'));
    }

    public function show($lecturerId)
    {
        $lecturer = Lecturer::findOrFail($lecturerId);
        return view('student.lecturer.profile', compact('lecturer'));
    }
} 