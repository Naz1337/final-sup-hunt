@extends('layouts.lecturer')

@section('title', 'Profile Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Profile Settings</h2>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-medium text-gray-800 mb-4">Lecturer Information</h3>
                <form action="{{ route('lecturer.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Profile Photo Section -->
                    <div class="mb-6">
                        <div class="flex items-center space-x-6">
                            <div class="shrink-0">
                                @if($lecturer->photo)
                                    <img class="h-24 w-24 object-cover rounded-full border-2 border-blue-200" src="{{ asset('storage/' . $lecturer->photo) }}" alt="Profile photo">
                                @else
                                    <div class="h-24 w-24 rounded-full bg-gray-100 border-2 border-blue-200 flex items-center justify-center">
                                        <span class="text-gray-500 text-2xl">👤</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                                <input type="file" name="photo" accept="image/*"
                                       class="block w-full text-sm text-gray-600
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-100 file:text-blue-700
                                              hover:file:bg-blue-200 transition-colors
                                              border border-blue-200 rounded-md p-1">
                                @error('photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Non-editable fields -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Staff ID</label>
                            <input type="text" value="{{ $lecturer->staff_id }}"
                                   class="w-full px-3 py-2 rounded-md border border-gray-200 bg-gray-100 text-gray-600 focus:outline-none"
                                   disabled>
                        </div>

                        <!-- Editable fields -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" value="{{ old('name', $lecturer->name) }}" required
                                   class="w-full px-3 py-2 rounded-md border border-blue-200 bg-white text-gray-700
                                          focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" value="{{ $lecturer->email }}"
                                   class="w-full px-3 py-2 rounded-md border border-gray-200 bg-gray-100 text-gray-600 focus:outline-none"
                                   disabled>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Research Group</label>
                            <input type="text" value="{{ $lecturer->research_group }}"
                                   class="w-full px-3 py-2 rounded-md border border-gray-200 bg-gray-100 text-gray-600 focus:outline-none"
                                   disabled>
                        </div>

                        <!-- Expertise Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Areas of Expertise</label>
                            <textarea name="expertise" rows="3"
                                      class="w-full px-3 py-2 rounded-md border border-blue-200 bg-white text-gray-700
                                             focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                      placeholder="Enter your areas of expertise, separated by commas">{{ old('expertise', $lecturer->expertise) }}</textarea>
                            @error('expertise')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Teaching Experience Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teaching Experience</label>
                            <textarea name="teaching_experience" rows="3"
                                      class="w-full px-3 py-2 rounded-md border border-blue-200 bg-white text-gray-700
                                             focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                      placeholder="Enter your teaching experience">{{ old('teaching_experience', $lecturer->teaching_experience) }}</textarea>
                            @error('teaching_experience')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Previous FYP Titles Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Previous FYP Titles</label>
                            <textarea name="previous_fyp_titles" rows="3"
                                      class="w-full px-3 py-2 rounded-md border border-blue-200 bg-white text-gray-700
                                             focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400"
                                      placeholder="Enter your previous FYP titles">{{ old('previous_fyp_titles', $lecturer->previous_fyp_titles) }}</textarea>
                            @error('previous_fyp_titles')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Change Password Section -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-800 mb-4">Change Password</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                    <input type="password" name="current_password"
                                           class="w-full px-3 py-2 rounded-md border border-blue-200 bg-white text-gray-700
                                                  focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                                    @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                    <input type="password" name="password"
                                           class="w-full px-3 py-2 rounded-md border border-blue-200 bg-white text-gray-700
                                                  focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                                    @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                    <input type="password" name="password_confirmation"
                                           class="w-full px-3 py-2 rounded-md border border-blue-200 bg-white text-gray-700
                                                  focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700
                                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                                       transition-colors">
                                Update Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
