<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy('start_date')->get();
        return view('coordinator.timeframe.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'for_role' => 'required|in:All,Student,Lecturer'
        ]);

        try {
            DB::beginTransaction();

            Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'for_role' => $request->for_role,
                'status' => 'in-progress'
            ]);

            DB::commit();
            return back()->with('success', 'Task created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create task.')
                        ->withInput();
        }
    }

    public function edit(Task $task)
    {
        return view('coordinator.timeframe.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'for_role' => 'required|in:All,Student,Lecturer',
            'status' => 'required|in:in-progress,completed'
        ]);

        try {
            DB::beginTransaction();

            $task->update($request->all());

            DB::commit();
            return redirect()->route('coordinator.timeframe.index')
                            ->with('success', "Task '{$task->title}' has been updated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update task.')
                        ->withInput();
        }
    }

    public function destroy(Task $task)
    {
        try {
            $task->delete();
            return back()->with('success', 'Task deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete task.');
        }
    }

    public function toggleStatus(Task $task)
    {
        try {
            $task->update([
                'status' => $task->status === 'in-progress' ? 'completed' : 'in-progress'
            ]);
            return back()->with('success', 'Task status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update task status.');
        }
    }
} 