@extends('app')

@section('content')
<section class="bg-white">
    <div id="default-carousel" class="relative w-full" data-carousel="slide">
        <div class="relative h-48 overflow-hidden bg-green-800 sm:h-64 lg:h-96">
            @forelse($groceries as $index => $grocery)
                <div class="hidden duration-700 ease-in-out" data-carousel-item>
                    <img src="{{ $grocery['urls']['regular'] }}" class="absolute inset-0 h-full w-full object-cover" alt="{{ $grocery['alt_description'] ?: 'Fresh groceries' }}">
                </div>
            @empty
                <div class="flex h-full items-center justify-center bg-gradient-to-r from-green-900 to-lime-600 px-6 text-center text-white">
                    <div><p class="text-sm font-semibold uppercase tracking-widest text-lime-100">Fresh food, local markets</p><h2 class="mt-3 text-3xl font-bold md:text-5xl">Good food starts here.</h2><p class="mt-3 text-sm text-green-50 md:text-base">Discover fresh groceries from your favourite local vendors.</p></div>
                </div>
            @endforelse
        </div>
        @if(count($groceries) > 1)
            <div class="absolute bottom-5 left-1/2 z-30 flex -translate-x-1/2 space-x-3">
                @foreach($groceries as $index => $grocery)
                    <button type="button" class="h-2 w-2 rounded-full bg-white" aria-label="Slide {{ $index + 1 }}" data-carousel-slide-to="{{ $index }}"></button>
                @endforeach
            </div>
        @endif
    </div>

    <div class="mx-auto max-w-7xl px-4 py-7 sm:px-6">
        <div class="mb-4 flex items-center justify-between"><h2 class="text-xl font-semibold text-gray-900">Shop by Category</h2></div>
        <div class="flex gap-3 overflow-x-auto pb-2">
            @forelse($categoryData as $item)
                <a href="{{ route('search', ['q' => $item['name']]) }}" class="group w-24 flex-shrink-0 overflow-hidden marketplace-card rounded-xl bg-white text-center transition hover:-translate-y-0.5 hover:shadow-[0_2px_4px_rgba(0,0,0,0.10)]">
                    @if($item['image'])
                        <img class="h-20 w-full object-cover" src="{{ $item['image'] }}" alt="{{ $item['name'] }}" loading="lazy">
                    @else
                        <div class="flex h-20 items-center justify-center bg-gradient-to-br from-lime-100 to-green-200 text-3xl" aria-hidden="true">🌿</div>
                    @endif
                    <span class="block truncate px-1.5 py-2 text-xs font-medium text-gray-800">{{ $item['name'] }}</span>
                </a>
            @empty
                <p class="text-sm text-gray-500">Categories will appear here as vendors add products.</p>
            @endforelse
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6">
        <div class="mb-4 flex items-center justify-between"><h2 class="text-xl font-semibold text-gray-900">New Groceries</h2><a href="{{ route('search', ['q' => 'fresh']) }}" class="text-sm font-medium text-green-700 hover:underline">Explore products →</a></div>
        <div class="flex gap-4 overflow-x-auto pb-3">
            @forelse($productData as $foodProduct)
                <a href="{{ route('product.show', ['id' => $foodProduct['id']]) }}" class="w-44 flex-shrink-0 overflow-hidden marketplace-card rounded-xl bg-white transition hover:shadow-[0_2px_4px_rgba(0,0,0,0.10)] sm:w-52">
                    @if($foodProduct['image'])
                        <img src="{{ $foodProduct['image'] }}" alt="{{ $foodProduct['name'] }}" class="h-36 w-full object-cover" loading="lazy">
                    @else
                        <div class="flex h-36 items-center justify-center bg-gradient-to-br from-lime-100 to-green-200 text-sm font-semibold text-green-900">Fresh grocery</div>
                    @endif
                    <div class="p-3"><h3 class="truncate text-sm font-semibold text-gray-900">{{ $foodProduct['name'] }}</h3><p class="mt-1 line-clamp-2 whitespace-normal text-xs text-gray-500">{{ $foodProduct['description'] }}</p><p class="mt-2 text-sm font-bold text-green-800">K{{ number_format((float) $foodProduct['price'], 2) }}</p></div>
                </a>
            @empty
                <p class="text-sm text-gray-500">New products from local vendors will appear here.</p>
            @endforelse
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-7 sm:px-6">
        <div class="mb-4 flex items-center justify-between"><h2 class="text-xl font-semibold text-gray-900">Popular Vendors</h2><span class="text-xs text-gray-500">Local marketplace sellers</span></div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @forelse($vendors as $vendor)
                <article class="flex items-center gap-3 marketplace-card rounded-xl bg-white p-3">
                    <img src="{{ $vendor->profile_picture_url ?: 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?auto=format&fit=crop&w=160&q=80' }}" alt="{{ $vendor->name }}" class="h-14 w-14 flex-shrink-0 rounded-full object-cover" loading="lazy">
                    <div class="min-w-0"><p class="truncate text-sm font-semibold text-gray-900">{{ $vendor->name }}</p><p class="truncate text-xs text-gray-500">{{ $vendor->address ?: 'Local seller' }}</p><span class="text-xs font-medium text-green-700">{{ ucfirst($vendor->status ?? 'open') }}</span></div>
                </article>
            @empty
                <p class="text-sm text-gray-500">No vendors are listed yet.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
