@extends('layouts.student')

@section('title', 'Appointments')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Appointments</h2>
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

    <!-- My Appointments -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">My Appointments</h3>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @if($myAppointments->isNotEmpty())
                <div class="divide-y divide-gray-200">
                    @foreach($myAppointments as $appointment)
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        With: {{ $appointment->lecturer->name }}
                                    </div>
                                    <div class="mt-1">
                                        <span class="text-sm text-gray-500">
                                            {{ $appointment->date->format('d M Y') }},
                                            {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        <div class="text-sm text-gray-900">
                                            Location: {{ $appointment->location }}
                                        </div>
                                        @if($appointment->meeting_link && $appointment->status === 'approved')
                                            <a href="{{ $appointment->meeting_link }}"
                                               target="_blank"
                                               class="text-sm text-blue-600 hover:text-blue-800">
                                                Join Meeting
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($appointment->status === 'approved') bg-green-100 text-green-800
                                    @elseif($appointment->status === 'rejected') bg-red-100 text-red-800
                                    @elseif($appointment->status === 'completed') bg-gray-100 text-gray-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                            @if($appointment->feedback)
                                <div class="mt-3 text-sm text-gray-600 bg-gray-50 p-3 rounded">
                                    <span class="font-medium">Feedback:</span> {{ $appointment->feedback }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center text-gray-500">
                    No appointments booked yet.
                </div>
            @endif
        </div>
    </div>

    <!-- Available Appointments -->
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

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('bookingModal');
    if (event.target === modal) {
        closeBookingModal();
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
