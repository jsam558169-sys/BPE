<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        // 1. Try admin guard first
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            // Always go to admin dashboard — no intended() to avoid stale URLs
            return redirect()->route('admin.dashboard');
        }

        // 2. Try borrower guard
        if (Auth::guard('borrower')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            // Always go to faculty page — no intended()
            return redirect()->route('faculty.borrow.index');
        }

        // 3. Neither matched
        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        Auth::guard('borrower')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
