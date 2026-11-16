<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Growgreen Vendor</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
<header class="bg-green-800 text-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5">
        <div><p class="text-xl font-bold">Growgreen</p><p class="text-sm text-green-100">Vendor workspace</p></div>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-lg bg-white/10 px-4 py-2 hover:bg-white/20">Sign out</button></form>
    </div>
</header>
<main class="mx-auto max-w-7xl space-y-8 px-5 py-8">
    <div><h1 class="text-3xl font-bold">Your store</h1><p class="mt-1 text-gray-500">Manage products and monitor incoming orders.</p></div>
    @foreach(['success' => 'green', 'error' => 'red', 'info' => 'blue'] as $key => $color)
        @if(session($key))<div class="rounded-lg bg-{{ $color }}-50 p-4 text-{{ $color }}-800">{{ session($key) }}</div>@endif
    @endforeach
    @if($errors->any())<div class="rounded-lg bg-red-50 p-4 text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <section class="rounded-xl border bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold">Store profile</h2>
        <form class="mt-4 grid gap-4 md:grid-cols-2" method="POST" action="{{ route('vendor.profile.update') }}" enctype="multipart/form-data">
            @csrf
            <label class="text-sm font-medium">Store address<input name="address" value="{{ old('address', auth('vendor')->user()->address) }}" class="mt-1 w-full rounded-lg border-gray-300"></label>
            <label class="text-sm font-medium">Store description<textarea name="description" rows="2" class="mt-1 w-full rounded-lg border-gray-300">{{ old('description', auth('vendor')->user()->description) }}</textarea></label>
            <label class="text-sm font-medium">Profile image<input type="file" name="profile_picture" accept="image/*" class="mt-1 block w-full text-sm"></label>
            <label class="text-sm font-medium">Cover image<input type="file" name="cover_picture" accept="image/*" class="mt-1 block w-full text-sm"></label>
            <div class="md:col-span-2"><button class="rounded-lg bg-green-700 px-5 py-3 font-semibold text-white hover:bg-green-800">Save store profile</button></div>
        </form>
    </section>
    <section class="grid gap-4 sm:grid-cols-3">
        @foreach($stats as $label => $value)
            <article class="rounded-xl border bg-white p-5 shadow-sm"><p class="text-sm capitalize text-gray-500">{{ str_replace('_', ' ', $label) }}</p><p class="mt-2 text-3xl font-bold">{{ number_format($value) }}</p></article>
        @endforeach
    </section>
    <section class="rounded-xl border bg-white p-5 shadow-sm">
        <h2 class="text-lg font-semibold">Add a product</h2>
        <form class="mt-4 grid gap-4 md:grid-cols-2" method="POST" action="{{ route('vendor.products.store') }}" enctype="multipart/form-data">
            @csrf
            <label class="text-sm font-medium">Product name<input required name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border-gray-300"></label>
            <label class="text-sm font-medium">Category<select name="category_id" class="mt-1 w-full rounded-lg border-gray-300"><option value="">Choose category</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></label>
            <label class="text-sm font-medium">Price<input required type="number" min="0.01" step="0.01" name="price" value="{{ old('price') }}" class="mt-1 w-full rounded-lg border-gray-300"></label>
            <label class="text-sm font-medium">Stock<input required type="number" min="0" name="stock" value="{{ old('stock', 0) }}" class="mt-1 w-full rounded-lg border-gray-300"></label>
            <label class="text-sm font-medium md:col-span-2">Description<textarea name="description" rows="3" class="mt-1 w-full rounded-lg border-gray-300">{{ old('description') }}</textarea></label>
            <label class="text-sm font-medium">Product image<input required type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm"></label>
            <div class="flex items-end"><button class="rounded-lg bg-green-700 px-5 py-3 font-semibold text-white hover:bg-green-800">Add product</button></div>
        </form>
    </section>
    <section class="rounded-xl border bg-white shadow-sm">
        <div class="border-b px-5 py-4"><h2 class="font-semibold">Your products</h2></div>
        <div class="divide-y">
            @forelse($products as $product)
                <div class="flex flex-wrap items-center justify-between gap-4 p-5">
                    <div><p class="font-semibold">{{ $product->name }}</p><p class="text-sm text-gray-500">{{ $product->category?->name ?? 'Uncategorised' }} · {{ number_format((float) $product->price, 2) }} · Stock: {{ $product->stock }}</p></div>
                    <div class="flex flex-wrap items-center gap-2">
                        <details class="text-sm"><summary class="cursor-pointer rounded-lg border px-3 py-2">Edit</summary>
                            <form class="mt-3 grid min-w-64 gap-2 rounded-lg border bg-gray-50 p-3" method="POST" action="{{ route('vendor.products.update', $product) }}" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <input required name="name" value="{{ $product->name }}" class="rounded border-gray-300 text-sm" aria-label="Product name">
                                <input required type="number" min="0.01" step="0.01" name="price" value="{{ $product->price }}" class="rounded border-gray-300 text-sm" aria-label="Price">
                                <input required type="number" min="0" name="stock" value="{{ $product->stock }}" class="rounded border-gray-300 text-sm" aria-label="Stock">
                                <select required name="category_id" class="rounded border-gray-300 text-sm"><option value="">Uncategorised</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected($product->category_id == $category->id)>{{ $category->name }}</option>@endforeach</select>
                                <textarea name="description" rows="2" class="rounded border-gray-300 text-sm" aria-label="Description">{{ $product->description }}</textarea>
                                <input type="file" name="image" accept="image/*" class="text-xs">
                                <button class="rounded bg-green-700 px-3 py-2 text-xs font-semibold text-white">Save changes</button>
                            </form>
                        </details>
                        <form method="POST" action="{{ route('vendor.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">@csrf @method('DELETE')<button class="rounded-lg border border-red-200 px-3 py-2 text-sm text-red-700">Delete</button></form>
                    </div>
                </div>
            @empty
                <p class="p-6 text-sm text-gray-500">You have not added any products yet.</p>
            @endforelse
        </div>
        <div class="p-5">{{ $products->links() }}</div>
    </section>
    <section class="rounded-xl border bg-white shadow-sm">
        <div class="border-b px-5 py-4"><h2 class="font-semibold">Recent orders</h2></div>
        <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-gray-50 text-gray-500"><tr><th class="px-5 py-3">Order</th><th class="px-5 py-3">Product</th><th class="px-5 py-3">Qty</th><th class="px-5 py-3">Total</th><th class="px-5 py-3">Status</th></tr></thead><tbody class="divide-y">
            @forelse($orders as $order)<tr><td class="px-5 py-3">#{{ $order->id }}</td><td class="px-5 py-3">{{ $order->product_name }}</td><td class="px-5 py-3">{{ $order->quantity }}</td><td class="px-5 py-3">{{ number_format((float) $order->price, 2) }}</td><td class="px-5 py-3"><div>{{ str_replace('_', ' ', $order->status) }}</div>@if($order->status === 'paid' || $order->status === 'processing' || $order->status === 'shipped')<form method="POST" action="{{ route('vendor.orders.status', $order) }}" class="mt-2 flex items-center gap-2">@csrf @method('PATCH')<select name="status" class="rounded border-gray-300 text-xs"><option value="processing" @selected($order->status === 'paid')>Processing</option><option value="shipped" @selected($order->status === 'processing')>Shipped</option><option value="closed" @selected($order->status === 'shipped')>Complete</option></select><button class="rounded bg-green-700 px-2 py-1 text-xs text-white">Update</button></form>@endif</td></tr>@empty<tr><td colspan="5" class="px-5 py-8 text-center text-gray-500">No orders yet.</td></tr>@endforelse
        </tbody></table></div>
    </section>
</main>
</body>
</html>
