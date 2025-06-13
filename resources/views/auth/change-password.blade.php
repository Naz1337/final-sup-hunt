@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen" style="background-color: #C8D9E6;">
    <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-6">
            <img src="{{ asset('images/innovisory.png') }}" alt="Innovisory Logo" class="h-16 mx-auto mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Change Password</h2>
        </div>

        @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route($type . '.change-password') }}">
            @csrf

            <div class="mb-4">
                <label for="current_password" class="block text-gray-700 font-medium mb-2">Current Password</label>
                <input type="password" 
                       name="current_password" 
                       id="current_password" 
                       class="w-full px-4 py-3 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900"
                       required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-medium mb-2">New Password</label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       class="w-full px-4 py-3 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900"
                       required>
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Confirm New Password</label>
                <input type="password" 
                       name="password_confirmation" 
                       id="password_confirmation" 
                       class="w-full px-4 py-3 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900"
                       required>
            </div>

            <button type="submit" 
                    style="background-color: #C8D9E6;" 
                    class="w-full py-3 px-6 rounded-lg font-bold text-gray-700 hover:bg-[#b5c6d3] transition-colors">
                Change Password
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="/" class="text-blue-600 hover:underline">Back to Home</a>
        </div>
    </div>
</div>
@endsection
