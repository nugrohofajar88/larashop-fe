@php
    $username = data_get(session('customer.user'), 'username', 'customer');
    $name = data_get(session('customer.user'), 'name', 'Customer Larashop');
    $initial = strtoupper(substr($username, 0, 1));
    $isProfile = request()->routeIs('profile');
    $isAddresses = request()->routeIs('addresses');
    $isOrders = request()->routeIs('customer.orders.*') || request()->routeIs('customer.orders');
    $pillActive = 'bg-secondary-container text-on-secondary-container font-bold';
    $pillIdle = 'text-on-surface-variant hover:bg-surface-container-low';
@endphp

<aside class="w-full shrink-0 md:w-80">
    {{-- User summary --}}
    <div class="mb-6 rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-primary to-secondary font-headline-md text-headline-md text-on-primary shadow-lg">
                {{ $initial }}
            </div>
            <div class="min-w-0">
                <h2 class="truncate font-headline-md text-headline-md text-on-surface">{{ $name }}</h2>
                <p class="truncate font-body-sm text-body-sm text-on-surface-variant">{{ '@'.$username }}</p>
            </div>
        </div>
    </div>

    {{-- Desktop sidebar --}}
    <div class="hidden rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-4 soft-warm-shadow md:block">
        <div class="mb-6">
            <p class="mb-3 px-4 font-label-eyebrow text-label-eyebrow uppercase text-outline">Akun Saya</p>
            <nav class="space-y-1">
                <a href="{{ route('profile') }}" class="flex items-center gap-3 rounded-full px-4 py-3 transition-all {{ $isProfile ? $pillActive : $pillIdle }}">
                    <span class="material-symbols-outlined" @if($isProfile) style="font-variation-settings: 'FILL' 1;" @endif>person</span>
                    <span class="font-body-md text-body-md">Profil</span>
                </a>
                <a href="{{ route('addresses') }}" class="flex items-center gap-3 rounded-full px-4 py-3 transition-all {{ $isAddresses ? $pillActive : $pillIdle }}">
                    <span class="material-symbols-outlined" @if($isAddresses) style="font-variation-settings: 'FILL' 1;" @endif>location_on</span>
                    <span class="font-body-md text-body-md">Alamat</span>
                </a>
            </nav>
        </div>
        <div>
            <p class="mb-3 px-4 font-label-eyebrow text-label-eyebrow uppercase text-outline">Pesanan Saya</p>
            <nav class="space-y-1">
                <a href="{{ route('customer.orders') }}" class="flex items-center gap-3 rounded-full px-4 py-3 transition-all {{ $isOrders ? $pillActive : $pillIdle }}">
                    <span class="material-symbols-outlined" @if($isOrders) style="font-variation-settings: 'FILL' 1;" @endif>receipt_long</span>
                    <span class="font-body-md text-body-md">Riwayat Pesanan</span>
                </a>
            </nav>
        </div>
    </div>

    {{-- Mobile pill nav --}}
    <div class="no-scrollbar -mx-1 flex gap-2 overflow-x-auto px-1 pb-2 md:hidden">
        <a href="{{ route('profile') }}" class="flex shrink-0 items-center gap-2 whitespace-nowrap rounded-full px-5 py-2.5 text-sm {{ $isProfile ? $pillActive : 'border border-surface-container-highest bg-surface-container-lowest text-on-surface-variant shadow-sm' }}">
            <span class="material-symbols-outlined text-[20px]">person</span> Profil
        </a>
        <a href="{{ route('addresses') }}" class="flex shrink-0 items-center gap-2 whitespace-nowrap rounded-full px-5 py-2.5 text-sm {{ $isAddresses ? $pillActive : 'border border-surface-container-highest bg-surface-container-lowest text-on-surface-variant shadow-sm' }}">
            <span class="material-symbols-outlined text-[20px]">location_on</span> Alamat
        </a>
        <a href="{{ route('customer.orders') }}" class="flex shrink-0 items-center gap-2 whitespace-nowrap rounded-full px-5 py-2.5 text-sm {{ $isOrders ? $pillActive : 'border border-surface-container-highest bg-surface-container-lowest text-on-surface-variant shadow-sm' }}">
            <span class="material-symbols-outlined text-[20px]">receipt_long</span> Pesanan
        </a>
    </div>
</aside>
