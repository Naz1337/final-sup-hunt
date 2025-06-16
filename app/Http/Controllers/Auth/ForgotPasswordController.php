<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Student;
use App\Models\Lecturer;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('auth.forgot-password-form');
    }

    public function sendTemporaryPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $student = Student::where('email', $request->email)->first();
        $lecturer = Lecturer::where('email', $request->email)->first();

        if (!$student && !$lecturer) {
            return back()->withErrors(['email' => 'No account found with that email.']);
        }

        $userModel = $student ?? $lecturer;

        $temporaryPassword = Str::random(8);
        $userModel->password = Hash::make($temporaryPassword);
        $userModel->save();

        Mail::send('emails.temp-password', [
            'name' => $userModel->name,
            'temporaryPassword' => $temporaryPassword,
        ], function ($message) use ($userModel) {
            $message->to($userModel->email)
                    ->subject('Your Temporary Password');
        });

        return back()->with('success', 'A temporary password has been sent to your email.');
    }
}

