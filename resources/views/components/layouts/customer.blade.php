<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Larashop' }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f6f1e7] text-stone-900">
        <div class="relative min-h-screen overflow-x-hidden">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-56 bg-[radial-gradient(circle_at_top,_rgba(56,189,121,0.22),_transparent_55%)]"></div>

            <header class="sticky top-0 z-30 border-b border-stone-200/80 bg-[#f6f1e7]/90 backdrop-blur">
                <div class="mx-auto flex max-w-md items-center justify-between px-4 py-4 sm:max-w-2xl sm:px-6 lg:max-w-6xl lg:px-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-sm font-bold tracking-wide text-white shadow-lg shadow-emerald-900/20">
                            LS
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-stone-950">Larashop</p>
                            <p class="text-xs text-stone-500">Kebutuhan pertanian yang ringkas dan siap kirim</p>
                        </div>
                    </a>
                    <div class="flex items-center gap-2">
                        @if (session('customer.user'))
                            <div class="relative" data-customer-menu>
                                <button
                                    type="button"
                                    class="flex items-center gap-2 rounded-full border border-stone-300 bg-white px-4 py-2 text-sm font-medium text-stone-700 transition hover:border-emerald-500 hover:text-emerald-700"
                                    data-customer-menu-trigger
                                    aria-expanded="false"
                                >
                                    <span>{{ data_get(session('customer.user'), 'username', 'Profil') }}</span>
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div
                                    class="absolute right-0 top-full z-40 mt-3 hidden w-56 overflow-hidden rounded-[1.4rem] border border-stone-200 bg-white shadow-2xl shadow-stone-900/10"
                                    data-customer-menu-panel
                                >
                                    <a href="{{ route('profile') }}" class="block px-5 py-4 text-sm font-medium text-stone-800 transition hover:bg-stone-50">
                                        Akun Saya
                                    </a>
                                    <a href="{{ route('customer.orders') }}" class="block px-5 py-4 text-sm font-medium text-stone-800 transition hover:bg-stone-50">
                                        Pesanan Saya
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="border-t border-stone-100">
                                        @csrf
                                        <button type="submit" class="block w-full px-5 py-4 text-left text-sm font-medium text-stone-800 transition hover:bg-stone-50">
                                            Log Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 transition hover:border-emerald-500 hover:text-emerald-700">
                                Masuk
                            </a>
                        @endif
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-md px-4 pb-24 pt-6 sm:max-w-2xl sm:px-6 lg:max-w-6xl lg:px-8 lg:pb-10">
                @if (session('success'))
                    <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
                @endif
                {{ $slot }}
            </main>

            <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-stone-200 bg-white/95 backdrop-blur lg:hidden">
                <div class="mx-auto grid max-w-md grid-cols-4 px-4 py-3 text-center text-xs font-medium text-stone-500">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') || request()->routeIs('catalog') || request()->routeIs('products.show') ? 'text-emerald-700' : '' }}">Produk</a>
                    <a href="{{ route('cart') }}" class="{{ request()->routeIs('cart') ? 'text-emerald-700' : '' }}">Keranjang</a>
                    <a href="{{ route('customer.orders') }}" class="{{ request()->routeIs('customer.orders') ? 'text-emerald-700' : '' }}">Pesanan</a>
                    <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') || request()->routeIs('addresses') ? 'text-emerald-700' : '' }}">Profil</a>
                </div>
            </nav>
        </div>
    </body>
</html>
