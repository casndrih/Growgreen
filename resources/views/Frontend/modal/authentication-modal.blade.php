<div id="authentication-modal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden h-full w-full items-center justify-center overflow-y-auto bg-gray-900/50 p-3">
  <div class="relative my-auto max-h-[92vh] w-full max-w-md overflow-y-auto rounded-2xl border border-gray-100 bg-white shadow-[0_2px_4px_rgba(0,0,0,0.12)]">
    <button type="button" data-modal-hide="authentication-modal" aria-label="Close" class="absolute right-3 top-3 rounded-full p-2 text-gray-500 hover:bg-gray-100">✕</button>
    <div class="p-6 sm:p-8">
      <div class="mb-6"><p class="text-sm font-semibold uppercase tracking-wider text-green-700">Welcome to Growgreen</p><h2 class="mt-1 text-2xl font-bold text-gray-900">Sign in</h2><p class="mt-1 text-sm text-gray-500">Access your account, orders and store.</p></div>
      @if($errors->any())<div class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</div>@endif
      <form class="space-y-4" action="{{ route('login.submit') }}" method="POST">
        @csrf
        <label class="block text-sm font-medium text-gray-700">Email address<input type="email" name="email" required autocomplete="email" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600"></label>
        <label class="block text-sm font-medium text-gray-700">Password<input type="password" name="password" required autocomplete="current-password" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-green-600 focus:ring-green-600"></label>
        <div class="flex items-center justify-between gap-3 text-sm"><label class="flex items-center gap-2 text-gray-600"><input type="checkbox" name="remember" value="1" class="rounded border-gray-300 text-green-700"> Remember me</label><button type="button" data-modal-target="reset-modal" data-modal-show="reset-modal" data-modal-hide="authentication-modal" class="text-green-700 hover:underline">Forgot password?</button></div>
        <button type="submit" class="w-full rounded-lg bg-green-700 px-5 py-3 font-semibold text-white hover:bg-green-800">Sign in to your account</button>
      </form>
      <div class="my-5 flex items-center gap-3 text-xs text-gray-400"><span class="h-px flex-1 bg-gray-200"></span>NEW TO GROGREEN?<span class="h-px flex-1 bg-gray-200"></span></div>
      <button type="button" data-modal-target="registration-modal" data-modal-show="registration-modal" data-modal-hide="authentication-modal" class="w-full rounded-lg border border-green-700 px-5 py-3 font-semibold text-green-800 hover:bg-green-50">Create an account</button>
    </div>
  </div>
</div>

<div id="registration-modal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden h-full w-full items-center justify-center overflow-y-auto bg-gray-900/50 p-3">
  <div class="relative my-auto max-h-[92vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white shadow-[0_2px_4px_rgba(0,0,0,0.12)]">
    <button type="button" data-modal-hide="registration-modal" aria-label="Close" class="absolute right-3 top-3 rounded-full p-2 text-gray-500 hover:bg-gray-100">✕</button>
    <div class="p-6 sm:p-8"><h2 class="text-2xl font-bold text-gray-900">Create your account</h2><p class="mt-1 text-sm text-gray-500">Join as a customer or local seller.</p>
      <form class="mt-6 space-y-3" action="{{ url('/register') }}" method="POST">
        @csrf
        <label class="block text-sm font-medium text-gray-700">Full name or store name<input type="text" name="full_name" value="{{ old('full_name') }}" required maxlength="255" autocomplete="name" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
        <label class="block text-sm font-medium text-gray-700">Email address<input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
        <label class="block text-sm font-medium text-gray-700">Account type<select name="role" required class="mt-1 w-full rounded-lg border-gray-300 text-sm"><option value="user">Customer / Buyer</option><option value="vendor">Seller / Vendor</option></select></label>
        <label class="block text-sm font-medium text-gray-700">Password<input type="password" name="password" required minlength="8" autocomplete="new-password" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
        <label class="block text-sm font-medium text-gray-700">Confirm password<input type="password" name="confirm_password" required minlength="8" autocomplete="new-password" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
        <label class="flex items-start gap-2 text-xs leading-5 text-gray-600"><input type="checkbox" name="terms" value="1" required class="mt-1 rounded border-gray-300"><span>I agree to the <a href="{{ route('policy') }}" class="text-green-700 underline">Privacy Policy</a> and <a href="{{ route('tos') }}" class="text-green-700 underline">Terms of Service</a>.</span></label>
        <button type="submit" class="w-full rounded-lg bg-green-700 px-5 py-3 font-semibold text-white hover:bg-green-800">Create account</button>
      </form>
      <p class="mt-4 text-center text-sm text-gray-500">Already registered? <button type="button" data-modal-target="authentication-modal" data-modal-show="authentication-modal" data-modal-hide="registration-modal" class="font-semibold text-green-700 hover:underline">Sign in</button></p>
    </div>
  </div>
</div>

<div id="reset-modal" tabindex="-1" aria-hidden="true" class="fixed inset-0 z-50 hidden h-full w-full items-center justify-center overflow-y-auto bg-gray-900/50 p-3">
  <div class="relative my-auto w-full max-w-md rounded-2xl bg-white p-6 shadow-[0_2px_4px_rgba(0,0,0,0.12)]">
    <button type="button" data-modal-hide="reset-modal" aria-label="Close" class="absolute right-3 top-3 rounded-full p-2 text-gray-500 hover:bg-gray-100">✕</button>
    <h2 class="text-xl font-bold text-gray-900">Reset your password</h2><p class="mt-1 text-sm text-gray-500">We'll email a reset link if the account exists.</p>
    <form method="POST" action="{{ route('password.email') }}" class="mt-5 space-y-4">@csrf
      <label class="block text-sm font-medium text-gray-700">Email address<input type="email" name="email" required autocomplete="email" class="mt-1 w-full rounded-lg border-gray-300 text-sm"></label>
      <button type="submit" class="w-full rounded-lg bg-green-700 px-5 py-3 font-semibold text-white">Send reset link</button>
    </form>
  </div>
</div>
