@php
    $username = data_get(session('customer.user'), 'username', 'customer');
    $name = data_get(session('customer.user'), 'name', 'Customer Larashop');
    $initial = strtoupper(substr($username, 0, 1));
@endphp

<div class="space-y-4">
    <nav class="-mx-1 overflow-x-auto pb-1 lg:hidden" aria-label="Navigasi akun customer">
        <div class="flex min-w-full gap-2 px-1">
            <a
                href="{{ route('profile') }}"
                class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('profile') ? 'bg-stone-900 text-white' : 'border border-stone-300 bg-white text-stone-700 hover:border-emerald-500 hover:text-emerald-700' }}"
            >
                Profil
            </a>
            <a
                href="{{ route('addresses') }}"
                class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('addresses') ? 'bg-stone-900 text-white' : 'border border-stone-300 bg-white text-stone-700 hover:border-emerald-500 hover:text-emerald-700' }}"
            >
                Alamat
            </a>
            <a
                href="{{ route('customer.orders') }}"
                class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('customer.orders.*') ? 'bg-stone-900 text-white' : 'border border-stone-300 bg-white text-stone-700 hover:border-emerald-500 hover:text-emerald-700' }}"
            >
                Pesanan Saya
            </a>
        </div>
    </nav>

    <aside class="hidden lg:block">
        <div class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-[linear-gradient(135deg,_#0f9b6d,_#32c28b)] text-base font-bold text-white shadow-lg shadow-emerald-900/15">
                    {{ $initial }}
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-stone-950">{{ $username }}</p>
                    <p class="mt-1 truncate text-sm text-stone-500">{{ $name }}</p>
                </div>
            </div>

            <div class="mt-8 space-y-6">
                <div class="space-y-3">
                    <p class="text-sm font-semibold text-stone-950">Akun Saya</p>
                    <div class="space-y-1">
                        <a
                            href="{{ route('profile') }}"
                            class="block rounded-2xl px-4 py-3 text-sm transition {{ request()->routeIs('profile') ? 'bg-emerald-50 font-semibold text-emerald-700' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}"
                        >
                            Profil
                        </a>
                        <a
                            href="{{ route('addresses') }}"
                            class="block rounded-2xl px-4 py-3 text-sm transition {{ request()->routeIs('addresses') ? 'bg-emerald-50 font-semibold text-emerald-700' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}"
                        >
                            Alamat
                        </a>
                    </div>
                </div>

                <div class="space-y-3">
                    <p class="text-sm font-semibold text-stone-950">Pesanan Saya</p>
                    <div class="space-y-1">
                        <a
                            href="{{ route('customer.orders') }}"
                            class="block rounded-2xl px-4 py-3 text-sm transition {{ request()->routeIs('customer.orders.*') ? 'bg-emerald-50 font-semibold text-emerald-700' : 'text-stone-600 hover:bg-stone-50 hover:text-stone-900' }}"
                        >
                            Riwayat Pesanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </aside>
</div>
