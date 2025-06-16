@extends('layouts.coordinator')

@section('content')
<div class="container mx-auto px-4 py-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <span class="block sm:inline">{{ session('success') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                    <title>Close</title>
                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                </svg>
            </span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Add New Task</h2>
        
        <form action="{{ route('coordinator.timeframe.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Task Name</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">For Role</label>
                    <select name="for_role" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        <option value="All">All</option>
                        <option value="Student">Students Only</option>
                        <option value="Lecturer">Lecturers Only</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date') }}" required
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="datetime-local" name="end_date" value="{{ old('end_date') }}" required
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="3" 
                              class="mt-1 w-full rounded-md border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    Add Task
                </button>
            </div>
        </form>
    </div>

    <!-- Tasks List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Task</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">For</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tasks as $task)
                <tr>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $task->title }}</div>
                        <div class="text-sm text-gray-500">{{ $task->description }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $task->getRoleColorClass() }}">
                            {{ $task->for_role }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div>{{ $task->start_date->format('M d, Y') }}</div>
                        <div class="text-sm text-gray-500">{{ $task->start_date->format('h:i A') }}</div>
                        <div class="text-xs text-gray-400 mt-1">to</div>
                        <div>{{ $task->end_date->format('M d, Y') }}</div>
                        <div class="text-sm text-gray-500">{{ $task->end_date->format('h:i A') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <form action="{{ route('coordinator.timeframe.toggle-status', $task) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-2 py-1 text-xs rounded-full {{ $task->getStatusColorClass() }}">
                                {{ ucfirst($task->status) }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-3">
                            <a href="{{ route('coordinator.timeframe.edit', $task) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('coordinator.timeframe.destroy', $task) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this task?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        No tasks found. Add your first task above.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Add auto-dismiss for success message
document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.querySelector('.bg-green-100');
    if (successMessage) {
        setTimeout(function() {
            successMessage.style.transition = 'opacity 1s';
            successMessage.style.opacity = '0';
            setTimeout(function() {
                successMessage.remove();
            }, 1000);
        }, 3000);
    }

    // Close button functionality
    const closeButton = successMessage?.querySelector('svg');
    if (closeButton) {
        closeButton.addEventListener('click', function() {
            successMessage.remove();
        });
    }
});
</script>
@endpush 