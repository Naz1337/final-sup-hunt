<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class TopicController extends Controller
{
    public function index()
    {
        // Get student's topics
        $myTopics = Topic::where('student_id', Auth::guard('student')->id())
                        ->with('lecturer')
                        ->latest()
                        ->get();

        // Get only available lecturer topics
        $lecturerTopics = Topic::where('status', 'available')
                              ->where('created_by', 'lecturer')
                              ->whereHas('lecturer')
                              ->with('lecturer')
                              ->latest()
                              ->get();

        // Get all lecturers for the dropdown
        $lecturers = Lecturer::all();

        return view('student.topic.index', compact('myTopics', 'lecturerTopics', 'lecturers'));
    }

    public function create()
    {
        // Only get lecturers who have logged in and changed their password
        $supervisors = Lecturer::where('is_first_login', false)
                              ->orderBy('name')
                              ->get(['id', 'name', 'research_group'])
                              ->map(function ($lecturer) {
                                  return [
                                      'id' => $lecturer->id,
                                      'name' => $lecturer->name . ' (' . $lecturer->research_group . ')'
                                  ];
                              });

        // Debug information
        \Log::info('Available supervisors: ' . $supervisors->count());
        \Log::info('Supervisor details:', $supervisors->toArray());

        return view('student.topics.create', compact('supervisors'));
    }

    public function store(Request $request)
    {
        // Check if student already has an approved or pending topic
        $existingTopic = Topic::where('student_id', Auth::guard('student')->id())
                             ->whereIn('status', ['approved', 'pending'])
                             ->first();

        if ($existingTopic) {
            $message = $existingTopic->status === 'approved' 
                ? 'You already have an approved topic. You cannot create a new topic.'
                : 'You already have a pending topic application. Please wait for supervisor approval or withdraw your current application.';
            
            return back()->with('error', $message);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'research_area' => 'required|string|max:255',
            'lecturer_id' => 'required|exists:lecturers,id'
        ]);

        try {
            Topic::create([
                'title' => $request->title,
                'description' => $request->description,
                'research_area' => $request->research_area,
                'lecturer_id' => $request->lecturer_id,
                'student_id' => Auth::guard('student')->id(),
                'status' => 'pending',
                'created_by' => 'student'
            ]);

            return back()->with('success', 'Topic submitted successfully. Please wait for supervisor approval.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit topic: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Topic $topic)
    {
        if ($topic->status !== 'pending') {
            return back()->with('error', 'Cannot update topic that has been ' . $topic->status);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'research_area' => 'required|string|max:255',
            'lecturer_id' => 'required|exists:lecturers,id'
        ]);

        $topic->update($request->all());

        return back()->with('success', 'Topic updated successfully.');
    }

    public function destroy(Topic $topic)
    {
        if ($topic->status !== 'pending') {
            return back()->with('error', 'Cannot delete topic that has been ' . $topic->status);
        }

        $topic->delete();
        return back()->with('success', 'Topic deleted successfully.');
    }

    public function apply(Topic $topic)
    {
        try {
            // Check if topic is available
            if ($topic->status !== 'available') {
                return back()->with('error', 'This topic is no longer available.');
            }

            // Check if student already has a pending or approved topic
            $existingTopic = Topic::where('student_id', auth()->id())
                ->whereIn('status', ['pending', 'approved'])
                ->exists();

            if ($existingTopic) {
                return back()->with('error', 'You already have a pending or approved topic.');
            }

            // Update the topic with student's application
            $topic->update([
                'student_id' => auth()->id(),
                'status' => 'pending'
            ]);

            return back()->with('success', 'Successfully applied for the topic.');
        } catch (\Exception $e) {
            \Log::error('Topic application failed:', [
                'error' => $e->getMessage(),
                'topic_id' => $topic->id,
                'student_id' => auth()->id()
            ]);
            return back()->with('error', 'Failed to apply for topic. Please try again.');
        }
    }
} 