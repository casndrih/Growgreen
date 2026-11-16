<?php

namespace App\\Http\\Controllers\\Users;

use App\\Http\\Controllers\\Controller;
use App\\Models\\Order;
use App\\Models\\OrderHistory;
use App\\Models\\User;
use App\\Models\\Vendor;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $orders = Order::where('user_id', $user->id)
            ->with('user', 'vendor')
            ->latest()
            ->paginate(10);

        $order_history = OrderHistory::where('user_id', $user->id)
            ->with('user', 'vendor')
            ->latest()
            ->paginate(10);

        $vendorIds = $orders->getCollection()->pluck('vendor_id')
            ->merge($order_history->getCollection()->pluck('vendor_id'))
            ->unique();

        $vendors = Vendor::whereIn('id', $vendorIds)->get();

        return view('User.profile.index', compact('user', 'orders', 'order_history', 'vendors'));
    }

    public function edit(int $id)
    {
        abort_unless($id === Auth::id(), 403);

        return response()->json([User::findOrFail($id)]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'edit_user_name' => ['required', 'string', 'max:255'],
            'edit_user_gender' => ['nullable', 'string', 'max:40'],
            'edit_user_birthday' => ['nullable', 'date'],
            'edit_user_email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'edit_user_phone' => ['nullable', 'string', 'max:40'],
            'edit_user_city' => ['nullable', 'string', 'max:120'],
            'edit_user_state' => ['nullable', 'string', 'max:120'],
            'edit_user_address' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->update([
            'name' => $validated['edit_user_name'],
            'gender' => $validated['edit_user_gender'] ?? null,
            'birthday' => $validated['edit_user_birthday'] ?? null,
            'email' => $validated['edit_user_email'],
            'phone' => $validated['edit_user_phone'] ?? null,
            'city' => $validated['edit_user_city'] ?? null,
            'state' => $validated['edit_user_state'] ?? null,
            'home_delivery_address' => $validated['edit_user_address'] ?? null,
        ]);

        return redirect()->route('user.profile')->with('success', 'Profile successfully updated.');
    }
}
