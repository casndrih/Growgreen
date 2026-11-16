<?php

namespace App\\Http\\Controllers;

use App\\Models\\Cart;
use App\\Models\\FoodProduct;
use Illuminate\\Http\\Request;
use Illuminate\\Support\\Facades\\DB;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('product')
            ->where('user_id', auth()->id())
            ->get();

        return view('Users.cart.index', compact('cartItems'));
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:food_products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $result = DB::transaction(function () use ($validated) {
            $product = FoodProduct::query()->lockForUpdate()->findOrFail($validated['product_id']);

            if ((int) $product->stock < (int) $validated['quantity']) {
                return false;
            }

            $item = Cart::query()
                ->where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            $newQuantity = ($item?->quantity ?? 0) + (int) $validated['quantity'];

            if ($newQuantity > (int) $product->stock) {
                return false;
            }

            if ($item) {
                $item->update(['quantity' => $newQuantity]);
            } else {
                Cart::create([
                    'user_id' => auth()->id(),
                    'product_id' => $product->id,
                    'quantity' => $newQuantity,
                ]);
            }

            return true;
        });

        if (! $result) {
            return response()->json([
                'success' => false,
                'message' => 'The requested quantity is not available in stock.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully.',
        ]);
    }

    public function removeFromCart(Request $request, int $productId)
    {
        Cart::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart successfully.',
        ]);
    }
}
