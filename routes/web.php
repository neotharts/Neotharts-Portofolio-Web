<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ArtworkController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VisitorController;
use App\Http\Middleware\AdminMiddleware;

/**
 * Public Routes
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::post('/api/visitor/track', [VisitorController::class, 'track'])->name('visitor.track');

/**
 * Admin Routes
 * Hanya admin yang bisa akses
 */
Route::prefix('admin')->name('admin.')->middleware([AdminMiddleware::class])->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Artwork Resource
    Route::resource('artworks', ArtworkController::class);
});
