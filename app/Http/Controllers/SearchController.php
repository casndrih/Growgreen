<?php

namespace App\Http\Controllers;

use App\Models\FoodProduct;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $validated = $request->validate(['q' => ['required', 'string', 'min:2', 'max:100']]);
        $term = trim($validated['q']);
        $pattern = '%'.str_replace(['%', '_'], ['\\%', '\\_'], $term).'%';

        $results = FoodProduct::query()
            ->with('category')
            ->where('stock', '>', 0)
            ->where(function ($query) use ($pattern) {
                $query->where('name', 'like', $pattern)
                    ->orWhereHas('category', fn ($category) => $category->where('name', 'like', $pattern));
            })
            ->orderBy('name')
            ->limit(12)
            ->get();

        return response()->json([
            'data' => $results->map(fn (FoodProduct $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image_url,
                'url' => route('product.show', ['id' => $product->id]),
                'category' => $product->category?->name,
            ]),
        ]);
    }
}
