<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LecturerAppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::where('lecturer_id', auth()->id())
                                 ->with('student')
                                 ->orderBy('date')
                                 ->orderBy('time')
                                 ->get();
        
        $students = Student::whereHas('topics', function($query) {
            $query->where('lecturer_id', auth()->id())
                  ->where('status', 'approved');
        })->get();

        return view('lecturer.appointment.index', compact('appointments', 'students'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,completed',
            'feedback' => 'nullable|string',
        ]);

        $appointment->update([
            'status' => $request->status,
            'feedback' => $request->feedback,
            'meeting_link' => $request->meeting_link
        ]);

        return back()->with('success', 'Appointment ' . $request->status . ' successfully.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'location' => 'required|string|max:255',
            'meeting_link' => 'nullable|url'
        ]);

        try {
            DB::beginTransaction();

            // Debug information
            \Log::info('Creating appointment with data:', [
                'lecturer_id' => auth()->id(),
                'date' => $request->date,
                'time' => $request->time,
                'location' => $request->location,
                'meeting_link' => $request->meeting_link,
                'status' => 'available'
            ]);

            $appointment = Appointment::create([
                'lecturer_id' => auth()->id(),
                'date' => $request->date,
                'time' => $request->time,
                'location' => $request->location,
                'meeting_link' => $request->meeting_link,
                'status' => 'available'
            ]);

            DB::commit();

            // Debug successful creation
            \Log::info('Appointment created successfully:', ['appointment_id' => $appointment->id]);

            return back()->with('success', 'Appointment slot created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Debug error
            \Log::error('Failed to create appointment:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Failed to create appointment: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function getAvailableAppointments()
    {
        $availableAppointments = Appointment::with('lecturer')
            ->where('status', 'available')
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->groupBy('lecturer.name');

        return view('student.appointment.index', compact('availableAppointments'));
    }

    public function bookAppointment(Appointment $appointment)
    {
        // Check if appointment is still available
        if ($appointment->status !== 'available') {
            return back()->with('error', 'This appointment slot is no longer available.');
        }

        try {
            DB::beginTransaction();

            $appointment->update([
                'student_id' => auth()->id(),
                'status' => 'pending',
                'title' => 'Supervision Meeting',
                'description' => 'Student requested supervision meeting'
            ]);

            DB::commit();
            return back()->with('success', 'Appointment slot booked successfully. Waiting for lecturer approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to book appointment slot.');
        }
    }

    public function destroy(Appointment $appointment)
    {
        try {
            // Check if appointment belongs to the lecturer
            if ($appointment->lecturer_id !== Auth::guard('lecturer')->id()) {
                return back()->with('error', 'Unauthorized action.');
            }

            // Only allow deletion of available slots or pending appointments
            if (!in_array($appointment->status, ['available', 'pending'])) {
                return back()->with('error', 'Cannot delete appointments that are ' . $appointment->status);
            }

            $appointment->delete();
            return back()->with('success', 'Appointment deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete appointment:', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to delete appointment. Please try again.');
        }
    }
} 