<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::where('student_id', Auth::guard('student')->id())
                                 ->with('lecturer')
                                 ->latest()
                                 ->get();
        
        $lecturers = Lecturer::all();
        
        return view('student.appointment.index', compact('appointments', 'lecturers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'lecturer_id' => 'required|exists:lecturers,id',
            'location' => 'required|string|max:255'
        ]);

        $appointment = Appointment::create([
            'student_id' => Auth::guard('student')->id(),
            'lecturer_id' => $request->lecturer_id,
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'time' => $request->time,
            'location' => $request->location,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Appointment request submitted successfully.');
    }

    public function update(Request $request, Appointment $appointment)
    {
        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Cannot update appointment that has been ' . $appointment->status);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'lecturer_id' => 'required|exists:lecturers,id',
            'location' => 'required|string|max:255'
        ]);

        $appointment->update($request->all());

        return back()->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Cannot cancel appointment that has been ' . $appointment->status);
        }

        $appointment->delete();
        return back()->with('success', 'Appointment cancelled successfully.');
    }
} 