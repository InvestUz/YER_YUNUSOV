<?php
// File: app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use App\Models\LotView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('monitoring.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is inactive. Please contact administrator.',
                ])->withInput();
            }

            // Track login
            $this->trackLogin($request, $user);

            return redirect()->intended(route('monitoring.index'))
                ->with('success', 'Welcome back, ' . $user->name);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Track logout
            $this->trackLogout($request, $user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }

    private function trackLogin(Request $request, $user)
    {
        $userAgent = $request->userAgent();
        $parsed = LotView::parseUserAgent($userAgent);

        LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'device' => $parsed['device'],
            'browser' => $parsed['browser'],
            'platform' => $parsed['platform'],
            'login_at' => now(),
            'session_id' => session()->getId(),
            'status' => LoginHistory::STATUS_ACTIVE
        ]);
    }

    private function trackLogout(Request $request, $user)
    {
        $sessionId = session()->getId();

        $loginHistory = LoginHistory::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->where('status', LoginHistory::STATUS_ACTIVE)
            ->first();

        if ($loginHistory) {
            $loginHistory->update([
                'logout_at' => now(),
                'status' => LoginHistory::STATUS_LOGGED_OUT
            ]);
        }
    }
}