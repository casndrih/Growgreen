@extends('app')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
    <a href="{{ route('home') }}" class="text-sm font-medium text-green-700 hover:underline">← Back to marketplace</a>
    <div class="mt-5 grid gap-6 md:grid-cols-2">
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-[0_1px_2px_rgba(0,0,0,0.08)]">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="max-h-[560px] min-h-72 w-full object-cover">
            @else
                <div class="flex min-h-72 items-center justify-center bg-gradient-to-br from-lime-100 to-green-200 text-lg font-semibold text-green-900">Fresh from Growgreen</div>
            @endif
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-[0_1px_2px_rgba(0,0,0,0.08)] sm:p-7">
            <p class="text-xs font-semibold uppercase tracking-wider text-green-700">{{ $product->category?->name ?? 'Grocery' }}</p>
            <h1 class="mt-2 text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
            <p class="mt-4 text-2xl font-bold text-gray-900">K{{ number_format((float) $product->price, 2) }} <span class="text-sm font-normal text-gray-500">/ {{ $product->unit ?: 'item' }}</span></p>
            <div class="mt-5 border-t border-gray-100 pt-5"><h2 class="font-semibold text-gray-900">Product description</h2><p class="mt-2 whitespace-pre-line text-sm leading-7 text-gray-600">{{ $product->description ?: 'A fresh grocery item from a Growgreen marketplace vendor.' }}</p></div>
            <p class="mt-5 text-sm font-medium {{ $product->stock > 0 ? 'text-green-700' : 'text-red-700' }}">{{ $product->stock > 0 ? $product->stock.' available in stock' : 'Currently out of stock' }}</p>
            @if($product->stock > 0)
                @auth('web')
                    <form id="add-to-cart-form" action="{{ route('cart.add') }}" method="POST" class="mt-5 flex flex-wrap items-end gap-3">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <label class="text-sm font-medium text-gray-700">Quantity<input name="quantity" type="number" min="1" max="{{ min((int) $product->stock, 100) }}" value="1" required class="mt-1 block w-24 rounded-lg border-gray-300"></label>
                        <button type="submit" class="rounded-lg bg-green-700 px-6 py-3 font-semibold text-white hover:bg-green-800">Add to cart</button>
                        <a href="{{ route('cart.index') }}" class="rounded-lg border border-green-700 px-5 py-3 font-semibold text-green-800 hover:bg-green-50">View cart</a>
                    </form>
                    <p id="cart-feedback" role="status" aria-live="polite" class="mt-3 text-sm"></p>
                @else
                    <a href="{{ route('login') }}" class="mt-5 inline-flex rounded-lg bg-green-700 px-6 py-3 font-semibold text-white hover:bg-green-800">Sign in to add to cart</a>
                @endauth
            @endif
            <div class="mt-7 border-t border-gray-100 pt-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Sold by</p>
                <div class="mt-3 flex items-center gap-3">
                    <img src="{{ $vendor->profile_picture_url ?: 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?auto=format&fit=crop&w=160&q=80' }}" alt="{{ $vendor->name }}" class="h-14 w-14 rounded-full object-cover">
                    <div><p class="font-semibold text-gray-900">{{ $vendor->name }}</p><p class="mt-1 text-sm text-gray-500">{{ $vendor->address ?: 'Local seller' }}</p><p class="mt-1 text-xs text-green-700">{{ ucfirst($vendor->status ?? 'open') }} store</p></div>
                </div>
                @if($vendor->description)<p class="mt-3 text-sm leading-6 text-gray-600">{{ $vendor->description }}</p>@endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
@auth('web')
<script>
document.getElementById('add-to-cart-form')?.addEventListener('submit', async function (event) {
    event.preventDefault();
    const form = this;
    const button = form.querySelector('button[type="submit"]');
    const feedback = document.getElementById('cart-feedback');
    button.disabled = true;
    button.textContent = 'Adding…';
    try {
        const response = await fetch(form.action, {method:'POST', body:new FormData(form), headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin'});
        const payload = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(payload.message || Object.values(payload.errors || {}).flat()[0] || 'Unable to add this product.');
        feedback.textContent = payload.message || 'Product added to your cart.';
        feedback.className = 'mt-3 text-sm text-green-700';
    } catch (error) {
        feedback.textContent = error.message || 'A network error occurred. Please try again.';
        feedback.className = 'mt-3 text-sm text-red-700';
    } finally {
        button.disabled = false;
        button.textContent = 'Add to cart';
    }
});
</script>
@endauth
@endsection
