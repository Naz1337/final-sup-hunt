@extends('layouts.coordinator')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto">
        <h1 class="text-2xl font-bold mb-6">Edit Lecturer</h1>

        <form action="{{ route('coordinator.lecturers.update', $lecturer->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" 
                       name="name" 
                       value="{{ $lecturer->name }}"
                       class="mt-1 block w-full rounded-md border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" 
                       name="email" 
                       value="{{ $lecturer->email }}"
                       class="mt-1 block w-full rounded-md border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Research Group</label>
                <select name="research_group" class="mt-1 block w-full rounded-md border-gray-300">
                    @foreach(['CSRG', 'VISIC', 'MIRG', 'Cy-SIG', 'SERG', 'KECL', 'DSSim', 'DBIS', 'EDU-TECH', 'ISP', 'CNRG', 'SCORE'] as $group)
                        <option value="{{ $group }}" {{ $lecturer->research_group == $group ? 'selected' : '' }}>
                            {{ $group }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('coordinator.lecturers.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">
                    Update Lecturer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 