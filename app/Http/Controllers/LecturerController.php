<?php

namespace App\Http\Controllers;

use App\Models\Lecturer;
use App\Models\Topic;
use App\Models\Quota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class LecturerController extends Controller
{
    public function index()
    {
        $lecturers = Lecturer::orderBy('name')->paginate(10);
        return view('coordinator.lecturers.index', compact('lecturers'));
    }

    public function importCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        try {
            DB::beginTransaction();
            
            $file = $request->file('csv_file');
            $handle = fopen($file, 'r');
            
            // Skip header row
            $header = fgetcsv($handle);
            
            $imported = 0;
            $errors = [];
            
            while (($data = fgetcsv($handle)) !== false) {
                try {
                    $lecturer = Lecturer::create([
                        'staff_id' => $data[0],
                        'name' => $data[1],
                        'email' => $data[2],
                        'research_group' => $data[3],
                        'password' => Hash::make('password123'),
                        'is_first_login' => true
                    ]);

                    // Create default quota
                    Quota::create([
                        'lecturer_id' => $lecturer->id,
                        'max_supervisees' => 5,
                        'current_supervisees' => 0
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Error on row $imported: " . $e->getMessage();
                }
            }

            fclose($handle);
            
            if (empty($errors)) {
                DB::commit();
                return back()->with('success', "$imported lecturers imported successfully");
            } else {
                DB::rollBack();
                return back()->with('error', implode("\n", $errors));
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function dashboard()
    {
        $lecturer = Auth::guard('lecturer')->user();
        $pendingTopicsCount = Topic::where('lecturer_id', $lecturer->id)
                                 ->where('status', 'pending')
                                 ->count();
        $totalStudents = Topic::where('lecturer_id', $lecturer->id)
                             ->where('status', 'approved')
                             ->count();

        return view('lecturer.dashboard', compact('pendingTopicsCount', 'totalStudents'));
    }

    public function generateReport()
    {
        $lecturers = Lecturer::with(['quota', 'topics'])
            ->orderBy('name')
            ->get();
            
        return view('coordinator.lecturers.report', compact('lecturers'));
    }

    public function downloadTemplate()
    {
        $filePath = public_path('templates/lecturers_template.csv');
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Template file not found.');
        }

        return Response::download($filePath, 'lecturers_template.csv', [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=lecturers_template.csv'
        ]);
    }

    public function destroy(Lecturer $lecturer)
    {
        try {
            // Check if lecturer has any active supervisees
            if ($lecturer->topics()->where('status', 'approved')->exists()) {
                return back()->with('error', 'Cannot delete lecturer with active supervisees.');
            }

            // Delete the lecturer's quota if it exists
            if ($lecturer->quota) {
                $lecturer->quota->delete();
            }

            // Delete the lecturer
            $lecturer->delete();

            return back()->with('success', 'Lecturer deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete lecturer.');
        }
    }

    // ... other management methods ...
}