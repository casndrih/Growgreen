@extends('app')

@section('content')
<section class="mx-auto flex min-h-[65vh] max-w-md items-center px-4 py-12">
    <div class="w-full rounded-2xl border border-gray-200 bg-white p-7 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-wider text-green-700">Account security</p>
        <h1 class="mt-2 text-3xl font-bold text-gray-900">Reset your password</h1>
        <p class="mt-2 text-sm text-gray-500">Choose a new password for your Growgreen account.</p>
        @if($errors->any())<div class="mt-5 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
        <form class="mt-6 space-y-4" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <label class="block text-sm font-medium text-gray-700">Email address<input required type="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" class="mt-1 w-full rounded-lg border-gray-300"></label>
            <label class="block text-sm font-medium text-gray-700">New password<input required minlength="8" type="password" name="password" autocomplete="new-password" class="mt-1 w-full rounded-lg border-gray-300"></label>
            <label class="block text-sm font-medium text-gray-700">Confirm new password<input required minlength="8" type="password" name="password_confirmation" autocomplete="new-password" class="mt-1 w-full rounded-lg border-gray-300"></label>
            <button class="w-full rounded-lg bg-green-700 px-5 py-3 font-semibold text-white hover:bg-green-800">Update password</button>
        </form>
    </div>
</section>
@endsection
