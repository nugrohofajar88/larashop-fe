<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Admin Sobat Akar Tani Kimia' }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo-circle.png') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-stone-100 text-stone-900">
        {{-- Toggle drawer (CSS-only via peer checkbox) --}}
        <input type="checkbox" id="admin-drawer" class="peer sr-only">

        {{-- Overlay (mobile saja) --}}
        <label for="admin-drawer" aria-hidden="true"
            class="fixed inset-0 z-40 hidden bg-stone-950/50 backdrop-blur-sm peer-checked:block lg:hidden"></label>

        {{-- Sidebar / drawer --}}
        <aside class="fixed inset-y-0 left-0 z-50 flex w-72 max-w-[85%] -translate-x-full flex-col overflow-y-auto bg-stone-950 px-6 py-8 text-stone-100 transition-transform duration-300 ease-out peer-checked:translate-x-0 lg:translate-x-0">
            <div class="mb-10 flex items-center justify-between gap-3">
                <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-3">
                    <img src="{{ asset('images/logo-circle.png') }}" alt="Sobat Akar Tani Kimia" class="h-12 w-12 shrink-0 rounded-full object-cover">
                    <div class="min-w-0">
                        <p class="truncate font-semibold">{{ data_get(session('admin.user'), 'name', 'Admin') }}</p>
                        <p class="truncate text-sm text-stone-400">{{ data_get(session('admin.user'), 'email', 'Administrator') }}</p>
                    </div>
                </a>
                {{-- Tombol tutup (mobile) --}}
                <label for="admin-drawer" aria-label="Tutup menu"
                    class="-mr-2 cursor-pointer rounded-xl p-2 text-stone-400 transition hover:bg-white/10 hover:text-white lg:hidden">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </label>
            </div>

            <nav class="space-y-2 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.accounting') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.accounting') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                    Akuntansi
                </a>
                <a href="{{ route('admin.accounts.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.accounts.*') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                    {{ (session('admin.user.is_super_admin') ?? (session('admin.user.admin_role') === 'super_admin')) ? 'Account' : 'Profil Saya' }}
                </a>
                <a href="{{ route('admin.customers.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.customers.*') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                    Customer
                </a>
                <a href="{{ route('admin.products.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.products.*') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                    Produk
                </a>
                <a href="{{ route('admin.categories.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.categories.*') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                    Kategori
                </a>
                <a href="{{ route('admin.orders.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.orders.*') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                    Pesanan
                </a>
                <a href="{{ route('admin.shipments.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.shipments.*') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                    Shipment
                </a>
                <a href="{{ route('admin.payments.settings') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.payments.*') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                    Pembayaran
                </a>
                <a href="{{ route('admin.qris.index') }}" class="block rounded-2xl px-4 py-3 {{ request()->routeIs('admin.qris.*') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5 hover:text-white' }}">
                    QRIS
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

        {{-- Top bar (mobile saja) --}}
        <header class="sticky top-0 z-30 flex items-center gap-3 border-b border-stone-200 bg-white/95 px-4 py-3 backdrop-blur lg:hidden">
            <label for="admin-drawer" aria-label="Buka menu"
                class="-ml-1 cursor-pointer rounded-xl p-2 text-stone-700 transition hover:bg-stone-100">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </label>
            <img src="{{ asset('images/logo-circle.png') }}" alt="" class="h-8 w-8 rounded-full object-cover">
            <span class="truncate font-semibold text-stone-900">Akar Tani Kimia</span>
        </header>

        {{-- Konten --}}
        <main class="min-w-0 px-4 py-6 sm:px-6 lg:ml-72 lg:px-10 lg:py-8">
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

        {{-- Modal konfirmasi global (pengganti confirm() bawaan). Dipicu form[data-confirm]. --}}
        <div data-confirm-modal class="fixed inset-0 z-[60] hidden items-center justify-center bg-stone-950/40 p-4">
            <div class="w-full max-w-sm rounded-3xl border border-stone-200 bg-white p-6 shadow-2xl">
                <h3 data-confirm-title class="text-lg font-semibold text-stone-950">Konfirmasi</h3>
                <p data-confirm-message class="mt-2 text-sm text-stone-600"></p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" data-confirm-cancel class="rounded-2xl border border-stone-300 px-4 py-2.5 text-sm font-semibold text-stone-700 hover:bg-stone-50">Batal</button>
                    <button type="button" data-confirm-ok class="rounded-2xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-700">Ya, lanjut</button>
                </div>
            </div>
        </div>

        {{-- Modal batalkan order + keterangan. Dipicu form[data-cancel-reason-form].
             Keterangannya dikirim ke pembeli lewat email & WA notifikasi pembatalan. --}}
        <div data-cancel-reason-modal class="fixed inset-0 z-[60] hidden items-center justify-center bg-stone-950/40 p-4">
            <div class="w-full max-w-md rounded-3xl border border-stone-200 bg-white p-6 shadow-2xl">
                <h3 class="text-lg font-semibold text-stone-950">Batalkan Order</h3>
                <p class="mt-2 text-sm text-stone-600">Keterangan ini (opsional) akan dikirim ke pembeli lewat email &amp; WA.</p>
                <textarea data-cancel-reason-input rows="3" placeholder="Contoh: Produk sudah tidak dijual lagi, silakan pesan produk lain."
                    class="mt-4 w-full rounded-2xl border border-stone-200 px-4 py-3 text-sm text-stone-800 focus:border-rose-400 focus:outline-none"></textarea>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" data-cancel-reason-cancel class="rounded-2xl border border-stone-300 px-4 py-2.5 text-sm font-semibold text-stone-700 hover:bg-stone-50">Batal</button>
                    <button type="button" data-cancel-reason-ok class="rounded-2xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-700">Ya, Batalkan Order</button>
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

            (function () {
                const modal = document.querySelector('[data-cancel-reason-modal]');
                if (!modal) return;
                const input = modal.querySelector('[data-cancel-reason-input]');
                const okBtn = modal.querySelector('[data-cancel-reason-ok]');
                const cancelBtn = modal.querySelector('[data-cancel-reason-cancel]');
                let pendingForm = null;
                const open = () => { modal.classList.remove('hidden'); modal.classList.add('flex'); document.body.classList.add('overflow-hidden'); input.focus(); };
                const close = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); document.body.classList.remove('overflow-hidden'); pendingForm = null; input.value = ''; };
                document.addEventListener('submit', (e) => {
                    const form = e.target;
                    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-cancel-reason-form')) return;
                    e.preventDefault();
                    pendingForm = form;
                    open();
                });
                okBtn.addEventListener('click', () => {
                    if (!pendingForm) return;
                    let hidden = pendingForm.querySelector('input[name="reason"]');
                    if (!hidden) {
                        hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'reason';
                        pendingForm.appendChild(hidden);
                    }
                    hidden.value = input.value.trim();
                    pendingForm.submit();
                });
                cancelBtn.addEventListener('click', close);
                modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
                document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
            })();
        </script>
    </body>
</html>
