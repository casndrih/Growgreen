<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('vendor')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('User.orders.index', compact('orders'));
    }

    public function show(int $id)
    {
        $order = Order::with('vendor')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('User.orders.show', compact('order'));
    }
}
