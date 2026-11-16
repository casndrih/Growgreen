<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('Auth.login');
    }

    public function sendResetLink(Request $request)\n    {\n        $validated = $request->validate(['email' => ['required', 'email']]);\n        $broker = $this->passwordBrokerFor($validated['email']);\n        $status = Password::broker($broker)->sendResetLink(['email' => $validated['email']]);\n\n        if ($status === Password::RESET_LINK_SENT) {\n            return back()->with('success', 'If an account exists for that email, a password reset link has been sent.');\n        }\n\n        return back()->withErrors(['email' => __($status)]);\n    }\n\n    public function showResetForm(Request $request, string $token)\n    {\n        return view('Auth.reset-password', [\n            'token' => $token,\n            'email' => $request->query('email'),\n        ]);\n    }\n\n    public function resetPassword(Request $request)\n    {\n        $validated = $request->validate([\n            'token' => ['required'],\n            'email' => ['required', 'email'],\n            'password' => ['required', 'string', 'min:8', 'confirmed'],\n        ]);\n\n        $broker = $this->passwordBrokerFor($validated['email']);\n        $status = Password::broker($broker)->reset($validated, function ($user) use ($validated) {\n            $user->forceFill([\n                'password' => Hash::make($validated['password']),\n                'remember_token' => Str::random(60),\n            ])->save();\n\n            event(new PasswordReset($user));\n        });\n\n        if ($status === Password::PASSWORD_RESET) {\n            return redirect()->route('login')->with('success', 'Your password has been reset. You can now sign in.');\n        }\n\n        return back()->withErrors(['email' => __($status)]);\n    }\n\n    protected function passwordBrokerFor(string $email): string\n    {\n        if (DB::table('admins')->where('email', $email)->exists()) {\n            return 'admins';\n        }\n\n        if (DB::table('vendors')->where('email', $email)->exists()) {\n            return 'vendors';\n        }\n\n        return 'users';\n    }\n\n    public function login(Request $request)
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
