<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class LecturerProfileController extends Controller
{
    public function index()
    {
        $lecturer = Auth::guard('lecturer')->user();
        return view('lecturer.profile.index', compact('lecturer'));
    }

    public function update(Request $request)
    {
        $lecturer = Auth::guard('lecturer')->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'current_password' => 'required_with:password',
            'password' => 'nullable|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'expertise' => 'nullable|string',
            'teaching_experience' => 'nullable|string',
            'previous_fyp_titles' => 'nullable|string',
        ]);

        try {
            $data = ['name' => $request->name];
            
            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($lecturer->photo) {
                    Storage::disk('public')->delete($lecturer->photo);
                }
                
                // Store new photo
                $path = $request->file('photo')->store('lecturer-photos', 'public');
                $data['photo'] = $path;
            }

            // Handle other fields
            if ($request->filled('expertise')) {
                $data['expertise'] = $request->expertise;
            }
            
            if ($request->filled('teaching_experience')) {
                $data['teaching_experience'] = $request->teaching_experience;
            }
            
            if ($request->filled('previous_fyp_titles')) {
                $data['previous_fyp_titles'] = $request->previous_fyp_titles;
            }
            
            if ($request->filled('password')) {
                if (!Hash::check($request->current_password, $lecturer->password)) {
                    return back()->withErrors(['current_password' => 'Current password is incorrect']);
                }
                $data['password'] = Hash::make($request->password);
            }

            $lecturer->update($data);
            
            return back()->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }
} 