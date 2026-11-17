@extends('app')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-8">
    <a href="{{ route('home') }}" class="text-sm font-medium text-green-700 hover:underline">← Back to marketplace</a>
    <div class="mt-5 grid gap-8 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:grid-cols-2 md:p-8">
        <div class="flex min-h-72 items-center justify-center overflow-hidden rounded-xl bg-gray-50">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full max-h-[520px] w-full object-cover">
            @else
                <div class="p-10 text-center text-lg font-semibold text-green-800">Fresh from Growgreen</div>
            @endif
        </div>
        <div class="flex flex-col">
            <p class="text-sm font-semibold uppercase tracking-wider text-green-700">{{ $product->category?->name ?? 'Grocery' }}</p>
            <h1 class="mt-2 text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
            <p class="mt-4 text-2xl font-bold text-gray-900">{{ number_format((float) $product->price, 2) }} {{ config('paypal.currency', 'USD') }} <span class="text-sm font-normal text-gray-500">per {{ $product->unit ?: 'item' }}</span></p>
            <p class="mt-5 leading-7 text-gray-600">{{ $product->description ?: 'A fresh grocery item from a Growgreen marketplace vendor.' }}</p>
            <p class="mt-5 text-sm {{ $product->stock > 0 ? 'text-green-700' : 'text-red-700' }}">
                {{ $product->stock > 0 ? $product->stock.' available in stock' : 'Currently out of stock' }}
            </p>

            @if($product->stock > 0)
                @auth
                    <form id="add-to-cart-form" class="mt-6 flex flex-wrap items-end gap-3">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <label class="text-sm font-medium text-gray-700">Quantity
                            <input name="quantity" type="number" min="1" max="{{ min((int) $product->stock, 100) }}" value="1" required class="mt-1 block w-24 rounded-lg border-gray-300">
                        </label>
                        <button class="rounded-lg bg-green-700 px-6 py-3 font-semibold text-white hover:bg-green-800">Add to cart</button>
                    </form>
                    <p id="cart-feedback" role="status" class="mt-3 text-sm" aria-live="polite"></p>
                @else
                    <a href="{{ route('login') }}" class="mt-6 inline-flex w-fit rounded-lg bg-green-700 px-6 py-3 font-semibold text-white hover:bg-green-800">Sign in to add to cart</a>
                @endauth
            @endif

            <div class="mt-8 border-t border-gray-100 pt-5">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Sold by</p>
                <p class="mt-1 font-semibold text-gray-900">{{ $vendor->name }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ $vendor->address }}</p>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
@auth
<script>
document.getElementById('add-to-cart-form')?.addEventListener('submit', async function (event) {
    event.preventDefault();
    const feedback = document.getElementById('cart-feedback');
    const button = this.querySelector('button[type="submit"], button:not([type])');
    button.disabled = true;
    feedback.textContent = 'Adding product…';
    try {
        const response = await fetch(@json(route('cart.add')), {
            method: 'POST',
            headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
            body: new FormData(this),
            credentials: 'same-origin'
        });
        const result = await response.json();
        feedback.textContent = result.message || (response.ok ? 'Added to cart.' : 'Unable to add this product.');
        feedback.className = 'mt-3 text-sm ' + (response.ok ? 'text-green-700' : 'text-red-700');
        if (response.ok) {
            const link = document.createElement('a');
            link.href = @json(route('cart.index'));
            link.textContent = ' View cart';
            link.className = 'ml-2 font-semibold underline';
            feedback.appendChild(link);
        }
    } catch (error) {
        feedback.textContent = 'A network error occurred. Please try again.';
        feedback.className = 'mt-3 text-sm text-red-700';
    } finally {
        button.disabled = false;
    }
});
</script>
@endauth
@endsection
