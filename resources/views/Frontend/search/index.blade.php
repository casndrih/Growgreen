@extends('app')
@section('content')
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
    <div class="mb-6"><p class="text-xs font-semibold uppercase tracking-wider text-green-700">Marketplace search</p><h1 class="mt-1 text-3xl font-bold text-gray-900">Results for “{{ $term }}”</h1><p class="mt-1 text-sm text-gray-500">{{ $products->total() }} available product(s) found</p></div>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
        @forelse($products as $product)
            <a href="{{ route('product.show',['id'=>$product->id]) }}" class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-[0_1px_2px_rgba(0,0,0,0.08)]">
                @if($product->image_url)<img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-36 w-full object-cover" loading="lazy">@else<div class="flex h-36 items-center justify-center bg-lime-100 text-sm font-semibold text-green-900">Fresh grocery</div>@endif
                <div class="p-3"><p class="truncate text-sm font-semibold text-gray-900">{{ $product->name }}</p><p class="mt-1 text-xs text-gray-500">{{ $product->category?->name }}</p><p class="mt-2 font-bold text-green-800">K{{ number_format((float)$product->price,2) }}</p><p class="mt-1 text-xs text-gray-500">Sold by {{ $product->vendor?->name }}</p></div>
            </a>
        @empty
            <p class="col-span-full rounded-xl border border-dashed border-gray-300 p-10 text-center text-sm text-gray-500">No available products matched your search. Try another product, vendor or category.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $products->links() }}</div>
</section>
@endsection
