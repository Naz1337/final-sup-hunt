@extends('layouts.student')

@section('title', 'Topic Management')

@section('content')
<div class="container mx-auto px-4">

    <!-- @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif -->

    <!-- Check for existing topic and show warning -->
    @php
        $existingTopic = $myTopics->first(function($topic) {
            return in_array($topic->status, ['pending', 'approved']);
        });
    @endphp

    @if($existingTopic)
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">
        <span class="block sm:inline">
            @if($existingTopic->status === 'pending')
                <i class="fas fa-exclamation-triangle mr-2"></i>
                You have a pending topic application. You cannot apply for additional topics until your current application is processed.
            @else
                <i class="fas fa-info-circle mr-2"></i>
                You already have an approved topic. You cannot apply for additional topics.
            @endif
        </span>
    </div>
    @endif

    <!-- My Topics Section -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">My Topics</h2>
            @if(!$existingTopic)
                <button onclick="showAddTopicModal()"
                        class="bg-gradient-to-br from-[#C8D9E6] to-[#C8D9E6] hover:from-[#A6B9C6] hover:to-[#A6B9C6] text-gray-700 px-4 py-2 rounded-lg flex items-center transition">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Topic
                </button>
            @endif
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
        @endif

        <!-- My Topics Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-[#C8D9E6] text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Research Area</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Lecturer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($myTopics as $topic)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $topic->title }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ Str::limit($topic->description, 100) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ $topic->research_area }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($topic->lecturer)
                                <div class="text-sm font-medium text-gray-900">{{ $topic->lecturer->name }}</div>
                                <div class="text-sm text-gray-500">{{ $topic->lecturer->staff_id }}</div>
                            @else
                                <span class="text-sm text-gray-500">Not assigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($topic->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($topic->status === 'approved') bg-green-100 text-green-800
                                @elseif($topic->status === 'rejected') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($topic->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex space-x-2">
                                @if($topic->status === 'pending')
                                    <form action="{{ route('student.topic.destroy', $topic->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Are you sure you want to withdraw this topic?');"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-times-circle mr-1"></i> Withdraw
                                        </button>
                                    </form>
                                @endif
                                @if($topic->feedback)
                                    <button onclick="viewFeedback('{{ addslashes($topic->feedback) }}')"
                                            class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-comment-alt mr-1"></i> Feedback
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No topics submitted yet. Click "Add New Topic" to submit one.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Lecturer Topics Section -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold mb-6">Available Supervisor Topics</h2>

        <!-- Search Bar -->
        <div class="mb-4">
            <div class="relative">
                <input type="text"
                       id="supervisorSearch"
                       placeholder="Search by supervisor name..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-[#C8D9E6] text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Supervisor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Research Area</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="supervisorTableBody" class="bg-white divide-y divide-gray-200">
                    @forelse($lecturerTopics as $topic)
                    <tr class="hover:bg-gray-50 supervisor-row">
                        <td class="px-6 py-4">
                            @if($topic->lecturer)
                                <div class="flex items-center space-x-3">
                                    @if($topic->lecturer->photo)
                                        <img src="{{ asset('storage/' . $topic->lecturer->photo) }}" alt="{{ $topic->lecturer->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-300">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 text-xl">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('student.lecturer.profile', $topic->lecturer->id) }}" class="text-sm font-medium text-blue-700 hover:underline supervisor-name">{{ $topic->lecturer->name }}</a>
                                        <div class="text-sm text-gray-500">{{ $topic->lecturer->research_group }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="text-sm text-gray-500">Lecturer not found</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $topic->title }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ ($topic->description) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ $topic->research_area }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            @if($topic->status === 'available')
                                @if($existingTopic)
                                    <span class="text-gray-500">
                                        <i class="fas fa-ban mr-1"></i> Cannot Apply
                                        <span class="block text-xs mt-1">
                                            (You have a {{ $existingTopic->status }} topic)
                                        </span>
                                    </span>
                                @else
                                    <form action="{{ route('student.topic.apply', $topic->id) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to apply for this topic? You can only have one topic at a time.');">
                                        @csrf
                                        <button type="submit"
                                                class="text-blue-600 hover:text-blue-900 flex items-center">
                                            <i class="fas fa-hand-pointer mr-1"></i> Apply
                                        </button>
                                    </form>
                                @endif
                            @else
                                <span class="text-gray-500">
                                    <i class="fas fa-lock mr-1"></i> Unavailable
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No supervisor topics available at the moment.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Topic Modal -->
    <div id="addTopicModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        @if($existingTopic)
            <script>
                // Immediately close modal if student has existing topic
                closeAddTopicModal();
            </script>
        @else
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Topic</h3>
                    <form action="{{ route('student.topic.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" required rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Research Area</label>
                            <input type="text" name="research_area" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Supervisor</label>
                            <select name="lecturer_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                                <option value="">Select a supervisor</option>
                                @foreach($lecturers as $lecturer)
                                    <option value="{{ $lecturer->id }}">
                                        {{ $lecturer->name }} ({{ $lecturer->research_group }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" onclick="closeAddTopicModal()"
                                    class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-[#C8D9E6] text-gray-700 rounded hover:bg-[#A6B9C6] transition">
                                Submit Topic
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
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
            <div id="feedbackContent" class="text-gray-600 bg-gray-50 p-4 rounded"></div>
            <div class="mt-6 flex justify-end">
                <button onclick="document.getElementById('feedbackModal').classList.add('hidden')"
                        class="px-4 py-2 bg-[#C8D9E6] text-gray-700 rounded hover:bg-[#A6B9C6] transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewFeedback(feedback) {
    document.getElementById('feedbackContent').textContent = feedback;
    document.getElementById('feedbackModal').classList.remove('hidden');
}

function showAddTopicModal() {
    @if($existingTopic)
        alert('You cannot create a new topic because you already have a {{ $existingTopic->status }} topic.');
        return;
    @endif
    document.getElementById('addTopicModal').classList.remove('hidden');
}

function closeAddTopicModal() {
    document.getElementById('addTopicModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    let modal = document.getElementById('addTopicModal');
    if (event.target == modal) {
        closeAddTopicModal();
    }
}

// Add search functionality
document.getElementById('supervisorSearch').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('.supervisor-row');
    rows.forEach(row => {
        const supervisorName = row.querySelector('.supervisor-name').textContent.toLowerCase();
        if (supervisorName.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endpush
@endsection
