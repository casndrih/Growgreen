<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vendors · Growgreen Admin</title>
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
    <div><h1 class="text-3xl font-bold">Vendors</h1><p class="mt-1 text-gray-500">Review marketplace stores and verification status.</p></div>
    <form method="GET" class="flex max-w-xl gap-2"><input name="q" value="{{ request('q') }}" placeholder="Search store or email" class="min-w-0 flex-1 rounded-lg border border-gray-300 px-4 py-2"><button class="rounded-lg bg-green-700 px-5 py-2 font-medium text-white">Search</button>@if(request('q'))<a href="{{ route('admin.vendors.index') }}" class="rounded-lg border px-4 py-2">Clear</a>@endif</form>
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-[0_1px_2px_rgba(0,0,0,0.08)]">
        <div class="overflow-x-auto"><table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500"><tr><th class="px-5 py-3">Store</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Address</th><th class="px-5 py-3">Store status</th><th class="px-5 py-3">Verification</th><th class="px-5 py-3">Joined</th><th class="px-5 py-3">Manage</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($vendors as $vendor)
                <tr><td class="px-5 py-4"><div class="flex items-center gap-3"><img src="{{ $vendor->profile_picture_url ?: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=96&h=96&q=80' }}" alt="" class="h-10 w-10 rounded-full object-cover"><span class="font-medium">{{ $vendor->name }}</span></div></td><td class="px-5 py-4">{{ $vendor->email }}</td><td class="px-5 py-4">{{ $vendor->address ?: '—' }}</td><td class="px-5 py-4"><span class="rounded-full bg-gray-100 px-2 py-1 text-xs">{{ ucfirst($vendor->status ?? 'unknown') }}</span></td><td class="px-5 py-4"><span class="rounded-full bg-green-50 px-2 py-1 text-xs text-green-800">{{ ucfirst($vendor->verification ?? 'pending') }}</span></td><td class="px-5 py-4">{{ $vendor->created_at?->format('d M Y') }}</td><td class="px-5 py-4"><form method="POST" action="{{ route('admin.vendors.update',$vendor) }}" class="flex min-w-56 flex-col gap-2">@csrf @method('PATCH')<select name="status" class="rounded border-gray-300 text-xs"><option value="open" @selected($vendor->status === 'open')>Open</option><option value="closed" @selected($vendor->status === 'closed')>Closed</option></select><select name="verification" class="rounded border-gray-300 text-xs"><option value="pending" @selected($vendor->verification === 'pending')>Pending</option><option value="approved" @selected($vendor->verification === 'approved')>Approved</option><option value="rejected" @selected($vendor->verification === 'rejected')>Rejected</option></select><button class="rounded bg-green-700 px-3 py-1.5 text-xs font-semibold text-white">Save status</button></form></td></tr>
            @empty
                <tr><td colspan="7" class="px-5 py-10 text-center text-gray-500">No vendors found.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="border-t px-5 py-4">{{ $vendors->links() }}</div>
    </section>
</main>
</body>
</html>
