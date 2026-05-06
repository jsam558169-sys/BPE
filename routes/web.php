<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Faculty\BorrowController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', function () {
    return view('welcome');
});

// THE TRAFFIC COP: This redirects the generic /dashboard to the right place
Route::get('/dashboard', function () {
    if (Auth::user()->role_id == 1) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('faculty.borrow.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ADMIN ROUTES (Grouped for cleanliness)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('equipment', EquipmentController::class);
        Route::post('/return/{id}', [DashboardController::class, 'markAsReturned'])->name('return');
        Route::resource('users', UserController::class); // this alone handles index, create, store, edit, update, destroy
        Route::delete('/history/{id}', [DashboardController::class, 'destroyHistory'])->name('history.destroy');
    });

    // FACULTY ROUTES (Cleaned Up)
    Route::prefix('faculty')->name('faculty.')->group(function () {
        // This becomes 'faculty.borrow'
        Route::get('/borrow', [BorrowController::class, 'index'])->name('borrow.index');


        // This becomes 'faculty.borrow.store'
        Route::post('/borrow', [BorrowController::class, 'store'])->name('borrow.store');

        // This becomes 'faculty.history'
        Route::get('/history', [BorrowController::class, 'history'])->name('history');

        Route::get('/borrow/create', [BorrowController::class, 'create'])->name('borrow.create');
        Route::post('/borrow/store', [BorrowController::class, 'store'])->name('borrow.store');
    });
});

require __DIR__ . '/auth.php';
