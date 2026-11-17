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

        $query = FoodProduct::query()
            ->with(['category', 'vendor'])
            ->where('stock', '>', 0)
            ->where(function ($builder) use ($pattern) {
                $builder->where('name', 'like', $pattern)
                    ->orWhereHas('category', fn ($category) => $category->where('name', 'like', $pattern))
                    ->orWhereHas('vendor', fn ($vendor) => $vendor->where('name', 'like', $pattern));
            })
            ->orderBy('name');

        if ($request->expectsJson() === false) {
            $products = $query->paginate(16)->withQueryString();

            return view('Frontend.search.index', compact('products', 'term'));
        }


        return response()->json([
            'data' => $query->limit(12)->get()->map(fn (FoodProduct $product) => [
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
