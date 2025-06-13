@extends('layouts.coordinator')

@section('content')
<div class="container mx-auto px-4 py-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('warning') }}</span>
            @if(session('importErrors'))
                <ul class="list-disc list-inside mt-2">
                    @foreach(session('importErrors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Student Management</h1>
        <div class="flex gap-4">
            <button class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-file-import mr-2"></i> Import Students
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
                <option value="program" {{ request('sort') == 'program' ? 'selected' : '' }}>Sort by Program</option>
                <option value="matric_id" {{ request('sort') == 'matric_id' ? 'selected' : '' }}>Sort by Matric ID</option>
            </select>
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg">
                Apply
            </button>
        </form>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matric ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Program</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($students as $student)
                <tr>
                    <td class="px-6 py-4">{{ $student->matric_id }}</td>
                    <td class="px-6 py-4">{{ $student->name }}</td>
                    <td class="px-6 py-4">{{ $student->email }}</td>
                    <td class="px-6 py-4">{{ $student->program }}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <form action="{{ route('coordinator.students.edit', $student->id) }}" method="GET">
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
                        No students found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Important Notes Section -->
    <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Important Notes</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc list-inside">
                        <li>Matric ID must be in format: CA12345</li>
                        <li>Duplicate Matric IDs will be rejected</li>
                        <li>All fields (Matric ID, Name, Email) are required</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Import Students</h3>
                <form action="{{ route('coordinator.students.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">CSV File</label>
                        <input type="file" 
                               name="file" 
                               accept=".csv"
                               class="mt-1 block w-full">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeImportModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openEditModal(id, name, email, program) {
        document.getElementById('student_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_program').value = program;
        document.getElementById('editForm').action = `/coordinator/students/${id}`;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    // Update the Import Students button to use this function
    document.querySelector('button:contains("Import Students")').onclick = openImportModal;
    </script>
</div>

<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Student</h3>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" id="student_id" name="student_id">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="edit_name" class="mt-1 block w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="edit_email" class="mt-1 block w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Program</label>
                    <select name="program" id="edit_program" class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="Software Engineering">Software Engineering</option>
                        <option value="Computer System & Networking">Computer System & Networking</option>
                        <option value="Computer Graphics & Multimedia">Computer Graphics & Multimedia</option>
                        <option value="Cybersecurity">Cybersecurity</option>
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
@endsection 