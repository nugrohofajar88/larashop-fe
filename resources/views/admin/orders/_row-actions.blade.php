<details data-row-menu class="relative ml-auto w-fit">
    <summary class="flex h-9 w-9 cursor-pointer list-none items-center justify-center rounded-full border border-stone-200 bg-white text-stone-600 transition hover:border-stone-300 hover:text-stone-900">
        <svg viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5" aria-hidden="true">
            <path d="M10 4.75a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5zm0 6.5a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5zm0 6.5a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5z" />
        </svg>
    </summary>

    <div class="absolute bottom-full right-0 z-50 mb-2 w-52 overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-xl shadow-stone-900/10">
        <a href="{{ route('admin.orders.show', $order['code']) }}" class="block px-4 py-3 text-sm font-medium text-stone-700 transition hover:bg-stone-50">
            Lihat invoice
        </a>

        @if ($order['status'] === 'pending_payment')
            <form method="POST" action="{{ route('admin.orders.validate-payment', $order['code']) }}">
                @csrf
                <button type="submit" class="block w-full px-4 py-3 text-left text-sm font-medium text-stone-700 transition hover:bg-stone-50">
                    Validasi pembayaran
                </button>
            </form>
            <form method="POST" action="{{ route('admin.orders.cancel', $order['code']) }}">
                @csrf
                <button type="submit" class="block w-full px-4 py-3 text-left text-sm font-medium text-rose-700 transition hover:bg-rose-50">
                    Batalkan pesanan
                </button>
            </form>
        @elseif ($order['status'] === 'paid')
            <a href="{{ route('admin.orders.show', $order['code']) }}" class="block px-4 py-3 text-sm font-medium text-stone-700 transition hover:bg-stone-50">
                Jadwalkan pickup
            </a>
            <form method="POST" action="{{ route('admin.orders.cancel', $order['code']) }}">
                @csrf
                <button type="submit" class="block w-full px-4 py-3 text-left text-sm font-medium text-rose-700 transition hover:bg-rose-50">
                    Batalkan pesanan
                </button>
            </form>
        @elseif ($order['status'] === 'processing')
            {{-- Disembunyikan sementara --}}
            {{-- <form method="POST" action="{{ route('admin.orders.process-shipment', $order['code']) }}">
                @csrf
                <button type="submit" class="block w-full px-4 py-3 text-left text-sm font-medium text-stone-700 transition hover:bg-stone-50">
                    Tandai dikirim
                </button>
            </form> --}}
        @elseif ($order['status'] === 'shipped')
            <form method="POST" action="{{ route('admin.orders.complete', $order['code']) }}">
                @csrf
                <button type="submit" class="block w-full px-4 py-3 text-left text-sm font-medium text-stone-700 transition hover:bg-stone-50">
                    Tandai selesai
                </button>
            </form>
        @endif
    </div>
</details>
