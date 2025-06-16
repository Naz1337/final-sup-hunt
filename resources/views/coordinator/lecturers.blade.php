@extends('layouts.coordinator')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Lecturer Management</h1>
        <div class="flex gap-4">
            <button class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-file-import mr-2"></i> Import Lecturers
            </button>
            <button class="bg-green-500 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-file-export mr-2"></i> Generate Report
            </button>
        </div>
    </div>

    <!-- Search and Sort -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form class="flex gap-4" method="GET">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       placeholder="Search by name..."
                       value="{{ request('search') }}"
                       class="w-full px-4 py-2 border rounded-lg">
            </div>
            <select name="sort" 
                    class="px-4 py-2 border rounded-lg"
                    onchange="this.form.submit()">
                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Sort by Name</option>
                <option value="research_group" {{ request('sort') == 'research_group' ? 'selected' : '' }}>Sort by Research Group</option>
                <option value="staff_id" {{ request('sort') == 'staff_id' ? 'selected' : '' }}>Sort by Staff ID</option>
            </select>
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg">
                Apply
            </button>
        </form>
    </div>

    <!-- Lecturers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Staff ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Research Group</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($lecturers as $lecturer)
                <tr>
                    <td class="px-6 py-4">{{ $lecturer->staff_id }}</td>
                    <td class="px-6 py-4">{{ $lecturer->name }}</td>
                    <td class="px-6 py-4">{{ $lecturer->email }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-sm rounded-full 
                            {{ $lecturer->research_group == 'CSRG' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $lecturer->research_group == 'VISIC' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $lecturer->research_group == 'MIRG' ? 'bg-green-100 text-green-800' : '' }}">
                            {{ $lecturer->research_group }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <form action="{{ route('coordinator.lecturers.edit', $lecturer->id) }}" method="GET">
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Edit
                                </button>
                            </form>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        No lecturers found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add the edit modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Lecturer</h3>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="lecturer_id" name="lecturer_id">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="edit_name" class="mt-1 block w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="edit_email" class="mt-1 block w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Research Group</label>
                    <select name="research_group" id="edit_research_group" class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="CSRG">CSRG</option>
                        <option value="VISIC">VISIC</option>
                        <option value="MIRG">MIRG</option>
                        <option value="Cy-SIG">Cy-SIG</option>
                        <option value="SERG">SERG</option>
                        <option value="KECL">KECL</option>
                        <option value="DSSim">DSSim</option>
                        <option value="DBIS">DBIS</option>
                        <option value="EDU-TECH">EDU-TECH</option>
                        <option value="ISP">ISP</option>
                        <option value="CNRG">CNRG</option>
                        <option value="SCORE">SCORE</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditModal(id, name, email, research_group) {
    document.getElementById('lecturer_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_research_group').value = research_group;
    document.getElementById('editForm').action = `/coordinator/lecturers/${id}`;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection 