<?php

use App\Http\Controllers\Students\StudentController;
use App\Http\Controllers\Guardians\GuardianController;
use Illuminate\Support\Facades\Route;

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
});
