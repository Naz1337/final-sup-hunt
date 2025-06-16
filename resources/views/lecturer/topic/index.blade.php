@extends('layouts.lecturer')

@section('title', 'Topic Management')

@section('content')
<div class="container mx-auto px-4">
    <!-- Lecturer Topics Section -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">My Topics</h2>
            <button onclick="showAddTopicModal()"
                    class="bg-gradient-to-br from-[#C8D9E6] to-[#C8D9E6] hover:from-[#A6B9C6] hover:to-[#A6B9C6] text-gray-700 px-4 py-2 rounded-lg flex items-center transition">
                <i class="fas fa-plus mr-2"></i>
                Add New Topic
            </button>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
        @endif

        <!-- Lecturer Created Topics -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-[#C8D9E6] text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Research Area</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lecturerTopics as $topic)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $topic->title }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ $topic->description }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ $topic->research_area }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($topic->student)
                                <div class="text-sm text-gray-900">{{ $topic->student->name }}</div>
                                <div class="text-sm text-gray-500">{{ $topic->student->matric_id }}</div>
                            @else
                                <div class="text-sm text-gray-500">Not assigned</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($topic->status === 'available') bg-green-100 text-green-800
                                @elseif($topic->status === 'unavailable') bg-red-100 text-red-800
                                @elseif($topic->status === 'approved') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($topic->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            @if($topic->student && $topic->status === 'pending')
                                <button onclick="showReviewModal({{ $topic->id }}, '{{ $topic->title }}')"
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                    Review
                                </button>
                            @elseif($topic->status === 'available')
                                <button onclick="showEditTopicModal({{ $topic->id }})"
                                        class="text-blue-600 hover:text-blue-900 mr-3">
                                    Edit
                                </button>
                                <form action="{{ route('lecturer.topic.destroy', $topic) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Are you sure you want to delete this topic?')">
                                        Delete
                                    </button>
                                </form>
                            @endif

                            @if($topic->feedback)
                                <button onclick="viewFeedback('{{ addslashes($topic->feedback) }}')"
                                        class="text-gray-600 hover:text-gray-900 ml-3">
                                    View Feedback
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No topics created yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Student Proposals Section -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold mb-6">Student Proposals</h2>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-[#C8D9E6] text-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Research Area</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($studentProposals as $topic)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $topic->student->name }}</div>
                            <div class="text-sm text-gray-500">{{ $topic->student->matric_id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $topic->title }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ $topic->description }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ $topic->research_area }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($topic->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($topic->status === 'approved') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($topic->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            @if($topic->status === 'pending')
                                <button onclick="showReviewModal({{ $topic->id }})" class="text-blue-600 hover:text-blue-900">
                                    Review
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No student proposals yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Topic Modal -->
    <div id="addTopicModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Add New Topic</h3>
                <button onclick="closeAddTopicModal()" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('lecturer.topic.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text"
                               name="title"
                               required
                               class="mt-1 block w-full rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#C8D9E6] text-gray-900 px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description"
                                  rows="4"
                                  required
                                  class="mt-1 block w-full rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#C8D9E6] text-gray-900 px-4 py-2"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Research Area</label>
                        <input type="text"
                               name="research_area"
                               required
                               class="mt-1 block w-full rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#C8D9E6] text-gray-900 px-4 py-2">
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                            onclick="closeAddTopicModal()"
                            class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-[#C8D9E6] text-gray-700 rounded hover:bg-[#A6B9C6] transition">
                        Create Topic
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Topic Modal -->
    <div id="editTopicModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Topic</h3>
                    <button onclick="closeEditTopicModal()" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="editTopicForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" name="title" id="editTitle" required
                               class="mt-1 block w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="editDescription" required rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Research Area</label>
                        <input type="text" name="research_area" id="editResearchArea" required
                               class="mt-1 block w-full rounded-md border-gray-300">
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditTopicModal()"
                                class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Update Topic
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Review Topic Application</h3>
                    <button onclick="closeReviewModal()" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p id="reviewTopicTitle" class="text-gray-600 mb-4"></p>
                <form id="reviewForm" method="POST" onsubmit="submitReview(event)">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="reviewStatus" required
                                class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Feedback</label>
                        <textarea name="feedback" id="reviewFeedback" required rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300"
                                  placeholder="Provide feedback for the student..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" onclick="closeReviewModal()"
                                class="px-4 py-2 border rounded text-gray-600 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Submit Review
                        </button>
                    </div>
                </form>
            </div>
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
function showReviewModal(topicId, title) {
    document.getElementById('reviewTopicTitle').textContent = title || 'Review Topic';
    document.getElementById('reviewForm').setAttribute('data-topic-id', topicId);
    document.getElementById('reviewStatus').value = 'approved';
    document.getElementById('reviewFeedback').value = '';
    document.getElementById('reviewModal').classList.remove('hidden');
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
}

function submitReview(event) {
    event.preventDefault();
    const form = event.target;
    const topicId = form.getAttribute('data-topic-id');
    const formData = new FormData(form);
    const url = "{{ url('/lecturer/topic') }}/" + topicId;

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeReviewModal();
            window.location.reload();
        } else {
            alert(data.message || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing your request');
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('reviewModal');
    if (event.target === modal) {
        closeReviewModal();
    }
}

function viewFeedback(feedback) {
    document.getElementById('feedbackContent').textContent = feedback;
    document.getElementById('feedbackModal').classList.remove('hidden');
}

function showAddTopicModal() {
    document.getElementById('addTopicModal').classList.remove('hidden');
}

function closeAddTopicModal() {
    document.getElementById('addTopicModal').classList.add('hidden');
}

function showEditTopicModal(id, title, description, researchArea) {
    document.getElementById('editTitle').value = title;
    document.getElementById('editDescription').value = description;
    document.getElementById('editResearchArea').value = researchArea;
    document.getElementById('editTopicForm').action = `/lecturer/my-topics/${id}`;
    document.getElementById('editTopicModal').classList.remove('hidden');
}

function closeEditTopicModal() {
    document.getElementById('editTopicModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    let modal = document.getElementById('editTopicModal');
    if (event.target == modal) {
        closeEditTopicModal();
    }
}
</script>
@endpush
@endsection
