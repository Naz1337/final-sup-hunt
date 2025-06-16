@extends('layouts.student')

@section('title', 'Appointments')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Appointments</h2>
        <button onclick="document.getElementById('addAppointmentModal').classList.remove('hidden')"
                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>Request Appointment
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- My Appointments Table -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">My Appointments</h3>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supervisor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $appointment->title }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($appointment->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $appointment->lecturer->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $appointment->date->format('d M Y') }}<br>
                            {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $appointment->location }}
                            @if($appointment->meeting_link)
                            <br>
                            <a href="{{ $appointment->meeting_link }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                Join Meeting
                            </a>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($appointment->status === 'approved') bg-green-100 text-green-800
                                @elseif($appointment->status === 'rejected') bg-red-100 text-red-800
                                @elseif($appointment->status === 'completed') bg-gray-100 text-gray-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            @if($appointment->status === 'pending')
                            <button onclick="editAppointment({{ $appointment->id }})" class="text-blue-600 hover:text-blue-900 mr-3">
                                Edit
                            </button>
                            <form action="{{ route('student.appointment.destroy', $appointment) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                    Cancel
                                </button>
                            </form>
                            @endif
                            @if($appointment->feedback)
                            <button onclick="viewFeedback('{{ $appointment->feedback }}')" class="text-gray-600 hover:text-gray-900 ml-3">
                                View Feedback
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No appointments scheduled yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Available Appointments -->
    @if(isset($availableAppointments) && $availableAppointments->isNotEmpty())
    <div>
        <h3 class="text-lg font-semibold mb-4">Available Appointment Slots</h3>
        @forelse($availableAppointments as $lecturerName => $appointments)
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="bg-gray-50 px-6 py-3">
                    <h4 class="text-md font-medium text-gray-900">{{ $lecturerName }}</h4>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach($appointments as $appointment)
                        <div class="p-6 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center space-x-3">
                                        <div class="text-sm text-gray-500">
                                            {{ $appointment->date->format('d M Y') }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="text-sm text-gray-900">
                                            Location: {{ $appointment->location }}
                                        </div>
                                    </div>
                                </div>
                                <button onclick="showBookingModal({{ $appointment->id }})"
                                        class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm">
                                    Book Slot
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
                No available appointment slots at the moment.
            </div>
        @endforelse
    </div>
    @endif
</div>

<!-- Add Appointment Modal -->
<div id="addAppointmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Request New Appointment</h3>
            <button onclick="document.getElementById('addAppointmentModal').classList.add('hidden')"
                    class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('student.appointment.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Title</label>
                    <input type="text" name="title" required
                        class="w-full px-4 py-2 rounded-lg bg-gray-100 border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900 placeholder-gray-400"
                        placeholder="Appointment title">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Description</label>
                    <textarea name="description" rows="3" required
                            class="w-full px-4 py-2 rounded-lg bg-gray-100 border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900 placeholder-gray-400"
                            placeholder="Brief description of your appointment request"></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Supervisor</label>
                    <select name="lecturer_id" required
                            class="w-full px-4 py-2 rounded-lg bg-gray-100 border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900">
                        <option value="">Select a supervisor</option>
                        @foreach($lecturers as $lecturer)
                        <option value="{{ $lecturer->id }}">{{ $lecturer->name }}</option>
                        @endforeach
                    </select>
                </div>
                @php
                    $minDate = \Carbon\Carbon::now()->addDay()->format('Y-m-d');
                @endphp
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Date</label>
                    <input type="date" name="date" id="date" required min="{{ $minDate }}"
                        class="w-full px-4 py-2 rounded-lg bg-gray-100 border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900">
                    <small class="text-red-600 text-sm mt-1 block">Appointments must be made at least 24 hours in advance.</small>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Time</label>
                    <input type="time" name="time" id="time" required
                        class="w-full px-4 py-2 rounded-lg bg-gray-100 border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Location</label>
                    <input type="text" name="location" required
                        class="w-full px-4 py-2 rounded-lg bg-gray-100 border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-gray-900 placeholder-gray-400"
                        placeholder="e.g., Office Room, Google Meet, etc.">
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button"
                        onclick="document.getElementById('addAppointmentModal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Book Appointment</h3>
            <button onclick="closeBookingModal()" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="bookingForm" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                           placeholder="e.g., Project Discussion">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="3" required
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                              placeholder="Brief description of what you'd like to discuss..."></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeBookingModal()"
                            class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Book Appointment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Feedback Modal -->
<div id="feedbackModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Appointment Feedback</h3>
            <button onclick="closeFeedbackModal()" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="feedbackContent" class="text-gray-700 mb-4"></div>
        <div class="flex justify-end">
            <button onclick="closeFeedbackModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showBookingModal(appointmentId) {
    const modal = document.getElementById('bookingModal');
    const form = document.getElementById('bookingForm');
    form.action = `/student/appointments/${appointmentId}/book`;
    modal.classList.remove('hidden');
}

function closeBookingModal() {
    const modal = document.getElementById('bookingModal');
    modal.classList.add('hidden');
}

function editAppointment(id) {
    alert('Edit functionality to be implemented');
}

function viewFeedback(feedback) {
    const modal = document.getElementById('feedbackModal');
    const content = document.getElementById('feedbackContent');
    content.textContent = feedback;
    modal.classList.remove('hidden');
}

function closeFeedbackModal() {
    const modal = document.getElementById('feedbackModal');
    modal.classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const bookingModal = document.getElementById('bookingModal');
    const addModal = document.getElementById('addAppointmentModal');
    const feedbackModal = document.getElementById('feedbackModal');

    if (event.target === bookingModal) {
        closeBookingModal();
    }
    if (event.target === addModal) {
        addModal.classList.add('hidden');
    }
    if (event.target === feedbackModal) {
        closeFeedbackModal();
    }
}

// Enforce 24-hour rule on submit
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action="{{ route('student.appointment.store') }}"]');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const dateInput = form.querySelector('input[name="date"]').value;
        const timeInput = form.querySelector('input[name="time"]').value;

        if (!dateInput || !timeInput) return;

        const selectedDateTime = new Date(`${dateInput}T${timeInput}`);
        const now = new Date();
        const diffInMs = selectedDateTime - now;
        const diffInHours = diffInMs / (1000 * 60 * 60);

        if (diffInHours < 24) {
            e.preventDefault();
            alert("Appointments must be scheduled at least 24 hours in advance.");
        }
    });
});
</script>
@endpush
@endsection
