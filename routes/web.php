<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', fn () => view('web.home'))->middleware('guest')->name('web-home');
Route::get('/login', fn () => view('auth.login'))->middleware('guest')->name('login-page');

// middleware check, limit access only for admin
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('admin-dashboard');

    Route::prefix('students')->group(function () {
        Route::get('/', fn () => view('admin.students.index'))->name('admin-students');
    });
});
