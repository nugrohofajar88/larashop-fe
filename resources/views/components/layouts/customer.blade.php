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
            $isAccount = request()->routeIs('profile') || request()->routeIs('addresses') || request()->routeIs('customer.orders') || request()->routeIs('customer.orders.*');
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
                    <a href="{{ route('home') }}#katalog" class="text-label-lg {{ $isCatalog ? $navActive : $navIdle }}">Katalog</a>
                    <a href="{{ route('home') }}#tentang" class="text-label-lg {{ $navIdle }}">Tentang</a>
                    <a href="{{ route('cart') }}" class="text-label-lg flex items-center gap-1.5 {{ $isCart ? $navActive : $navIdle }}">
                        Keranjang
                        <span data-cart-badge class="flex h-5 min-w-[20px] items-center justify-center rounded-full bg-error px-1.5 text-[11px] font-bold text-on-error {{ $cartCount > 0 ? '' : 'hidden' }}">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
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
            <a href="{{ route('home') }}#katalog" class="flex flex-col items-center justify-center gap-0.5 rounded-full px-5 py-1 {{ $isCatalog ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined">grid_view</span>
                <span class="text-label-md">Produk</span>
            </a>
            <a href="{{ route('cart') }}" class="relative flex flex-col items-center justify-center gap-0.5 rounded-full px-5 py-1 {{ $isCart ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined">shopping_cart</span>
                <span class="text-label-md">Keranjang</span>
                <span data-cart-badge class="absolute right-2 top-0 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-error px-1 text-[10px] font-bold text-on-error {{ $cartCount > 0 ? '' : 'hidden' }}">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
            </a>
            <a href="{{ route('profile') }}" class="flex flex-col items-center justify-center gap-0.5 rounded-full px-5 py-1 {{ $isAccount ? 'bg-secondary-container text-on-secondary-container' : 'text-on-surface-variant' }}">
                <span class="material-symbols-outlined">person</span>
                <span class="text-label-md">Profil</span>
            </a>
            <a href="{{ route('home') }}#tentang" class="flex flex-col items-center justify-center gap-0.5 rounded-full px-5 py-1 text-on-surface-variant">
                <span class="material-symbols-outlined">info</span>
                <span class="text-label-md">Tentang</span>
            </a>
        </nav>

        {{-- Tombol WhatsApp melayang → chat toko. Muncul kalau nomor WA toko diisi. --}}
        @if (! empty($storeWhatsapp))
            <a href="https://wa.me/{{ $storeWhatsapp }}?text={{ urlencode('Halo Akar Tani Kimia, saya mau bertanya.') }}"
               target="_blank" rel="noopener" aria-label="Chat WhatsApp toko"
               class="fixed bottom-24 right-4 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg ring-1 ring-black/5 transition hover:scale-105 md:bottom-6 md:right-6">
                <svg viewBox="0 0 24 24" class="h-7 w-7" fill="currentColor" aria-hidden="true">
                    <path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.945C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 018.413 3.488 11.824 11.824 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884a9.86 9.86 0 001.51 5.26l-.999 3.648 3.589-.943zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.247-.694.247-1.289.173-1.413z"/>
                </svg>
            </a>
        @endif

        {{-- Modal konfirmasi global (pengganti confirm() bawaan). Dipicu form[data-confirm]. --}}
        <div data-confirm-modal class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40 p-4 backdrop-blur-sm">
            <div class="w-full max-w-sm rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
                <h3 data-confirm-title class="font-headline-md text-lg font-bold text-on-surface">Konfirmasi</h3>
                <p data-confirm-message class="mt-2 font-body-sm text-sm text-on-surface-variant"></p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" data-confirm-cancel class="rounded-2xl border border-surface-container-highest px-4 py-2.5 text-sm font-semibold text-on-surface">Batal</button>
                    <button type="button" data-confirm-ok class="rounded-2xl bg-error px-4 py-2.5 text-sm font-semibold text-on-error">Ya, lanjut</button>
                </div>
            </div>
        </div>
        <script>
            (function () {
                const modal = document.querySelector('[data-confirm-modal]');
                if (!modal) return;
                const titleEl = modal.querySelector('[data-confirm-title]');
                const msgEl = modal.querySelector('[data-confirm-message]');
                const okBtn = modal.querySelector('[data-confirm-ok]');
                const cancelBtn = modal.querySelector('[data-confirm-cancel]');
                let pendingForm = null;
                const open = () => { modal.classList.remove('hidden'); modal.classList.add('flex'); document.body.classList.add('overflow-hidden'); };
                const close = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.classList.remove('overflow-hidden'); pendingForm = null; };
                document.addEventListener('submit', (e) => {
                    const form = e.target;
                    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm')) return;
                    e.preventDefault();
                    pendingForm = form;
                    titleEl.textContent = form.dataset.confirmTitle || 'Konfirmasi';
                    msgEl.textContent = form.dataset.confirm || 'Lanjutkan tindakan ini?';
                    okBtn.textContent = form.dataset.confirmOk || 'Ya, lanjut';
                    open();
                });
                okBtn.addEventListener('click', () => { if (pendingForm) pendingForm.submit(); });
                cancelBtn.addEventListener('click', close);
                modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
                document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
            })();
        </script>

        @if (config('storefront.checkout_mode') === 'whatsapp')
            {{-- Toast notifikasi quick-add (mode WhatsApp). --}}
            <div data-quick-add-toast
                data-quick-add-csrf="{{ csrf_token() }}"
                data-quick-add-endpoint="{{ route('cart.items.store') }}"
                data-quick-add-placeholder="{{ asset('images/logo-circle.png') }}"
                aria-live="polite"
                class="pointer-events-none fixed inset-x-4 top-4 z-[70] flex flex-col items-center gap-2 sm:inset-x-auto sm:right-4 sm:items-end">
            </div>

            {{-- Modal pilih varian quick-add (mode WhatsApp). --}}
            <div data-quick-add-modal class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40 p-4 backdrop-blur-sm">
                <div class="w-full max-w-md rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow">
                    <div class="flex items-start gap-4">
                        <img data-quick-add-modal-image class="h-16 w-16 shrink-0 rounded-xl bg-surface-container object-cover" alt="">
                        <div class="flex-1">
                            <h3 data-quick-add-modal-title class="font-headline-md text-lg font-bold text-on-surface"></h3>
                            <p class="mt-0.5 text-xs text-on-surface-variant">Pilih varian sebelum menambah ke keranjang.</p>
                        </div>
                        <button type="button" data-quick-add-modal-close class="text-outline"><span class="material-symbols-outlined">close</span></button>
                    </div>
                    <div data-quick-add-modal-variants class="mt-5 max-h-64 space-y-2 overflow-y-auto pr-1"></div>
                    <div class="mt-5 flex items-center justify-between rounded-2xl bg-surface-container-low px-4 py-2">
                        <span class="text-sm font-semibold text-on-surface">Jumlah</span>
                        <div class="flex items-center rounded-full bg-surface-container-lowest px-2 py-1 soft-warm-shadow">
                            <button type="button" data-quick-add-modal-decrease class="flex h-8 w-8 items-center justify-center rounded-full disabled:opacity-40">−</button>
                            <span data-quick-add-modal-qty class="w-10 text-center text-sm font-bold">1</span>
                            <button type="button" data-quick-add-modal-increase class="flex h-8 w-8 items-center justify-center rounded-full disabled:opacity-40">+</button>
                        </div>
                    </div>
                    <p data-quick-add-modal-error class="mt-3 hidden text-body-sm text-error"></p>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" data-quick-add-modal-cancel class="rounded-2xl border border-surface-container-highest px-4 py-2.5 text-sm font-semibold text-on-surface">Batal</button>
                        <button type="button" data-quick-add-modal-confirm class="rounded-2xl bg-primary px-5 py-2.5 text-sm font-semibold text-on-primary">Tambah ke Keranjang</button>
                    </div>
                </div>
            </div>
        @endif
    </body>
</html>
