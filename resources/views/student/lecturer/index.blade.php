@extends('layouts.student')

@section('title', 'List of Lecturers')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-6">List of Lecturers</h2>
    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative">
            <input type="text" id="lecturerSearch" placeholder="Search by lecturer name..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <i class="fas fa-search text-gray-400"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4">
        <ul id="lecturerList">
            @forelse($lecturers as $lecturer)
                <li class="flex items-center space-x-4 py-4 border-b last:border-b-0 lecturer-row">
                    <a href="{{ route('student.lecturer.profile', $lecturer->id) }}">
                        @if($lecturer->photo)
                            <img src="{{ asset('storage/' . $lecturer->photo) }}" alt="{{ $lecturer->name }}" class="w-14 h-14 rounded-full object-cover border border-gray-300">
                        @else
                            <div class="w-14 h-14 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 text-2xl">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </a>
                    <div>
                        <a href="{{ route('student.lecturer.profile', $lecturer->id) }}" class="text-lg font-semibold text-blue-700 hover:underline lecturer-name">{{ $lecturer->name }}</a>
                        <div class="text-gray-600">{{ $lecturer->research_group }}</div>
                    </div>
                </li>
            @empty
                <li class="text-gray-500 py-4">No lecturers found.</li>
            @endforelse
        </ul>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('lecturerSearch').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('.lecturer-row');
    rows.forEach(row => {
        const name = row.querySelector('.lecturer-name').textContent.toLowerCase();
        if (name.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endpush
@endsection 