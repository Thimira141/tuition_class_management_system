<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', fn () => view('web.home'))->middleware('guest')->name('web-home');
