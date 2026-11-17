<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodProduct;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;

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
            'payments_requiring_refund' => Payment::where('status', 'requires_refund')->count(),
        ];

        $recentOrders = Order::with(['user', 'vendor'])->latest()->limit(10)->get();
        $recentVendors = Vendor::latest()->limit(8)->get();
        $recentPayments = Payment::with('user')->latest()->limit(10)->get();

        return view('Admin.dashboard.index', compact('stats', 'recentOrders', 'recentVendors', 'recentPayments'));
    }

    public function users(Request $request)
    {
        $query = User::query()->latest();
        if ($search = trim((string) $request->query('q'))) {
            $term = '%'.mb_strtolower($search).'%';
            $query->where(fn ($builder) => $builder
                ->whereRaw('LOWER(name) LIKE ?', [$term])
                ->orWhereRaw('LOWER(email) LIKE ?', [$term]));
        }

        $users = $query->paginate(15)->withQueryString();

        return view('Admin.users.index', compact('users'));
    }

    public function vendors(Request $request)
    {
        $query = Vendor::query()->latest();
        if ($search = trim((string) $request->query('q'))) {
            $term = '%'.mb_strtolower($search).'%';
            $query->where(fn ($builder) => $builder
                ->whereRaw('LOWER(name) LIKE ?', [$term])
                ->orWhereRaw('LOWER(email) LIKE ?', [$term]));
        }

        $vendors = $query->paginate(15)->withQueryString();

        return view('Admin.vendors.index', compact('vendors'));
    }
}
