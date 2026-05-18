<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------
// BORROWER (Faculty) AUTH ROUTES
// ---------------------------------------------------------------

// Login
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest:borrower')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest:borrower');

// Logout
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:borrower')
    ->name('logout');

// Registration (borrowers register themselves)
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest:borrower')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest:borrower');

// Password reset
Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest:borrower')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest:borrower')
    ->name('password.email');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest:borrower')
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest:borrower')
    ->name('password.store');
