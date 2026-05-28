<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ArtworkController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TosController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArtworkListController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\ThreeDController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\ContactController;
use App\Http\Middleware\AdminMiddleware;

/**
 * Public Routes
 */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/artworks', [ArtworkListController::class, 'index'])->name('artworks');
Route::get('/commission', [CommissionController::class, 'index'])->name('commission');
Route::get('/contact', function() {
    return view('contact');
})->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/3d', [ThreeDController::class, 'index'])->name('three_d');

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

    // Service Resource
    Route::resource('services', ServiceController::class);

    // TOS (Terms of Service)
    Route::get('/tos', [TosController::class, 'edit'])->name('tos.edit');
    Route::put('/tos', [TosController::class, 'update'])->name('tos.update');

    // Profile (Edit name & password)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/mark-as-read', [MessageController::class, 'markAsRead'])->name('messages.markAsRead');
    Route::post('/messages/mark-all-read', [MessageController::class, 'markAllAsRead'])->name('messages.markAllAsRead');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::get('/messages/{message}/download/{index}', [MessageController::class, 'download'])->name('messages.download');
});
