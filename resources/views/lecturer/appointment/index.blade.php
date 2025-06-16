@extends('layouts.lecturer')

@section('title', 'Appointment Management')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Appointment Management</h2>
        <button onclick="showAddAppointmentModal()" 
                class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>Add Appointment
        </button>
    </div>
    

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Appointments List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
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
                        @if($appointment->student)
                            <div class="text-sm font-medium text-gray-900">{{ $appointment->student->name }}</div>
                            <div class="text-sm text-gray-500">{{ $appointment->student->matric_id }}</div>
                        @else
                            <div class="text-sm text-gray-500">Not assigned</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($appointment->title)
                            <div class="text-sm font-medium text-gray-900">{{ $appointment->title }}</div>
                            @if($appointment->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($appointment->description, 50) }}</div>
                            @endif
                        @else
                            <div class="text-sm text-gray-500">Available slot</div>
                        @endif
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
                            Meeting Link
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
                        <div class="flex space-x-2">
                            @if($appointment->status === 'pending')
                                <button onclick="showReviewModal({{ $appointment->id }}, '{{ $appointment->title ?? 'Appointment Request' }}')" 
                                        class="text-blue-600 hover:text-blue-900">
                                    Review
                                </button>
                            @elseif($appointment->status === 'approved')
                                <button onclick="showCompleteModal({{ $appointment->id }})" 
                                        class="text-green-600 hover:text-green-900">
                                    Mark Complete
                                </button>
                            @elseif($appointment->status === 'available')
                                <form action="{{ route('lecturer.appointment.destroy', $appointment) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this appointment slot?');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>
                            @endif
                            
                            @if($appointment->feedback)
                                <button onclick="viewFeedback('{{ addslashes($appointment->feedback) }}')" 
                                        class="text-gray-600 hover:text-gray-900">
                                    View Feedback
                                </button>
                            @endif
                        </div>
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

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium" id="reviewAppointmentTitle"></h3>
                <button onclick="document.getElementById('reviewModal').classList.add('hidden')"
                        class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="reviewForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" required id="appointmentStatus"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Feedback</label>
                        <textarea name="feedback" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                  placeholder="Provide feedback to the student..."></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                            onclick="document.getElementById('reviewModal').classList.add('hidden')"
                            class="px-4 py-2 border rounded-md text-gray-600 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Complete Modal -->
    <div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Complete Appointment</h3>
                <button onclick="document.getElementById('completeModal').classList.add('hidden')"
                        class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="completeForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="completed">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Feedback</label>
                        <textarea name="feedback" rows="4" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"
                                  placeholder="Provide feedback about the meeting..."></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                            onclick="document.getElementById('completeModal').classList.add('hidden')"
                            class="px-4 py-2 border rounded-md text-gray-600 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        Mark as Complete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div id="feedbackModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Feedback</h3>
                <button onclick="document.getElementById('feedbackModal').classList.add('hidden')"
                        class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="feedbackContent" class="text-gray-600"></div>
            <div class="mt-6 flex justify-end">
                <button onclick="document.getElementById('feedbackModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Add Appointment Modal -->
    <div id="addAppointmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Schedule New Appointment</h3>
                <button onclick="closeAddAppointmentModal()" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- @if(config('app.debug'))
                <div class="mb-4 p-4 bg-gray-100 rounded">
                    <p class="text-sm text-gray-600">Debug Info:</p>
                    <p class="text-xs">Auth ID: {{ auth()->id() }}</p>
                    <p class="text-xs">Route: {{ route('lecturer.appointment.store') }}</p>
                </div>
            @endif -->

            <form action="{{ route('lecturer.appointment.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="date" required
                                   min="{{ date('Y-m-d') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Time</label>
                            <input type="time" name="time" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Location</label>
                        <input type="text" name="location" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                               placeholder="e.g., Office Room 123, Online Meeting">
                        @error('location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Meeting Link (Optional)</label>
                        <input type="url" name="meeting_link"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                               placeholder="https://meet.google.com/...">
                        @error('meeting_link')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeAddAppointmentModal()"
                            class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Create Appointment Slot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showReviewModal(appointmentId, title) {
    document.getElementById('reviewAppointmentTitle').textContent = title;
    document.getElementById('reviewForm').action = `/lecturer/appointments/${appointmentId}`;
    document.getElementById('reviewModal').classList.remove('hidden');
}

function showCompleteModal(appointmentId) {
    document.getElementById('completeForm').action = `/lecturer/appointments/${appointmentId}`;
    document.getElementById('completeModal').classList.remove('hidden');
}

function viewFeedback(feedback) {
    document.getElementById('feedbackContent').textContent = feedback;
    document.getElementById('feedbackModal').classList.remove('hidden');
}

// Show/hide meeting link field based on status
document.getElementById('appointmentStatus').addEventListener('change', function() {
    const meetingLinkDiv = document.getElementById('meetingLinkDiv');
    meetingLinkDiv.style.display = this.value === 'approved' ? 'block' : 'none';
});

function showAddAppointmentModal() {
    document.getElementById('addAppointmentModal').classList.remove('hidden');
}

function closeAddAppointmentModal() {
    document.getElementById('addAppointmentModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('addAppointmentModal');
    if (event.target === modal) {
        closeAddAppointmentModal();
    }
}
</script>
@endpush
@endsection 