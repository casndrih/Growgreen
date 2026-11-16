<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodProduct;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\User;
use App\Models\Vendor;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'customers' => User::count(),
            'vendors' => Vendor::count(),
            'products' => FoodProduct::count(),
            'active_orders' => Order::count(),
            'completed_orders' => OrderHistory::count(),
        ];

        $recentOrders = Order::with(['user', 'vendor'])->latest()->limit(10)->get();
        $recentVendors = Vendor::latest()->limit(8)->get();

        return view('Admin.dashboard.index', compact('stats', 'recentOrders', 'recentVendors'));
    }
}
