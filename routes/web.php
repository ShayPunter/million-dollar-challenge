<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\UserPageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/admin', AdminController::class)
    ->middleware(['auth', 'can:viewAdmin'])
    ->name('admin');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/user-page', [UserPageController::class, 'store'])->name('user-page.store');
    Route::put('/user-page', [UserPageController::class, 'update'])->name('user-page.update');

    Route::resource('projects', ProjectController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::post('/revenues', [RevenueController::class, 'store'])->name('revenues.store');
    Route::put('/revenues/{revenue}', [RevenueController::class, 'update'])->name('revenues.update');
    Route::delete('/revenues/{revenue}', [RevenueController::class, 'destroy'])->name('revenues.destroy');
});

Route::get('auth/google', [GoogleController::class, 'redirect'])
    ->name('google.login');

Route::get('auth/google/callback', [GoogleController::class, 'callback'])
    ->name('google.callback');

Route::get('/grid/{slug}', [UserPageController::class, 'show'])->name('public-page');

Route::get('/tos', function () {
    return Inertia::render('TOS');
});

require __DIR__.'/auth.php';
