@extends('app')

@section('content')
<section class="mx-auto flex min-h-[65vh] max-w-md items-center px-4 py-12">
    <div class="w-full rounded-2xl border border-gray-200 bg-white p-7 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-wider text-green-700">Welcome back</p>
        <h1 class="mt-2 text-3xl font-bold text-gray-900">Sign in to Growgreen</h1>
        <p class="mt-2 text-sm text-gray-500">Access your account, cart and orders.</p>
        @if($errors->any())<div class="mt-5 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
        <form class="mt-6 space-y-4" method="POST" action="{{ route('login.submit') }}">
            @csrf
            <label class="block text-sm font-medium text-gray-700">Email address<input required type="email" name="email" value="{{ old('email') }}" autocomplete="email" class="mt-1 w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600"></label>
            <label class="block text-sm font-medium text-gray-700">Password<input required type="password" name="password" autocomplete="current-password" class="mt-1 w-full rounded-lg border-gray-300 focus:border-green-600 focus:ring-green-600"></label>
            <label class="flex items-center gap-2 text-sm text-gray-600"><input type="checkbox" name="remember" value="1" class="rounded border-gray-300 text-green-700"> Remember me</label>
            <button class="w-full rounded-lg bg-green-700 px-5 py-3 font-semibold text-white hover:bg-green-800">Sign in</button>
        </form>
        <p class="mt-5 text-center text-sm text-gray-500">New to Growgreen? <a href="{{ route('home') }}#register" class="font-semibold text-green-700 hover:underline">Create an account</a></p>
    </div>
</section>
@endsection
