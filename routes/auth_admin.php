<?php

use App\Http\Controllers\Auth\Admin\AdminAuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------
// ADMIN AUTH ROUTES
// ---------------------------------------------------------------

// Admin Login (separate URL so it doesn't clash with borrower login)
Route::get('/admin/login', [AdminAuthenticatedSessionController::class, 'create'])
    ->middleware('guest:admin')
    ->name('admin.login');

Route::post('/admin/login', [AdminAuthenticatedSessionController::class, 'store'])
    ->middleware('guest:admin');

// Admin Logout
Route::post('/admin/logout', [AdminAuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:admin')
    ->name('admin.logout');
