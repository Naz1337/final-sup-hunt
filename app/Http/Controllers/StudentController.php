<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    // Add program mapping constant
    private const PROGRAM_MAPPING = [
        'CB' => 'Software Engineering',
        'CA' => 'Computer System & Networking',
        'CD' => 'Computer Graphics & Multimedia',
        'CF' => 'Cybersecurity'
    ];

    // Helper function to determine program from matric ID
    private function getProgramFromMatricId($matricId)
    {
        $prefix = strtoupper(substr($matricId, 0, 2));
        return self::PROGRAM_MAPPING[$prefix] ?? null;
    }

    public function index()
    {
        $students = Student::orderBy('name')->paginate(10);
        return view('coordinator.students.index', compact('students'));
    }

    public function importCSV(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        try {
            DB::beginTransaction();
            
            $file = $request->file('file');
            $handle = fopen($file, 'r');
            
            // Skip the header row
            $header = fgetcsv($handle);
            
            $importCount = 0;
            $errors = [];
            $line = 2; // Start from line 2 as line 1 is header

            while (($data = fgetcsv($handle)) !== false) {
                try {
                    if (count($data) < 3) {
                        $errors[] = "Line {$line}: Missing required fields";
                        continue;
                    }

                    [$matric_id, $name, $email] = $data;

                    // Validate data
                    if (empty($matric_id) || empty($name) || empty($email)) {
                        $errors[] = "Line {$line}: All fields are required";
                        continue;
                    }

                    // Get program based on matric ID prefix
                    $program = $this->getProgramFromMatricId($matric_id);
                    if (!$program) {
                        $errors[] = "Line {$line}: Invalid matric ID prefix. Must be CB, CA, CD, or CF";
                        continue;
                    }

                    // Check if student already exists
                    if (Student::where('matric_id', $matric_id)->exists()) {
                        $errors[] = "Line {$line}: Student with matric ID {$matric_id} already exists";
                        continue;
                    }

                    if (Student::where('email', $email)->exists()) {
                        $errors[] = "Line {$line}: Student with email {$email} already exists";
                        continue;
                    }

                    // Create new student with program
                    Student::create([
                        'matric_id' => $matric_id,
                        'name' => $name,
                        'email' => $email,
                        'program' => $program,
                        'password' => Hash::make($matric_id), // Use matric_id as default password
                        'is_first_login' => true,
                    ]);

                    $importCount++;

                } catch (\Exception $e) {
                    $errors[] = "Line {$line}: " . $e->getMessage();
                }

                $line++;
            }

            fclose($handle);
            DB::commit();

            $message = "{$importCount} students imported successfully.";
            if (!empty($errors)) {
                $message .= " However, there were some errors:";
                return back()->with([
                    'warning' => $message,
                    'importErrors' => $errors
                ]);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $filePath = public_path('templates/students_template.csv');
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Template file not found.');
        }

        return response()->download($filePath, 'students_template.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=students_template.csv'
        ]);
    }

    public function generateReport()
    {
        $students = Student::with('topics')->get();
        return view('coordinator.students.report', compact('students'));
    }

    public function edit(Student $student)
    {
        return view('coordinator.students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'matric_id' => 'required|string|unique:students,matric_id,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8'
        ]);

        try {
            if ($request->filled('password')) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $student->update($validated);
            return redirect()->route('coordinator.students.index')->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update student: ' . $e->getMessage());
        }
    }

    public function destroy(Student $student)
    {
        try {
            $student->delete();
            return back()->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'matric_id' => 'required|string|unique:students',
            'name' => 'required|string',
            'email' => 'required|email|unique:students',
        ]);

        // Get program based on matric ID prefix
        $program = $this->getProgramFromMatricId($request->matric_id);
        if (!$program) {
            return back()->with('error', 'Invalid matric ID prefix. Must be CB, CA, CD, or CF');
        }

        try {
            Student::create([
                'matric_id' => $request->matric_id,
                'name' => $request->name,
                'email' => $request->email,
                'program' => $program,
                'password' => Hash::make($request->matric_id),
                'is_first_login' => true
            ]);

            return back()->with('success', 'Student created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create student: ' . $e->getMessage());
        }
    }
} 