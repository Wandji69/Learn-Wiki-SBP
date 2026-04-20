<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::inertia('/dashboard', 'admin/Dashboard')->name('admin.dashboard');
        Route::inertia('/courses', 'admin/Courses')->name('admin.courses');
        Route::inertia('/appointments', 'admin/Appointments')->name('admin.appointments');
    });

    Route::prefix('user')->group(function () {
        Route::inertia('/dashboard', 'user/Dashboard')->name('user.dashboard');
        Route::inertia('/courses', 'user/Courses')->name('user.courses');
        Route::inertia('/appointments', 'user/Appointments')->name('user.appointments');
    });
});

Route::prefix('app')->group(function () {
    Route::inertia('/login', 'app/Login')->name('app.login');
    Route::inertia('/register', 'app/Register')->name('app.register');
});

require __DIR__.'/settings.php';
