<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Admin Larashop' }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-stone-100 text-stone-900">
        <div class="min-h-screen lg:grid lg:grid-cols-[280px_minmax(0,1fr)]">
            <aside class="border-r border-stone-200 bg-stone-950 px-6 py-8 text-stone-100">
                <a href="{{ route('admin.dashboard') }}" class="mb-10 flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500 font-bold text-stone-950">LS</div>
                    <div>
                        <p class="font-semibold">Admin Larashop</p>
                        <p class="text-sm text-stone-400">Operasional penjualan & pengiriman</p>
                    </div>
                </a>

                <nav class="space-y-2 text-sm">
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.accounts.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.accounts.*') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        Account
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.customers.*') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        Customer
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.products.*') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        Produk
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.orders.*') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        Pesanan
                    </a>
                    <a href="{{ route('admin.shipments.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.shipments.*') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                        Shipment
                    </a>
                </nav>

                <form method="POST" action="{{ route('admin.logout') }}" class="mt-8">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-stone-200 transition hover:bg-white/10 hover:text-white"
                    >
                        Logout
                    </button>
                </form>
            </aside>

            <main class="min-w-0 px-4 py-6 sm:px-6 lg:px-10 lg:py-8">
                @if (session('success'))
                    <div class="mb-6 rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 rounded-[1.5rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-700">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
