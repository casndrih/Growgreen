@if(Session::has('success') || Session::has('error') || Session::has('warning') || Session::has('info'))
    @php
        $toastType = Session::has('success') ? 'success' : (Session::has('error') ? 'error' : (Session::has('warning') ? 'warning' : 'info'));
        $toastMessage = Session::get($toastType);
        $toastStyles = [
            'success' => ['icon' => '✓', 'surface' => 'bg-lime-50', 'icon_surface' => 'bg-lime-100 text-lime-700', 'text' => 'text-lime-900'],
            'error' => ['icon' => '!', 'surface' => 'bg-red-50', 'icon_surface' => 'bg-red-100 text-red-700', 'text' => 'text-red-900'],
            'warning' => ['icon' => '!', 'surface' => 'bg-amber-50', 'icon_surface' => 'bg-amber-100 text-amber-700', 'text' => 'text-amber-900'],
            'info' => ['icon' => 'i', 'surface' => 'bg-sky-50', 'icon_surface' => 'bg-sky-100 text-sky-700', 'text' => 'text-sky-900'],
        ];
        $toastStyle = $toastStyles[$toastType];
    @endphp
    <div id="toast-default" class="fixed right-4 top-20 z-[70] flex w-[calc(100%-2rem)] max-w-sm items-start gap-3 rounded-xl border-0 {{ $toastStyle['surface'] }} p-4 shadow-[0_2px_6px_rgba(0,0,0,0.12)]" role="{{ $toastType === 'error' ? 'alert' : 'status' }}" aria-live="{{ $toastType === 'error' ? 'assertive' : 'polite' }}">
        <span class="inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full {{ $toastStyle['icon_surface'] }} text-sm font-bold" aria-hidden="true">{{ $toastStyle['icon'] }}</span>
        <p class="min-w-0 flex-1 break-words pt-1 text-sm font-medium {{ $toastStyle['text'] }}">{{ $toastMessage }}</p>
        <button type="button" class="-mr-1 -mt-1 inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border-0 text-gray-500 hover:bg-black/5 focus:outline-none focus:ring-2 focus:ring-lime-400" data-dismiss-target="#toast-default" aria-label="Dismiss notification">
            <span aria-hidden="true">×</span>
        </button>
    </div>
@endif