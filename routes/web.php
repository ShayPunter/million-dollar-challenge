<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserPageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\RevenueController;

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/user-page', [UserPageController::class, 'store'])->name('user-page.store');
    Route::put('/user-page', [UserPageController::class, 'update'])->name('user-page.update');


    Route::resource('projects', ProjectController::class);
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::post('/revenues', [RevenueController::class, 'store'])->name('revenues.store');
    Route::put('/revenues/{revenue}', [RevenueController::class, 'update'])->name('revenues.update');
    Route::delete('/revenues/{revenue}', [RevenueController::class, 'destroy'])->name('revenues.destroy');
});

Route::prefix('v1')->group(function () {
    Route::post('/challenges', [ChallengeController::class, 'store']);
    Route::get('/challenges/{id}', [ChallengeController::class, 'show']);
    Route::get('/challenges/{id}/progress', [ChallengeController::class, 'progress']);
    Route::post('/challenges/{id}/revenues', [RevenueController::class, 'store']);
    Route::get('/challenges/{id}/revenues', [RevenueController::class, 'index']);
});

Route::get('/{slug}', [UserPageController::class, 'show'])->name('public-page');


require __DIR__.'/auth.php';
