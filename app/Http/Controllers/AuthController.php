<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle the login request.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        // Attempt login using username instead of email
        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->is_admin) {
                return redirect()->intended(route('admin.dashboard'));
            }

            Auth::logout();
            return back()->withErrors(['username' => 'Akun tidak memiliki hak admin.']);
        }

        return back()->withErrors(['username' => 'Username atau kata sandi tidak valid.'])->onlyInput('username');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
