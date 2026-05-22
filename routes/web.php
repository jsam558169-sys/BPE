<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Faculty\BorrowController;
// ---------------------------------------------------------------
// Welcome / landing
// ---------------------------------------------------------------
Route::get('/', function () {
    return redirect()->route('login');
});

// ---------------------------------------------------------------
// Generic /dashboard redirect based on who is logged in
// ---------------------------------------------------------------
Route::get('/dashboard', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    if (Auth::guard('borrower')->check()) {
        return redirect()->route('faculty.borrow.index');
    }
    return redirect()->route('login'); // neither logged in
})->name('dashboard');

// ---------------------------------------------------------------
// ADMIN ROUTES
// ---------------------------------------------------------------
Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth:admin')          // <-- uses the admin guard
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Equipment CRUD
        Route::resource('equipment', EquipmentController::class);

        // Return action
        Route::post('/return/{id}', [DashboardController::class, 'markAsReturned'])->name('return');

        // Transaction History page
        Route::get('/history', [DashboardController::class, 'history'])->name('history.index');

        // History delete
        Route::delete('/history/{id}', [DashboardController::class, 'destroyHistory'])->name('history.destroy');

        // Equipment Categories
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)
            ->only(['index', 'store', 'update', 'destroy'])
            ->names('categories');

        // Borrower (user) management
        Route::resource('users', UserController::class);
        Route::patch('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
    });

// ---------------------------------------------------------------
// FACULTY / BORROWER ROUTES
// ---------------------------------------------------------------
Route::prefix('faculty')
    ->name('faculty.')
    ->middleware('auth:borrower')       // <-- uses the borrower guard
    ->group(function () {

        Route::get('/borrow',        [BorrowController::class, 'index'])->name('borrow.index');
        Route::get('/borrow/create', [BorrowController::class, 'create'])->name('borrow.create');
        Route::post('/borrow',       [BorrowController::class, 'store'])->name('borrow.store');
        Route::get('/history',       [BorrowController::class, 'history'])->name('history');
    });

// ---------------------------------------------------------------
// AUTH ROUTES (separate for each guard)
// See: routes/auth.php — split into admin_auth.php + borrower_auth.php
// ---------------------------------------------------------------
require __DIR__ . '/auth_borrower.php';
require __DIR__ . '/auth_admin.php';
