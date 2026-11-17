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
        <nav class="flex gap-4 text-sm"><a href="{{ route('admin.dashboard') }}" class="hover:underline">Dashboard</a><a href="{{ route('admin.users.index') }}" class="hover:underline">Customers</a><a href="{{ route('admin.vendors.index') }}" class="font-semibold underline">Vendors</a></nav>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg bg-white/10 px-4 py-2 hover:bg-white/20">Sign out</button></form>
    </div>
</header>
<main class="mx-auto max-w-7xl space-y-6 px-5 py-8">
    <div><h1 class="text-3xl font-bold">Vendors</h1><p class="mt-1 text-gray-500">Review marketplace stores and verification status.</p></div>
    <form method="GET" class="flex max-w-xl gap-2"><input name="q" value="{{ request('q') }}" placeholder="Search store or email" class="min-w-0 flex-1 rounded-lg border border-gray-300 px-4 py-2"><button class="rounded-lg bg-green-700 px-5 py-2 font-medium text-white">Search</button>@if(request('q'))<a href="{{ route('admin.vendors.index') }}" class="rounded-lg border px-4 py-2">Clear</a>@endif</form>
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="overflow-x-auto"><table class="w-full text-left text-sm">
            <thead class="bg-gray-50 text-gray-500"><tr><th class="px-5 py-3">Store</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Address</th><th class="px-5 py-3">Store status</th><th class="px-5 py-3">Verification</th><th class="px-5 py-3">Joined</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
            @forelse($vendors as $vendor)
                <tr><td class="px-5 py-4 font-medium">{{ $vendor->name }}</td><td class="px-5 py-4">{{ $vendor->email }}</td><td class="px-5 py-4">{{ $vendor->address ?: '—' }}</td><td class="px-5 py-4"><span class="rounded-full bg-gray-100 px-2 py-1 text-xs">{{ ucfirst($vendor->status ?? 'unknown') }}</span></td><td class="px-5 py-4"><span class="rounded-full bg-green-50 px-2 py-1 text-xs text-green-800">{{ ucfirst($vendor->verification ?? 'pending') }}</span></td><td class="px-5 py-4">{{ $vendor->created_at?->format('d M Y') }}</td></tr>
            @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">No vendors found.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        <div class="border-t px-5 py-4">{{ $vendors->links() }}</div>
    </section>
</main>
</body>
</html>
