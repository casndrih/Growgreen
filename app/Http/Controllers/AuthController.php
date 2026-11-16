<?php

namespace App\\Http\\Controllers;

use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        foreach (['admin', 'vendor', 'web'] as $guard) {
            if (Auth::guard($guard)->attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();

                return match ($guard) {
                    'admin' => redirect()->intended(route('admin.dashboard')),
                    'vendor' => redirect()->intended(route('vendor.dashboard')),
                    default => redirect()->intended(route('user.profile')),
                };
            }
        }

        return back()
            ->withErrors(['email' => 'The supplied credentials are invalid.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        foreach (['web', 'vendor', 'admin'] as $guard) {
            Auth::guard($guard)->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Successfully logged out.');
    }
}
