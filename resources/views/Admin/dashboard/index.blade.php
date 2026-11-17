<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Growgreen Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
<header class="bg-green-800 text-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5">
        <div><p class="text-xl font-bold">Growgreen</p><p class="text-sm text-green-100">Administration</p></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg bg-white/10 px-4 py-2 hover:bg-white/20">Sign out</button></form>
    </div>
</header>
<main class="mx-auto max-w-7xl space-y-8 px-5 py-8">
    <div><h1 class="text-3xl font-bold">Dashboard</h1><p class="mt-1 text-gray-500">Marketplace overview and recent activity.</p></div>
    <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @foreach($stats as $label => $value)
            <article class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm capitalize text-gray-500">{{ str_replace('_', ' ', $label) }}</p>
                <p class="mt-2 text-3xl font-bold">{{ number_format($value) }}</p>
            </article>
        @endforeach
    </section>
    <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b px-5 py-4"><h2 class="font-semibold">Recent orders</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500"><tr><th class="px-5 py-3">Order</th><th class="px-5 py-3">Customer</th><th class="px-5 py-3">Vendor</th><th class="px-5 py-3">Product</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Status</th></tr></thead>
                <tbody class="divide-y">
                @forelse($recentOrders as $order)
                    <tr><td class="px-5 py-3">#{{ $order->id }}</td><td class="px-5 py-3">{{ $order->user?->name ?? '—' }}</td><td class="px-5 py-3">{{ $order->vendor?->name ?? '—' }}</td><td class="px-5 py-3">{{ $order->product_name }}</td><td class="px-5 py-3">{{ number_format((float) $order->price, 2) }}</td><td class="px-5 py-3">{{ str_replace('_', ' ', $order->status) }}</td></tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-500">No orders have been placed yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b px-5 py-4"><h2 class="font-semibold">Recent payments</h2><p class="mt-1 text-xs text-gray-500">Review completed, pending and refund-required PayPal transactions.</p></div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500"><tr><th class="px-5 py-3">Reference</th><th class="px-5 py-3">Customer</th><th class="px-5 py-3">Amount</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Date</th></tr></thead>
                <tbody class="divide-y">
                @forelse($recentPayments as $payment)
                    <tr><td class="px-5 py-3 font-mono text-xs">{{ $payment->reference }}</td><td class="px-5 py-3">{{ $payment->user?->name ?? '—' }}</td><td class="px-5 py-3">{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</td><td class="px-5 py-3">{{ str_replace('_', ' ', $payment->status) }}</td><td class="px-5 py-3">{{ $payment->created_at?->format('d M Y H:i') }}</td></tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-500">No payments recorded yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b px-5 py-4"><h2 class="font-semibold">Latest vendors</h2></div>
        <div class="grid gap-3 p-5 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($recentVendors as $vendor)
                <div class="rounded-lg bg-gray-50 p-4"><p class="font-medium">{{ $vendor->name }}</p><p class="mt-1 text-sm text-gray-500">{{ $vendor->email }}</p><p class="mt-2 text-xs uppercase tracking-wide text-gray-500">{{ $vendor->status ?? 'Status unavailable' }}</p></div>
            @empty
                <p class="text-sm text-gray-500">No vendors registered yet.</p>
            @endforelse
        </div>
    </section>
</main>
</body>
</html>
