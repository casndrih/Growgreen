<?php

namespace App\Http\Controllers;

use App\Models\FoodProduct;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Request $request)
    {
        $productId = $request->query('id');
        $product = FoodProduct::findOrFail($productId);

        $vendor = Vendor::findOrFail($product->vendor_id);

        return view('Frontend.product.index', ['product' => $product, 'vendor' => $vendor]);
    }
}
