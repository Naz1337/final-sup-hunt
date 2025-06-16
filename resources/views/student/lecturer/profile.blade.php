@extends('layouts.student')

@section('title', 'Lecturer Profile')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center space-x-6 mb-6">
            @if($lecturer->photo)
                <img class="h-24 w-24 object-cover rounded-full border border-gray-300" src="{{ asset('storage/' . $lecturer->photo) }}" alt="Profile photo">
            @else
                <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-500 text-2xl">👤</span>
                </div>
            @endif
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $lecturer->name }}</h2>
                <div class="text-gray-600">Staff ID: {{ $lecturer->staff_id }}</div>
                <div class="text-gray-600">Email: {{ $lecturer->email }}</div>
                <div class="text-gray-600">Research Group: {{ $lecturer->research_group }}</div>
            </div>
        </div>
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Areas of Expertise</h3>
            <div class="text-gray-700 whitespace-pre-line">{{ $lecturer->expertise ?? '-' }}</div>
        </div>
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Teaching Experience</h3>
            <div class="text-gray-700 whitespace-pre-line">{{ $lecturer->teaching_experience ?? '-' }}</div>
        </div>
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Previous FYP Titles</h3>
            <div class="text-gray-700 whitespace-pre-line">{{ $lecturer->previous_fyp_titles ?? '-' }}</div>
        </div>
    </div>
</div>
@endsection 