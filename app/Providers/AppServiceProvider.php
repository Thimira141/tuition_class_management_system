<?php

namespace App\Providers;

use App\Models\AttendanceSession;
use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // Bind classroom by class_code and include trashed
        Route::bind('classroom', function ($value) {
            $classroom = new Classroom;
            return Classroom::withTrashed()->where($classroom->getRouteKeyName(), $value)->firstOrFail();
        });
        // Bind student by student_code and include trashed
        Route::bind('student', function ($value) {
            $student = new Student;
            return Student::withTrashed()->where($student->getRouteKeyName(), $value)->firstOrFail();
        });
        // Bind attendance_session by attendance_session_code and include trashed
        Route::bind('attendance_session', function ($value) {
            $attendance_session = new AttendanceSession;
            return AttendanceSession::withTrashed()->where($attendance_session->getRouteKeyName(), $value)->firstOrFail();
        });
    }
}
