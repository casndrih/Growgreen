<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('Auth.login');
    }

    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $broker = $this->passwordBrokerFor($validated['email']);
        $status = Password::broker($broker)->sendResetLink(['email' => $validated['email']]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'If an account exists for that email, a password reset link has been sent.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('Auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        $broker = $this->passwordBrokerFor($validated['email']);
        $status = Password::broker($broker)->reset($validated, function ($user) use ($validated) {
            $user->forceFill([
                'password' => Hash::make($validated['password']),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        });

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Your password has been reset. You can now sign in.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    protected function passwordBrokerFor(string $email): string
    {
        if (DB::table('admins')->where('email', $email)->exists()) {
            return 'admins';
        }

        if (DB::table('vendors')->where('email', $email)->exists()) {
            return 'vendors';
        }

        return 'users';
    }

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
