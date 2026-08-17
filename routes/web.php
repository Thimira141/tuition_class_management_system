<?php

use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Guardians\GuardianController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Classrooms;
use App\Http\Controllers\AttendanceSessions;

require __DIR__.'/auth.php';

Route::get('/', fn () => view('web.home'))->middleware('guest')->name('web-home');
Route::get('/login', fn () => view('auth.login'))->middleware('guest')->name('login-page');

// middleware check, limit access only for admin
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('admin-dashboard');

    Route::prefix('students')->group(function () {
        Route::get('/', fn () => view('admin.students.index'))->name('admin-students');
        Route::prefix('ajax')->group(function() {
            Route::get('/dt-index', [StudentController::class, 'index'])->name('students.ajax.dt.index');
            Route::get('/show/{student_code}', [StudentController::class, 'show'])->name('students.ajax.show');
            Route::post('/store', [StudentController::class, 'store'])->name('students.ajax.store');
            Route::put('/update/{student_code}', [StudentController::class, 'update'])->name('students.ajax.update');
            Route::delete('/destroy/{student_code}', [StudentController::class, 'destroy'])->name('students.ajax.destroy');
            Route::get('/guardians_index_ts', [StudentController::class, 'AJAX_GUARDIANS_INDEX_TS'])->name('students.ajax.ts.guardians-index');
        });
    });

    Route::prefix('guardians')->group(function () {
        Route::get('/', fn () => view('admin.guardians.index'))->name('admin-guardians');
        Route::prefix('ajax')->group(function () {
            Route::get('/dt-index', [GuardianController::class, 'index'])->name('guardians.ajax.dt.index');
            Route::get('/show/{guardian_code}', [GuardianController::class, 'show'])->name('guardians.ajax.show');
            Route::post('/store', [GuardianController::class, 'store'])->name('guardians.ajax.store');
            Route::put('/update/{guardian_code}', [GuardianController::class, 'update'])->name('guardians.ajax.update');
            Route::delete('/destroy/{guardian_code}', [GuardianController::class, 'destroy'])->name('guardians.ajax.destroy');
        });
    });

    Route::prefix('classrooms')->group(function () {
        Route::get('/', fn () => view('admin.classrooms.index'))->name('admin-classrooms');
        Route::prefix('ajax')->group(function () {
            Route::get('/dt-index', [Classrooms\ClassroomController::class, 'index'])->name('classrooms.ajax.dt.index');
            Route::get('/show/{classroom}', [Classrooms\ClassroomController::class, 'show'])->name('classrooms.ajax.show');
            Route::post('/store', [Classrooms\ClassroomController::class, 'store'])->name('classrooms.ajax.store');
            Route::put('/update/{classroom}', [Classrooms\ClassroomController::class, 'update'])->name('classrooms.ajax.update');
            Route::delete('/destroy/{classroom}', [Classrooms\ClassroomController::class, 'destroy'])->name('classrooms.ajax.destroy');
            // ClassroomStudent
            Route::prefix('{classroom}/students')->group(function () {
                Route::get('/dt-index', [Classrooms\ClassroomStudentController::class, 'index'])->name('classrooms.student.ajax.dt.index');
                Route::post('/attach', [Classrooms\ClassroomStudentController::class, 'attach'])->name('classrooms.student.attach');
                Route::post('/detach', [Classrooms\ClassroomStudentController::class, 'detach'])->name('classrooms.student.detach');
            });
        });
    });

    Route::prefix('sessions')->group(function () {
        Route::get('/', fn () => view('admin.sessions.index'))->name('admin-sessions');
        Route::prefix('ajax')->group(function () {
            Route::get('/dt-index', [AttendanceSessions\AttendanceSessionsController::class, 'index'])->name('attendance_sessions.ajax.dt.index');
            Route::get('/show/{attendance_session}', [AttendanceSessions\AttendanceSessionsController::class, 'show'])->name('attendance_sessions.ajax.show');
            Route::post('/store', [AttendanceSessions\AttendanceSessionsController::class, 'store'])->name('attendance_sessions.ajax.store');
            Route::put('/update/{attendance_session}', [AttendanceSessions\AttendanceSessionsController::class, 'update'])->name('attendance_sessions.ajax.update');
            Route::delete('/destroy/{attendance_session}', [AttendanceSessions\AttendanceSessionsController::class, 'destroy'])->name('attendance_sessions.ajax.destroy');

            Route::prefix('{attendance_session}/attendance/{student}')->group(function () {
                Route::get('/show', [AttendanceSessions\StudentsAttendanceController::class, 'show'])->name('attendance.student.show');
                Route::post('/mark', [AttendanceSessions\StudentsAttendanceController::class, 'markStudentAttendance'])->name('attendance.student.mark');
                Route::post('/unmark', [AttendanceSessions\StudentsAttendanceController::class, 'unmarkStudentAttendance'])->name('attendance.student.unmark');
            });
        });
    });
});
