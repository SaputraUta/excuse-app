<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('leave-requests', LeaveRequestController::class);

    Route::middleware(AdminMiddleware::class)->group(function () {
        Route::resource('approvals', ApprovalController::class);

        Route::get('/admin/approvals/{id}/details', [ApprovalController::class, 'showApprovalDetails'])
            ->name('approvals.details');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
