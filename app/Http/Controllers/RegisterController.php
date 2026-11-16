<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('vendors', 'email'),
                Rule::unique('admins', 'email'),
            ],
            'password' => ['required', 'string', 'min:8', 'same:confirm_password'],
            'confirm_password' => ['required', 'string'],
            'role' => ['required', Rule::in(['user', 'vendor'])],
            'terms' => ['required', 'accepted'],
        ]);

        $attributes = [
            'name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ];

        if ($validated['role'] === 'vendor') {
            $account = Vendor::create($attributes);
            $guard = 'vendor';
        } else {
            $account = User::create($attributes);
            $guard = 'web';
        }

        Auth::guard($guard)->login($account);
        $request->session()->regenerate();

        return $guard === 'vendor'
            ? redirect()->route('vendor.dashboard')->with('success', 'Your vendor account has been created.')
            : redirect()->route('user.profile')->with('success', 'Your account has been created.');
    }
}
