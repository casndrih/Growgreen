<?php

namespace App\Http\Controllers;

use App\Models\FoodProduct;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $results = FoodProduct::query()
            ->where('name', 'like', '%' . trim($validated['q']) . '%')
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name', 'price', 'image', 'stock']);

        return response()->json([
            'data' => $results->map(fn (FoodProduct $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image_url,
                'url' => route('product.show', ['id' => $product->id]),
            ]),
        ]);
    }
}
