<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use App\Models\FoodProduct;
use App\Models\GroceryCategory;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function index()
    {
        $vendorId = auth('vendor')->id();
        $products = FoodProduct::with('category')->where('vendor_id', $vendorId)->latest()->paginate(12);
        $orders = Order::where('vendor_id', $vendorId)->latest()->limit(10)->get();
        $categories = GroceryCategory::orderBy('name')->get();
        $stats = [
            'products' => FoodProduct::where('vendor_id', $vendorId)->count(),
            'orders' => Order::where('vendor_id', $vendorId)->count(),
            'pending_orders' => Order::where('vendor_id', $vendorId)->whereIn('status', ['paid', 'processing', 'pending'])->count(),
        ];

        return view('Vendor.dashboard.index', compact('products', 'orders', 'categories', 'stats'));
    }

    public function store(Request $request)
    {
        return redirect()->route('vendor.dashboard')
            ->with('info', 'Your vendor account is already registered. Manage your products from this dashboard.');
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['nullable', 'integer', 'exists:grocery_categories,id'],
            'price' => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $image = $request->file('image')?->store('products', 'public');

        FoodProduct::create([
            'vendor_id' => auth('vendor')->id(),
            'category_id' => $validated['category_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'image' => $image,
        ]);

        return redirect()->route('vendor.dashboard')->with('success', 'Product created successfully.');
    }

    public function updateProduct(Request $request, FoodProduct $product)
    {
        abort_unless((int) $product->vendor_id === (int) auth('vendor')->id(), 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['nullable', 'integer', 'exists:grocery_categories,id'],
            'price' => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && ! filter_var($product->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('vendor.dashboard')->with('success', 'Product updated successfully.');
    }

    public function destroyProduct(FoodProduct $product)
    {
        abort_unless((int) $product->vendor_id === (int) auth('vendor')->id(), 404);

        if (Order::where('product_id', $product->id)->exists()) {
            return redirect()->route('vendor.dashboard')->with('error', 'This product has order history and cannot be deleted. Set its stock to zero instead.');
        }

        if ($product->image && ! filter_var($product->image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('vendor.dashboard')->with('success', 'Product deleted successfully.');
    }
}
