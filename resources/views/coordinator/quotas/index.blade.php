@extends('layouts.coordinator')

@section('title', 'Quota Management')

@section('content')
<div class="container mx-auto px-4 py-6">
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

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Quota Management</h1>
        <button onclick="openAssignModal()"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Assign New Quota
        </button>
    </div>

    <!-- Quotas Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lecturer Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Research Group</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current/Max</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($quotas as $quota)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $quota->lecturer->staff_id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $quota->lecturer->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                            {{ $quota->lecturer->research_group }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm {{ $quota->current_supervisees >= $quota->max_supervisees ? 'text-red-600' : 'text-green-600' }}">
                            {{ $quota->current_supervisees }}/{{ $quota->max_supervisees }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex gap-2">
                            <button onclick="editQuota({{ $quota->id }}, {{ $quota->max_supervisees }}, {{ $quota->current_supervisees }})"
                                    class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('coordinator.quotas.destroy', $quota) }}"
                                  method="POST"
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this quota?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t">
            {{ $quotas->links() }}
        </div>
    </div>

    <!-- Edit Quota Modal -->
    <div id="editQuotaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Quota</h3>
                <form id="editQuotaForm" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Maximum Supervisees</label>
                        <input type="number"
                               name="max_supervisees"
                               id="edit_max_supervisees"
                               min="0"
                               max="20"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                               required>
                        <p class="mt-1 text-sm text-gray-500">Current supervisees: <span id="current_supervisees"></span></p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button"
                                onclick="closeEditModal()"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add this new Assign Modal -->
    <div id="assignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Assign New Quota</h3>
                <form action="{{ route('coordinator.quotas.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Lecturer</label>
                        <select name="lecturer_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                required>
                            <option value="">Select a lecturer</option>
                            @foreach($unassignedLecturers as $lecturer)
                                <option value="{{ $lecturer->id }}">
                                    {{ $lecturer->name }} ({{ $lecturer->staff_id }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Maximum Supervisees</label>
                        <input type="number"
                               name="max_supervisees"
                               min="0"
                               max="20"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                               required>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button"
                                onclick="closeAssignModal()"
                                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md">
                            Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editQuota(quotaId, maxSupervisees, currentSupervisees) {
    const form = document.getElementById('editQuotaForm');
    form.action = `/coordinator/quotas/${quotaId}`;
    document.getElementById('edit_max_supervisees').value = maxSupervisees;
    document.getElementById('edit_max_supervisees').min = currentSupervisees;
    document.getElementById('current_supervisees').textContent = currentSupervisees;
    document.getElementById('editQuotaModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editQuotaModal').classList.add('hidden');
}

function openAssignModal() {
    document.getElementById('assignModal').classList.remove('hidden');
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
}
</script>
@endsection
