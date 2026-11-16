@extends('User.app')

@section('content')
<section class="mx-auto max-w-5xl p-4 md:p-8">
    <a href="{{ route('orders.index') }}" class="text-lime-700 hover:underline">← Back to my orders</a>
    <div class="mt-5 rounded-xl bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500">Order #{{ $order->id }}</p>
                <h1 class="mt-1 text-2xl font-semibold text-gray-900">{{ $order->product_name }}</h1>
                <p class="mt-1 text-gray-600">Sold by {{ $order->vendor?->name ?? 'Growgreen vendor' }}</p>
            </div>
            <span class="rounded-full bg-lime-100 px-3 py-1 text-sm font-semibold text-lime-800">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span>
        </div>
        <dl class="mt-8 grid gap-5 sm:grid-cols-2">
            <div><dt class="text-sm text-gray-500">Quantity</dt><dd class="mt-1 font-medium">{{ $order->quantity }}</dd></div>
            <div><dt class="text-sm text-gray-500">Line total</dt><dd class="mt-1 font-medium">{{ number_format((float) $order->price, 2) }}</dd></div>
            <div><dt class="text-sm text-gray-500">Placed</dt><dd class="mt-1 font-medium">{{ $order->created_at?->format('d M Y, H:i') }}</dd></div>
            <div><dt class="text-sm text-gray-500">Last updated</dt><dd class="mt-1 font-medium">{{ $order->updated_at?->format('d M Y, H:i') }}</dd></div>
        </dl>
    </div>
</section>
@endsection
