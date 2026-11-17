@extends('app')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-14">
    <div class="rounded-3xl bg-gradient-to-br from-green-900 to-lime-700 px-7 py-12 text-white md:px-12">
        <p class="text-sm font-semibold uppercase tracking-widest text-lime-100">Fresh food. Local enterprise.</p>
        <h1 class="mt-3 max-w-2xl text-4xl font-bold">A marketplace that brings local groceries closer.</h1>
        <p class="mt-5 max-w-2xl leading-7 text-green-50">Growgreen gives local micro, small and medium enterprises a digital place to showcase grocery products and helps customers discover food from local vendors.</p>
    </div>
    <div class="mt-10 grid gap-6 md:grid-cols-3">
        <article class="rounded-2xl border border-gray-200 bg-white p-6"><h2 class="text-lg font-semibold text-gray-900">Discover</h2><p class="mt-2 text-sm leading-6 text-gray-600">Browse grocery categories and explore products from participating vendors.</p></article>
        <article class="rounded-2xl border border-gray-200 bg-white p-6"><h2 class="text-lg font-semibold text-gray-900">Shop</h2><p class="mt-2 text-sm leading-6 text-gray-600">Add available items to your cart and review your order before checkout.</p></article>
        <article class="rounded-2xl border border-gray-200 bg-white p-6"><h2 class="text-lg font-semibold text-gray-900">Sell</h2><p class="mt-2 text-sm leading-6 text-gray-600">Vendors can manage their store profile, product listings, stock and order fulfilment.</p></article>
    </div>
    <div class="mt-10 text-center"><a href="{{ route('home') }}" class="inline-flex rounded-lg bg-green-700 px-6 py-3 font-semibold text-white hover:bg-green-800">Explore Growgreen</a></div>
</section>
@endsection
