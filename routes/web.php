<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::prefix('app')->group(function () {
    Route::inertia('/login', 'app/Login')->name('app.login');
    Route::inertia('/register', 'app/Register')->name('app.register');
    Route::inertia('/courses', 'app/Courses')->name('app.courses');
    Route::inertia('/appointments', 'app/Appointments')->name('app.appointments');
});

require __DIR__.'/settings.php';
