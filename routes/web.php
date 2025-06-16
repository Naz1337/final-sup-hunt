<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CoordinatorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\QuotaController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Auth\LecturerAuthController;
use App\Http\Controllers\Student\TopicController;
use App\Http\Controllers\Student\AppointmentController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Lecturer\DashboardController;
use App\Http\Controllers\Lecturer\LecturerTopicController;
use App\Http\Controllers\Lecturer\LecturerAppointmentController;
use App\Http\Controllers\Lecturer\LecturerProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Student\LecturerProfileController as StudentLecturerProfileController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::get('/coordinator/login', [CoordinatorController::class, 'showLoginForm'])->name('coordinator.login');
Route::post('/coordinator/login', [CoordinatorController::class, 'login']);
Route::post('/coordinator/logout', [CoordinatorController::class, 'logout'])->name('coordinator.logout');

// Protected coordinator routes
Route::middleware(['auth:coordinator'])->group(function () {
    Route::get('/coordinator/dashboard', [CoordinatorController::class, 'showDashboard'])->name('coordinator.dashboard');
    // Student Management Routes
    Route::get('/coordinator/students', [StudentController::class, 'index'])->name('coordinator.students.index');
    Route::get('/coordinator/students/{student}/edit', [StudentController::class, 'edit'])->name('coordinator.students.edit');
    Route::put('/coordinator/students/{student}', [StudentController::class, 'update'])->name('coordinator.students.update');
    Route::delete('/coordinator/students/{student}', [StudentController::class, 'destroy'])->name('coordinator.students.destroy');
});

// Student Management Routes
Route::middleware(['auth:coordinator'])->group(function () {
    Route::get('/students', [StudentController::class, 'index'])->name('coordinator.students.index');
    Route::post('/coordinator/students/import', [StudentController::class, 'importCSV'])
        ->name('coordinator.students.import');
    Route::get('/students/template', [StudentController::class, 'downloadTemplate'])->name('coordinator.students.template');
    Route::get('/students/report', [StudentController::class, 'generateReport'])->name('coordinator.students.report');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('coordinator.students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('coordinator.students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('coordinator.students.destroy');
});

// Lecturer Management Routes
Route::middleware(['auth:coordinator'])->group(function () {
    Route::get('/lecturers', [LecturerController::class, 'index'])->name('coordinator.lecturers.index');
    Route::post('/lecturers/import', [LecturerController::class, 'importCSV'])->name('coordinator.lecturers.import');
    Route::get('/lecturers/template', [LecturerController::class, 'downloadTemplate'])->name('coordinator.lecturers.template');
    Route::get('/lecturers/report', [LecturerController::class, 'generateReport'])->name('coordinator.lecturers.report');
    Route::delete('/lecturers/{lecturer}', [LecturerController::class, 'destroy'])->name('coordinator.lecturers.destroy');
    Route::get('/lecturers/{lecturer}/edit', [LecturerController::class, 'edit'])->name('coordinator.lecturers.edit');
    Route::put('/lecturers', [LecturerController::class, 'update'])->name('coordinator.lecturers.update');
});

// Quota Management Routes
Route::middleware(['auth:coordinator'])->group(function () {
    Route::get('/quotas', [QuotaController::class, 'index'])->name('coordinator.quotas.index');
    Route::post('/quotas', [QuotaController::class, 'store'])->name('coordinator.quotas.store');
    Route::put('/quotas/{quota}', [QuotaController::class, 'update'])->name('coordinator.quotas.update');
    Route::delete('/quotas/{quota}', [QuotaController::class, 'destroy'])->name('coordinator.quotas.destroy');
});

// Timeframe Management Routes
Route::middleware(['auth:coordinator'])->group(function () {
    Route::get('/coordinator/timeframe', [TaskController::class, 'index'])->name('coordinator.timeframe.index');
    Route::post('/coordinator/timeframe', [TaskController::class, 'store'])->name('coordinator.timeframe.store');
    Route::get('/coordinator/timeframe/{task}/edit', [TaskController::class, 'edit'])->name('coordinator.timeframe.edit');
    Route::put('/coordinator/timeframe/{task}', [TaskController::class, 'update'])->name('coordinator.timeframe.update');
    Route::delete('/coordinator/timeframe/{task}', [TaskController::class, 'destroy'])->name('coordinator.timeframe.destroy');
    Route::patch('/coordinator/timeframe/{task}/toggle-status', [TaskController::class, 'toggleStatus'])->name('coordinator.timeframe.toggle-status');
});

// Student Auth Routes
Route::get('/student/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
Route::post('/student/login', [StudentAuthController::class, 'login']);
Route::post('/student/logout', [StudentAuthController::class, 'logout'])->name('student.logout');

// Student Change Password Routes
Route::get('/student/change-password', [StudentAuthController::class, 'showChangePasswordForm'])
    ->name('student.change-password')
    ->middleware('auth:student');
Route::post('/student/change-password', [StudentAuthController::class, 'changePassword'])
    ->name('student.change-password.update')
    ->middleware('auth:student');

// Lecturer Auth Routes
Route::prefix('lecturer')->group(function () {
    Route::get('/login', [LecturerAuthController::class, 'showLoginForm'])->name('lecturer.login');
    Route::post('/login', [LecturerAuthController::class, 'login']);
    Route::post('/logout', [LecturerAuthController::class, 'logout'])->name('lecturer.logout');

    // Lecturer Change Password Routes
    Route::get('/change-password', [LecturerAuthController::class, 'showChangePasswordForm'])
        ->name('lecturer.change-password')
        ->middleware('auth:lecturer');
    Route::post('/change-password', [LecturerAuthController::class, 'changePassword'])
        ->name('lecturer.change-password.update')
        ->middleware('auth:lecturer');

    // Protected lecturer routes
    Route::middleware('auth:lecturer')->group(function () {
        Route::get('/dashboard', [LecturerController::class, 'dashboard'])->name('lecturer.dashboard');
        // Add other lecturer routes here

        // Appointment Routes
        Route::get('/lecturer/appointments', [LecturerAppointmentController::class, 'index'])->name('lecturer.appointment.index');
        Route::post('/lecturer/appointments', [LecturerAppointmentController::class, 'store'])->name('lecturer.appointment.store');
        Route::put('/lecturer/appointments/{appointment}', [LecturerAppointmentController::class, 'update'])->name('lecturer.appointment.update');
        Route::delete('/lecturer/appointments/{appointment}', [LecturerAppointmentController::class, 'destroy'])->name('lecturer.appointment.destroy');

        // Topic Routes
        Route::get('/lecturer/topic', [LecturerTopicController::class, 'index'])->name('lecturer.topic.index');
        Route::post('/lecturer/topic', [LecturerTopicController::class, 'store'])->name('lecturer.topic.store');
        Route::put('/lecturer/topic/{topic}', [LecturerTopicController::class, 'update'])->name('lecturer.topic.update');
        Route::delete('/lecturer/topic/{topic}', [LecturerTopicController::class, 'destroy'])->name('lecturer.topic.destroy');
    });
});

//Forgot Password Form Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('forgot.password.form');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendTemporaryPassword'])->name('forgot.password.send');

// Student Routes
Route::middleware(['auth:student'])->group(function () {
    Route::get('/student/dashboard', [StudentAuthController::class, 'dashboard'])->name('student.dashboard');
    // Add other student routes here later (topic, appointment, profile)

    // Topic Routes
    Route::get('/student/topics', [TopicController::class, 'index'])->name('student.topic.index');
    Route::post('/student/topics', [TopicController::class, 'store'])->name('student.topic.store');
    Route::put('/student/topics/{topic}', [TopicController::class, 'update'])->name('student.topic.update');
    Route::delete('/student/topics/{topic}', [TopicController::class, 'destroy'])->name('student.topic.destroy');
    Route::post('/student/topics/{topic}/apply', [TopicController::class, 'apply'])->name('student.topic.apply');

    // Appointment Routes
    Route::get('/student/appointments', [AppointmentController::class, 'index'])->name('student.appointment.index');
    Route::post('/student/appointments', [AppointmentController::class, 'store'])->name('student.appointment.store');
    Route::put('/student/appointments/{appointment}', [AppointmentController::class, 'update'])->name('student.appointment.update');
    Route::delete('/student/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('student.appointment.destroy');
    Route::post('/student/appointments/{appointment}/book', [AppointmentController::class, 'bookAppointment'])
        ->name('student.appointment.book');

    // Profile Routes
    Route::get('/student/profile', [ProfileController::class, 'index'])->name('student.profile.index');
    Route::put('/student/profile', [ProfileController::class, 'update'])->name('student.profile.update');

    // Lecturer Profile Routes
    Route::get('/student/lecturers', [StudentLecturerProfileController::class, 'index'])->name('student.lecturer.list');
    Route::get('/student/lecturers/{lecturer}', [StudentLecturerProfileController::class, 'show'])->name('student.lecturer.profile');
});

// Lecturer Routes
Route::middleware(['auth:lecturer'])->group(function () {
    Route::get('/lecturer/dashboard', [DashboardController::class, 'index'])->name('lecturer.dashboard');

    // Topic Routes
    Route::get('/lecturer/topic', [LecturerTopicController::class, 'index'])->name('lecturer.topic.index');
    Route::post('/lecturer/topic', [LecturerTopicController::class, 'store'])->name('lecturer.topic.store');
    Route::put('/lecturer/topic/{topic}', [LecturerTopicController::class, 'update'])->name('lecturer.topic.update');
    Route::delete('/lecturer/topic/{topic}', [LecturerTopicController::class, 'destroy'])->name('lecturer.topic.destroy');

    // Appointment Routes
    Route::get('/lecturer/appointments', [LecturerAppointmentController::class, 'index'])->name('lecturer.appointment.index');
    Route::post('/lecturer/appointments', [LecturerAppointmentController::class, 'store'])->name('lecturer.appointment.store');
    Route::put('/lecturer/appointments/{appointment}', [LecturerAppointmentController::class, 'update'])->name('lecturer.appointment.update');
    Route::delete('/lecturer/appointments/{appointment}', [LecturerAppointmentController::class, 'destroy'])->name('lecturer.appointment.destroy');

    // Profile Routes
    Route::get('/lecturer/profile', [LecturerProfileController::class, 'index'])->name('lecturer.profile.index');
    Route::put('/lecturer/profile', [LecturerProfileController::class, 'update'])->name('lecturer.profile.update');
});

// Coordinator routes with auth middleware
Route::middleware(['auth:coordinator'])->group(function () {
    // Student Management Routes
    Route::get('/coordinator/students', [StudentController::class, 'index'])->name('coordinator.students.index');
    Route::put('/coordinator/students/{student}', [StudentController::class, 'update'])->name('coordinator.students.update');

    // Lecturer Management Routes
    Route::get('/coordinator/lecturers', [LecturerController::class, 'index'])->name('coordinator.lecturers.index');
});

// Coordinator's lecturer management routes
Route::prefix('coordinator')->middleware(['auth:coordinator'])->group(function () {
    Route::get('/lecturers', [LecturerController::class, 'index'])->name('coordinator.lecturers.index');
    Route::post('/lecturers/import', [LecturerController::class, 'importCSV'])->name('coordinator.lecturers.import');
    Route::get('/lecturers/template', [LecturerController::class, 'downloadTemplate'])->name('coordinator.lecturers.template');
    // ... other coordinator routes
});

Route::middleware(['auth:coordinator'])->group(function () {
    Route::put('/coordinator/quotas/{quota}', [QuotaController::class, 'update'])->name('coordinator.quotas.update');
    // ... other quota routes
});

Route::middleware(['auth:coordinator'])->group(function () {
    Route::get('/lecturers/template/download', [LecturerController::class, 'downloadTemplate'])
        ->name('coordinator.lecturers.template.download');
});

Route::middleware(['auth:coordinator'])->group(function () {
    Route::post('/coordinator/students/import', [StudentController::class, 'importCSV'])
        ->name('coordinator.students.import');
    Route::get('/coordinator/students/template/download', [StudentController::class, 'downloadTemplate'])
        ->name('coordinator.students.template.download');
});

Route::middleware(['auth:coordinator'])->group(function () {
    Route::get('/coordinator/lecturers/report', [LecturerController::class, 'generateReport'])
        ->name('coordinator.lecturers.report');
});

Route::middleware(['auth:coordinator'])->group(function () {
    Route::resource('coordinator/timeframe', TaskController::class)
        ->names([
            'index' => 'coordinator.timeframe.index',
            'store' => 'coordinator.timeframe.store',
            'update' => 'coordinator.timeframe.update',
            'destroy' => 'coordinator.timeframe.destroy',
        ]);
});

Route::middleware(['auth:lecturer'])->group(function () {
    Route::resource('lecturer/topics', LecturerTopicController::class)
        ->names([
            'index' => 'lecturer.topics.index',
            'store' => 'lecturer.topics.store',
            'update' => 'lecturer.topics.update',
            'destroy' => 'lecturer.topics.destroy',
        ]);
});
