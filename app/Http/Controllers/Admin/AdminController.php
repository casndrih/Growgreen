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

    public function products(Request $request)
    {
        $query = FoodProduct::with(['vendor', 'category'])->latest();
        if ($search = trim((string) $request->query('q'))) {
            $term = '%'.mb_strtolower($search).'%';
            $query->where(fn ($builder) => $builder
                ->whereRaw('LOWER(name) LIKE ?', [$term])
                ->orWhereHas('vendor', fn ($vendor) => $vendor->whereRaw('LOWER(name) LIKE ?', [$term]))
                ->orWhereHas('category', fn ($category) => $category->whereRaw('LOWER(name) LIKE ?', [$term])));
        }

        $products = $query->paginate(20)->withQueryString();

        return view('Admin.products.index', compact('products'));
    }

    public function orders(Request $request)
    {
        $query = Order::with(['user', 'vendor'])->latest();
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('Admin.orders.index', compact('orders'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $validated = $request->validate(['status' => ['required', 'in:processing,shipped']]);
        $allowed = ['paid' => ['processing'], 'processing' => ['shipped']];

        if (! in_array($validated['status'], $allowed[$order->status] ?? [], true)) {
            return back()->with('error', 'That order status transition is not allowed.');
        }

        $order->update(['status' => $validated['status']]);

        return back()->with('success', 'Order status updated.');
    }

    public function updateVendor(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,closed'],
            'verification' => ['required', 'in:pending,approved,rejected'],
        ]);

        $vendor->update($validated);

        return back()->with('success', 'Vendor status updated.');
    }

    public function settings()
    {
        return view('Admin.settings.index');
    }

    public function support()
    {
        return view('Admin.support.index');
    }
}
