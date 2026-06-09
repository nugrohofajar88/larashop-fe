<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Sobat Akar Tani Kimia' }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo-circle.png') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Literata:ital,opsz,wght@0,7..72,200..900;1,7..72,200..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-body-md text-on-surface">
        @php
            $cartCount = $cartCount ?? 0;
            $isCatalog = request()->routeIs('home') || request()->routeIs('catalog') || request()->routeIs('products.show');
            $isCart = request()->routeIs('cart');
            $isOrders = request()->routeIs('customer.orders') || request()->routeIs('customer.orders.*');
            $isAccount = request()->routeIs('profile') || request()->routeIs('addresses');
            $navActive = 'text-primary border-b-2 border-primary pb-1 font-bold';
            $navIdle = 'text-on-surface-variant hover:text-primary transition-colors';
        @endphp

        {{-- ===== Top navigation bar ===== --}}
        <header class="sticky top-0 z-50 w-full border-b border-outline-variant/20 bg-surface/80 bg-gradient-to-b from-primary/5 to-transparent backdrop-blur-md shadow-[0_4px_12px_rgba(0,108,74,0.08)]">
            <nav class="mx-auto flex h-20 w-full max-w-[1280px] items-center justify-between px-margin-mobile md:px-margin-desktop">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('images/logo.png') }}" alt="Sobat Akar Tani Kimia" class="h-10 w-auto sm:h-11">
                    <span class="hidden font-display text-headline-md font-semibold text-primary sm:inline">Akar Tani Kimia</span>
                </a>

                {{-- Desktop links --}}
                <div class="hidden items-center gap-8 md:flex">
                    <a href="{{ route('home') }}" class="text-label-lg {{ $isCatalog ? $navActive : $navIdle }}">Katalog</a>
                    <a href="{{ route('cart') }}" class="text-label-lg flex items-center gap-1.5 {{ $isCart ? $navActive : $navIdle }}">
                        Keranjang
                        @if ($cartCount > 0)
                            <span class="flex h-5 min-w-[20px] items-center justify-center rounded-full bg-error px-1.5 text-[11px] font-bold text-on-error">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('customer.orders') }}" class="text-label-lg {{ $isOrders ? $navActive : $navIdle }}">Pesanan</a>

                    @if (session('customer.user'))
                        <div class="relative" data-customer-menu>
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-full border border-outline-variant/50 bg-surface-container-lowest py-1.5 pl-1.5 pr-3 text-label-lg text-on-surface transition hover:border-primary"
                                data-customer-menu-trigger
                                aria-expanded="false"
                            >
                                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-primary to-secondary text-xs font-bold text-on-primary">
                                    {{ strtoupper(substr(data_get(session('customer.user'), 'username', 'P'), 0, 1)) }}
                                </span>
                                <span class="max-w-[8rem] truncate">{{ data_get(session('customer.user'), 'username', 'Profil') }}</span>
                                <span class="material-symbols-outlined text-lg text-outline">expand_more</span>
                            </button>

                            <div
                                class="absolute right-0 top-full z-40 mt-3 hidden w-60 overflow-hidden rounded-2xl border border-outline-variant/30 bg-surface-container-lowest shadow-[0_20px_50px_rgba(28,25,23,0.15)]"
                                data-customer-menu-panel
                            >
                                <div class="border-b border-outline-variant/20 px-5 py-4">
                                    <p class="truncate text-body-md font-semibold text-on-surface">{{ data_get(session('customer.user'), 'name', 'Pelanggan') }}</p>
                                    <p class="mt-0.5 truncate text-body-sm text-on-surface-variant">{{ data_get(session('customer.user'), 'username', '') }}</p>
                                </div>
                                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-5 py-3.5 text-body-md font-medium text-on-surface transition hover:bg-surface-container-low">
                                    <span class="material-symbols-outlined text-xl text-outline">person</span> Akun Saya
                                </a>
                                <a href="{{ route('customer.orders') }}" class="flex items-center gap-3 px-5 py-3.5 text-body-md font-medium text-on-surface transition hover:bg-surface-container-low">
                                    <span class="material-symbols-outlined text-xl text-outline">receipt_long</span> Pesanan Saya
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="border-t border-outline-variant/20">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-3 px-5 py-3.5 text-left text-body-md font-medium text-error transition hover:bg-error-container/40">
                                        <span class="material-symbols-outlined text-xl">logout</span> Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg bg-on-background px-6 py-2 text-label-lg text-on-primary transition-all duration-200 ease-in-out hover:bg-primary active:scale-95">Masuk</a>
                    @endif
                </div>

                {{-- Mobile account / login (bottom nav handles primary nav) --}}
                <div class="md:hidden">
                    @if (session('customer.user'))
                        <a href="{{ route('profile') }}" class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-primary to-secondary text-xs font-bold text-on-primary">
                            {{ strtoupper(substr(data_get(session('customer.user'), 'username', 'P'), 0, 1)) }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg bg-on-background px-4 py-1.5 text-body-sm font-medium text-on-primary">Masuk</a>
                    @endif
                </div>
            </nav>
        </header>

        {{-- ===== Main ===== --}}
        <main class="mx-auto w-full max-w-[1280px] px-margin-mobile py-8 md:px-margin-desktop md:py-12 mb-24">
            @if (session('success'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-secondary-container bg-secondary-container/30 px-4 py-3 text-body-sm text-on-secondary-container">
                    <span class="material-symbols-outlined text-xl">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-error-container bg-error-container/40 px-4 py-3 text-body-sm text-on-error-container">
                    <span class="material-symbols-outlined text-xl">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            {{ $slot }}
        </main>

        {{-- ===== Mobile bottom navigation ===== --}}
        <nav class="fixed bottom-0 left-0 z-50 flex w-full items-center justify-around rounded-t-2xl border-t border-outline-variant/30 bg-surface px-4 py-2.5 pb-[max(0.625rem,env(safe-area-inset-bottom))] shadow-[0_-4px_16px_rgba(0,108,74,0.06)] md:hidden">
            <a href="{{ route('home') }}" class="flex flex-col items-center justify-center gap-0.5 rounded-full px-5 py-1 {{ $isCatalog ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined">grid_view</span>
                <span class="text-label-md">Produk</span>
            </a>
            <a href="{{ route('cart') }}" class="relative flex flex-col items-center justify-center gap-0.5 rounded-full px-5 py-1 {{ $isCart ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined">shopping_cart</span>
                <span class="text-label-md">Keranjang</span>
                @if ($cartCount > 0)
                    <span class="absolute right-2 top-0 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-error px-1 text-[10px] font-bold text-on-error">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                @endif
            </a>
            <a href="{{ route('customer.orders') }}" class="flex flex-col items-center justify-center gap-0.5 rounded-full px-5 py-1 {{ $isOrders ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined">receipt_long</span>
                <span class="text-label-md">Pesanan</span>
            </a>
            <a href="{{ route('profile') }}" class="flex flex-col items-center justify-center gap-0.5 rounded-full px-5 py-1 {{ $isAccount ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined">person</span>
                <span class="text-label-md">Profil</span>
            </a>
        </nav>
    </body>
</html>
