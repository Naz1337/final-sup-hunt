<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index()
    {
        $availableAppointments = Appointment::with('lecturer')
            ->where('status', 'available')
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->groupBy('lecturer.name');

        $myAppointments = Appointment::where('student_id', auth()->id())
            ->with('lecturer')
            ->orderBy('date', 'desc')
            ->get();

        return view('student.appointment.index', compact('availableAppointments', 'myAppointments'));
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

        try {
            Appointment::create([
                'student_id' => Auth::guard('student')->id(),
                'lecturer_id' => $request->lecturer_id,
                'title' => $request->title,
                'description' => $request->description,
                'date' => $request->date,
                'time' => $request->time,
                'location' => $request->location,
                'status' => 'pending'
            ]);

            return back()->with('success', 'Appointment requested successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create appointment: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Appointment $appointment)
    {
        // Check if appointment belongs to the authenticated student
        if ($appointment->student_id !== Auth::guard('student')->id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Check if appointment can be edited
        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Cannot update appointment that has been ' . $appointment->status);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'location' => 'required|string|max:255'
        ]);

        try {
            $appointment->update($request->only([
                'title',
                'description',
                'date',
                'time',
                'location'
            ]));

            return back()->with('success', 'Appointment updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update appointment: ' . $e->getMessage());
        }
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->student_id !== Auth::guard('student')->id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Cannot cancel appointment that has been ' . $appointment->status);
        }

        try {
            $appointment->delete();
            return back()->with('success', 'Appointment cancelled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel appointment: ' . $e->getMessage());
        }
    }

    public function bookAppointment(Request $request, Appointment $appointment)
    {
        // Check if appointment is still available
        if ($appointment->status !== 'available') {
            return back()->with('error', 'This appointment slot is no longer available.');
        }

        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $appointment->update([
                'student_id' => auth()->id(),
                'status' => 'pending',
                'title' => $request->title,
                'description' => $request->description
            ]);

            DB::commit();
            return back()->with('success', 'Appointment slot booked successfully. Waiting for lecturer approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to book appointment slot.');
        }
    }
} 