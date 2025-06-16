@extends('layouts.coordinator')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto">
        <h1 class="text-2xl font-bold mb-6">Edit Student</h1>

        <form action="{{ route('coordinator.students.update', $student->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" 
                       name="name" 
                       value="{{ $student->name }}"
                       class="mt-1 block w-full rounded-md border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" 
                       name="email" 
                       value="{{ $student->email }}"
                       class="mt-1 block w-full rounded-md border-gray-300">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Program</label>
                <select name="program" class="mt-1 block w-full rounded-md border-gray-300">
                    <option value="Software Engineering" {{ $student->program == 'Software Engineering' ? 'selected' : '' }}>
                        Software Engineering
                    </option>
                    <option value="Computer System & Networking" {{ $student->program == 'Computer System & Networking' ? 'selected' : '' }}>
                        Computer System & Networking
                    </option>
                    <option value="Computer Graphics & Multimedia" {{ $student->program == 'Computer Graphics & Multimedia' ? 'selected' : '' }}>
                        Computer Graphics & Multimedia
                    </option>
                    <option value="Cybersecurity" {{ $student->program == 'Cybersecurity' ? 'selected' : '' }}>
                        Cybersecurity
                    </option>
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('coordinator.students.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">
                    Update Student
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 