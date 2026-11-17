<header class="sticky top-0 z-40 border-b border-gray-100 bg-white">
  <div class="mx-auto flex max-w-7xl items-center gap-2 px-3 py-3 sm:gap-4 sm:px-5">
    <a href="{{ route('home') }}" class="flex flex-shrink-0 items-center gap-2">
      <img src="{{ URL::asset('images/favicon/logo.svg') }}" alt="Grogreen" class="h-8 w-8">
      <span class="font-outfit hidden text-xl font-semibold text-lime-600 sm:inline">Grogreen</span>
    </a>
    <form action="{{ route('search') }}" method="GET" class="relative min-w-0 flex-1 sm:mx-auto sm:max-w-xl">
      <label for="searchInput" class="sr-only">Search groceries and vendors</label>
      <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">⌕</span>
        <input type="search" name="q" value="{{ $term ?? request('q') }}" placeholder="Search groceries & vendors" class="w-full rounded-full border-0 bg-gray-50 py-2.5 pl-9 pr-4 text-sm text-gray-900 outline-none transition focus:bg-white focus:ring-2 focus:ring-lime-100" id="searchInput" autocomplete="off">
        <div id="dropdownMenu" class="absolute left-0 right-0 top-full z-50 mt-2 hidden max-h-80 overflow-y-auto rounded-xl border-0 bg-white shadow-[0_2px_8px_rgba(0,0,0,0.12)]">
          <ul id="searchResults" class="divide-y divide-gray-100"></ul>
          <ul id="commandPalette" class="hidden"></ul>
        </div>
      </div>
    </form>
    <div class="ml-auto flex flex-shrink-0 items-center gap-2 sm:gap-3">
      @if(!auth('web')->check() && !auth('vendor')->check() && !auth('admin')->check())
        <button type="button" data-modal-target="authentication-modal" data-modal-show="authentication-modal" class="whitespace-nowrap rounded-full border-0 bg-lime-50 px-3 py-2 text-sm font-medium text-lime-800 hover:bg-lime-100">Sign in</button>
      @else
        @if(auth('web')->check())
          <a href="{{ route('user.profile') }}" class="flex items-center gap-2 text-sm font-medium text-gray-700"><img src="{{ auth('web')->user()->profile_picture ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=96&q=80' }}" alt="" class="h-8 w-8 rounded-full object-cover"><span class="hidden sm:inline">My account</span></a>
        @elseif(auth('vendor')->check())
          <a href="{{ route('vendor.dashboard') }}" class="whitespace-nowrap text-xs font-medium text-lime-700 sm:text-sm">Seller</a>
        @elseif(auth('admin')->check())
          <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-lime-700">Admin</a>
        @endif
        <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="text-xs text-red-600 hover:underline sm:text-sm">Logout</button></form>
      @endif
    </div>
  </div>
</header>
