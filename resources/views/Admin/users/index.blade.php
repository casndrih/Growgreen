<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customers · Growgreen Admin</title>
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
<header class="bg-green-800 text-white">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-5 py-5">
        <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold">Growgreen Admin</a>
        <nav class="flex flex-wrap gap-3 text-sm"><a href="{{ route('admin.dashboard') }}">Dashboard</a><a href="{{ route('admin.users.index') }}">Customers</a><a href="{{ route('admin.vendors.index') }}">Vendors</a><a href="{{ route('admin.products.index') }}">Products</a><a href="{{ route('admin.orders.index') }}">Orders</a><a href="{{ route('admin.settings') }}">Settings</a><a href="{{ route('admin.support') }}">Support</a></nav>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg bg-white/10 px-4 py-2 hover:bg-white/20">Sign out</button></form>
    </div>
</header>
<main class="mx-auto max-w-7xl space-y-6 px-5 py-8">
    <div><h1 class="text-3xl font-bold">Customers</h1><p class="mt-1 text-gray-500">Browse registered customer accounts.</p></div>
    <form method="GET" class="flex max-w-xl gap-2"><input name="q" value="{{ request('q') }}" placeholder="Search name or email" class="min-w-0 flex-1 rounded-lg border border-gray-300 px-4 py-2"><button class="rounded-lg bg-green-700 px-5 py-2 font-medium text-white">Search</button>@if(request('q'))<a href="{{ route('admin.users.index') }}" class="rounded-lg border px-4 py-2">Clear</a>@endif</form>
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-[0_1px_2px_rgba(0,0,0,0.08)]">
        <div class="overflow-x-auto"><table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500"><tr><th class="px-5 py-3">Customer</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Phone</th><th class="px-5 py-3">Location</th><th class="px-5 py-3">Joined</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($users as $user)
                <tr><td class="px-5 py-4 font-medium">{{ $user->name }}</td><td class="px-5 py-4">{{ $user->email }}</td><td class="px-5 py-4">{{ $user->phone ?: '—' }}</td><td class="px-5 py-4">{{ collect([$user->city, $user->state])->filter()->implode(', ') ?: '—' }}</td><td class="px-5 py-4">{{ $user->created_at?->format('d M Y') }}</td></tr>
            @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">No customers found.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="border-t px-5 py-4">{{ $users->links() }}</div>
    </section>
</main>
</body>
</html>
