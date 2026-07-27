<x-layouts.customer title="Sobat Akar Tani Kimia | Checkout">
    <div class="mb-10">
        <x-customer-section-title
            eyebrow="Checkout"
            title="Ringkasan pesanan"
            description="Ongkos kirim dan metode pembayaran akan dikonfirmasi lewat WhatsApp."
        />
    </div>

    @if (empty($items))
        <div class="rounded-3xl border border-dashed border-surface-container-highest bg-surface-container-lowest px-6 py-16 text-center soft-warm-shadow">
            <p class="font-body-md text-on-surface-variant">Keranjang masih kosong.</p>
            <a href="{{ route('cart') }}" class="mt-6 inline-flex rounded-full bg-primary px-6 py-3 font-body-md font-bold text-on-primary transition hover:bg-secondary">Kembali ke keranjang</a>
        </div>
    @else
        <div class="grid grid-cols-1 items-start gap-gutter lg:grid-cols-[1.5fr_1fr]">
            <div class="overflow-hidden rounded-3xl border border-surface-container-highest bg-surface-container-lowest soft-warm-shadow">
                <table class="w-full">
                    <thead class="bg-surface-container-low">
                        <tr class="text-left font-body-sm text-on-surface-variant">
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4 text-center">Qty</th>
                            <th class="px-6 py-4 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container-highest">
                        @foreach ($items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-body-md font-semibold text-on-surface">{{ $item['name'] }}</p>
                                    <p class="font-body-sm text-on-surface-variant">{{ $item['variant'] }}</p>
                                </td>
                                <td class="px-6 py-4 text-center font-body-md text-on-surface">{{ $item['qty'] }}</td>
                                <td class="px-6 py-4 text-right font-body-md font-semibold text-on-surface">{{ $item['subtotal'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <aside class="lg:sticky lg:top-28">
                <div class="rounded-3xl border border-surface-container-highest bg-surface-container-lowest p-6 soft-warm-shadow md:p-8">
                    <h2 class="mb-2 font-headline-md text-headline-md text-on-surface">Total</h2>
                    <div class="flex items-center justify-between border-t border-dashed border-surface-container-highest py-4">
                        <span class="font-body-md text-on-surface">Total harga</span>
                        <span class="font-headline-md text-xl font-bold text-primary">{{ $summary['selected_total'] }}</span>
                    </div>
                    <p class="mb-4 font-body-sm text-body-sm text-on-surface-variant">Tombol di bawah akan membuka WhatsApp dengan pesan pesanan yang sudah terisi.</p>
                    <p class="mb-6 rounded-xl border border-secondary-container bg-secondary-container/20 px-4 py-3 font-body-sm text-body-sm text-on-secondary-container">
                        <strong>Penting:</strong> sebelum menekan kirim di WhatsApp, lengkapi dulu bagian <strong>Nama</strong> dan <strong>Alamat</strong> di pesan tersebut — supaya ongkos kirim bisa langsung dihitung otomatis.
                    </p>

                    <form method="POST" action="{{ route('checkout.place-order') }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-primary px-5 py-4 font-body-md font-bold text-on-primary shadow-lg shadow-primary/20 transition hover:bg-secondary active:scale-95">
                            <span class="material-symbols-outlined">chat</span>
                            Pesan via WhatsApp
                        </button>
                    </form>
                </div>
            </aside>
        </div>
    @endif
</x-layouts.customer>
