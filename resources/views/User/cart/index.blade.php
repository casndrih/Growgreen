@extends('app')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-8">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div><p class="text-sm font-semibold uppercase tracking-wider text-green-700">Your basket</p><h1 class="mt-1 text-3xl font-bold text-gray-900">Shopping cart</h1></div>
        <a href="{{ route('home') }}" class="text-sm font-medium text-green-700 hover:underline">Continue shopping</a>
    </div>

    @if(session('success'))<div class="mt-5 rounded-lg bg-green-50 p-4 text-green-800">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mt-5 rounded-lg bg-red-50 p-4 text-red-800">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="mt-5 rounded-lg bg-red-50 p-4 text-red-800">{{ $errors->first() }}</div>@endif

    @php
        $validItems = $cartItems->filter(fn ($item) => $item->product);
        $total = $validItems->sum(fn ($item) => (float) $item->product->price * (int) $item->quantity);
    @endphp

    @if($validItems->isEmpty())
        <div class="mt-8 rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center">
            <h2 class="text-xl font-semibold text-gray-900">Your cart is empty</h2>
            <p class="mt-2 text-gray-500">Browse fresh products and add something you love.</p>
            <a href="{{ route('home') }}" class="mt-6 inline-flex rounded-lg bg-green-700 px-5 py-3 font-semibold text-white hover:bg-green-800">Explore products</a>
        </div>
    @else
        <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_340px]">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
                @foreach($validItems as $item)
                    <article class="flex flex-wrap items-center gap-4 border-b border-gray-100 p-5 last:border-0">
                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="h-20 w-20 rounded-xl bg-gray-100 object-cover">
                        <div class="min-w-0 flex-1">
                            <h2 class="font-semibold text-gray-900">{{ $item->product->name }}</h2>
                            <p class="mt-1 text-sm text-gray-500">Quantity: {{ $item->quantity }} · Unit price: {{ number_format((float) $item->product->price, 2) }}</p>
                            <p class="mt-1 text-sm text-gray-500">Available stock: {{ $item->product->stock }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">{{ number_format((float) $item->product->price * (int) $item->quantity, 2) }}</p>
                            <form class="mt-2" action="{{ route('cart.remove', $item->product->id) }}" method="POST">@csrf<button class="text-sm font-medium text-red-600 hover:underline">Remove</button></form>
                        </div>
                    </article>
                @endforeach
            </div>
            <aside class="h-fit rounded-2xl border border-gray-200 bg-white p-5">
                <h2 class="text-lg font-semibold">Order summary</h2>
                <div class="mt-5 flex justify-between text-sm text-gray-600"><span>Items</span><span>{{ $validItems->sum('quantity') }}</span></div>
                <div class="mt-3 flex justify-between border-t pt-4 text-lg font-bold"><span>Total</span><span>{{ number_format($total, 2) }} {{ config('paypal.currency', 'USD') }}</span></div>
                <p class="mt-3 text-xs leading-5 text-gray-500">Your final total is calculated from current product prices and stock on the server when you proceed to checkout.</p>
                <form class="mt-6" method="POST" action="{{ route('paypal.create') }}">@csrf<button class="w-full rounded-lg bg-yellow-400 px-5 py-3 font-bold text-gray-900 hover:bg-yellow-500">Continue with PayPal</button></form>
                <p class="mt-3 text-center text-xs text-gray-500">Secure checkout · PayPal {{ ucfirst(config('paypal.mode', 'sandbox')) }} mode</p>
            </aside>
        </div>
    @endif
</section>
@endsection
