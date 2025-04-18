<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::where('student_id', Auth::guard('student')->id())
                      ->with('lecturer')
                      ->latest()
                      ->get();
        
        $lecturers = Lecturer::all();
        
        return view('student.topic.index', compact('topics', 'lecturers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'research_area' => 'required|string|max:255',
            'lecturer_id' => 'required|exists:lecturers,id'
        ]);

        $topic = Topic::create([
            'student_id' => Auth::guard('student')->id(),
            'lecturer_id' => $request->lecturer_id,
            'title' => $request->title,
            'description' => $request->description,
            'research_area' => $request->research_area,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Topic submitted successfully.');
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
} 