<?php

namespace App\Support;

/**
 * Keranjang tamu untuk mode checkout `whatsapp` (lihat config/storefront.php).
 * Disimpan di session (bukan BE API) supaya bisa dipakai tanpa login.
 * Harga/stok di-resolve ulang dari BE tiap kali dibutuhkan (bukan snapshot saat add-to-cart).
 */
class GuestCart
{
    private const SESSION_KEY = 'guest_cart';

    public function __construct(
        private readonly LarashopApi $api,
    ) {
    }

    public function add(int $productId, string $productSlug, int $variantId, int $quantity = 1): void
    {
        $quantity = max(1, $quantity);
        $items = $this->rawItems();

        foreach ($items as $id => $item) {
            if ($item['product_variant_id'] === $variantId) {
                $items[$id]['quantity'] += $quantity;
                $this->save($items);

                return;
            }
        }

        $nextId = $items === [] ? 1 : max(array_keys($items)) + 1;
        $items[$nextId] = [
            'id' => $nextId,
            'product_id' => $productId,
            'product_slug' => $productSlug,
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
        ];

        $this->save($items);
    }

    public function update(int $itemId, int $quantity): bool
    {
        $items = $this->rawItems();

        if (! isset($items[$itemId])) {
            return false;
        }

        $items[$itemId]['quantity'] = max(1, $quantity);
        $this->save($items);

        return true;
    }

    public function remove(int $itemId): void
    {
        $items = $this->rawItems();
        unset($items[$itemId]);
        $this->save($items);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        return count($this->rawItems());
    }

    public function isEmpty(): bool
    {
        return $this->rawItems() === [];
    }

    /** Resolve tiap baris ke data produk/varian terbaru dari BE (harga & stok selalu up-to-date). */
    public function hydrated(): array
    {
        $payloadCache = [];
        $result = [];

        foreach ($this->rawItems() as $item) {
            $slug = $item['product_slug'];

            if (! array_key_exists($slug, $payloadCache)) {
                try {
                    $payloadCache[$slug] = $this->api->publicProduct($slug);
                } catch (LarashopApiException) {
                    $payloadCache[$slug] = null;
                }
            }

            $payload = $payloadCache[$slug];
            $productData = data_get($payload, 'product', []);
            $variant = collect(data_get($productData, 'variants', []))
                ->firstWhere('id', $item['product_variant_id']);

            if ($payload === null || $productData === [] || $variant === null) {
                $result[] = [
                    'id' => $item['id'],
                    'name' => $productData['name'] ?? 'Produk tidak ditemukan',
                    'variant' => '-',
                    'image' => null,
                    'price' => 'Rp0',
                    'price_value' => 0,
                    'stock' => 0,
                    'qty' => $item['quantity'],
                    'subtotal' => 'Rp0',
                    'subtotal_value' => 0,
                    'available' => false,
                ];

                continue;
            }

            $priceValue = (int) preg_replace('/\D/', '', (string) $variant['price']);
            $qty = (int) $item['quantity'];
            $subtotalValue = $priceValue * $qty;

            $result[] = [
                'id' => $item['id'],
                'name' => $productData['name'] ?? '',
                'variant' => $variant['label'] ?? '-',
                'image' => $productData['image'] ?? null,
                'price' => $variant['price'],
                'price_value' => $priceValue,
                'stock' => (int) ($variant['stock'] ?? 0),
                'qty' => $qty,
                'subtotal' => 'Rp'.number_format($subtotalValue, 0, ',', '.'),
                'subtotal_value' => $subtotalValue,
                'available' => true,
            ];
        }

        return $result;
    }

    public function summary(): array
    {
        $available = array_filter($this->hydrated(), fn (array $item) => $item['available']);
        $totalValue = (int) array_sum(array_column($available, 'subtotal_value'));

        return [
            'selected_product_count' => count($available),
            'selected_total_value' => $totalValue,
            'selected_total' => 'Rp'.number_format($totalValue, 0, ',', '.'),
        ];
    }

    /**
     * Format persis sama dengan form order bot WA (lihat WaOrderService::start()
     * di larashop-be) — Nama/No HP/Alamat kosong (diisi pelanggan sebelum kirim),
     * baris "Pesanan:" berisi item siap di-parse otomatis oleh bot tanpa perlu
     * ketik /pesan dulu. Baris "Total (referensi)" aman karena parser bot
     * mengabaikan baris yang diawali "total".
     */
    public function toWhatsappMessage(): string
    {
        $items = array_values(array_filter($this->hydrated(), fn (array $item) => $item['available']));
        $summary = $this->summary();

        $lines = ['Nama: ', 'No HP: ', 'Alamat: ', 'Pesanan:'];

        foreach ($items as $item) {
            $lines[] = sprintf('- %s (%s) x%d', $item['name'], $item['variant'], $item['qty']);
        }

        $lines[] = '';
        $lines[] = 'Total (referensi): '.$summary['selected_total'];

        return implode("\n", $lines);
    }

    private function rawItems(): array
    {
        return session(self::SESSION_KEY, []);
    }

    private function save(array $items): void
    {
        session([self::SESSION_KEY => $items]);
    }
}
