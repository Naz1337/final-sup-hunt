<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Quota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LecturerTopicController extends Controller
{
    public function index()
    {
        // Get topics created by lecturer (including those with assigned students)
        $lecturerTopics = Topic::with('student')
            ->where('lecturer_id', Auth::guard('lecturer')->id())
            ->where('created_by', 'lecturer')
            ->whereHas('lecturer')
            ->latest()
            ->get();

        // Get topics proposed by students themselves
        $studentProposals = Topic::with('student')
            ->where('lecturer_id', Auth::guard('lecturer')->id())
            ->where('created_by', 'student')
            ->whereHas('lecturer')
            ->latest()
            ->get();

        return view('lecturer.topic.index', compact('lecturerTopics', 'studentProposals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'research_area' => 'required|string|max:255',
        ]);

        Topic::create([
            'title' => $request->title,
            'description' => $request->description,
            'research_area' => $request->research_area,
            'lecturer_id' => Auth::guard('lecturer')->id(),
            'status' => 'available',
            'created_by' => 'lecturer'
        ]);

        return back()->with('success', 'Topic created successfully.');
    }

    public function update(Request $request, Topic $topic)
    {
        // Debug request data
        \Log::info('Topic Update Request:', [
            'topic_id' => $topic->id,
            'request_data' => $request->all(),
            'current_status' => $topic->status,
            'student_id' => $topic->student_id
        ]);

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'feedback' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            // If approving the topic
            if ($request->status === 'approved') {
                // Get lecturer's quota
                $quota = Quota::where('lecturer_id', Auth::guard('lecturer')->id())->first();
                
                \Log::info('Lecturer Quota:', [
                    'lecturer_id' => Auth::guard('lecturer')->id(),
                    'quota' => $quota ? [
                        'current' => $quota->current_supervisees,
                        'max' => $quota->max_supervisees
                    ] : 'No quota found'
                ]);

                if (!$quota) {
                    throw new \Exception('No quota set for this lecturer.');
                }

                if ($quota->current_supervisees >= $quota->max_supervisees) {
                    throw new \Exception('You have reached your maximum number of supervisees.');
                }

                // Check if student already has an approved topic
                $existingApprovedTopic = Topic::where('student_id', $topic->student_id)
                                            ->where('status', 'approved')
                                            ->exists();

                if ($existingApprovedTopic) {
                    throw new \Exception('This student already has an approved topic.');
                }

                // Increment current supervisees count
                $quota->increment('current_supervisees');

                // Make other applications by this student rejected
                Topic::where('student_id', $topic->student_id)
                     ->where('id', '!=', $topic->id)
                     ->where('status', 'pending')
                     ->update([
                         'status' => 'rejected',
                         'feedback' => 'Another topic was approved'
                     ]);
            }

            // Update the topic
            $topic->update([
                'status' => $request->status,
                'feedback' => $request->feedback
            ]);

            // If rejecting a lecturer-created topic, reset it
            if ($request->status === 'rejected' && $topic->created_by === 'lecturer') {
                $topic->update([
                    'status' => 'available',
                    'student_id' => null
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Topic review submitted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Topic update failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'topic_id' => $topic->id,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Topic $topic)
    {
        if ($topic->lecturer_id !== Auth::guard('lecturer')->id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $topic->delete();
        return back()->with('success', 'Topic deleted successfully.');
    }

    public function updateMyTopic(Request $request, Topic $topic)
    {
        // Check if topic belongs to the lecturer and was created by them
        if ($topic->lecturer_id !== Auth::guard('lecturer')->id() || $topic->created_by !== 'lecturer') {
            return back()->with('error', 'Unauthorized action.');
        }

        // Check if topic can be edited (only if status is 'available')
        if ($topic->status !== 'available') {
            return back()->with('error', 'Cannot edit topic that has been ' . $topic->status);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'research_area' => 'required|string|max:255',
        ]);

        try {
            $topic->update([
                'title' => $request->title,
                'description' => $request->description,
                'research_area' => $request->research_area
            ]);

            return back()->with('success', 'Topic updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update topic: ' . $e->getMessage());
        }
    }
} 