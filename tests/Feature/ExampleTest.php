<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Http::preventStrayRequests();
    }

    public function test_catalog_renders_products_from_api(): void
    {
        Http::fake([
            $this->apiUrl('/products*') => Http::response([
                'data' => [
                    [
                        'slug' => 'pestisida-organik',
                        'name' => 'Pestisida Organik',
                        'image' => '/images/products/pestisida-organik.svg',
                        'category' => 'Perlindungan Tanaman',
                        'price' => 'Rp64.000',
                        'original_price' => 'Rp69.000',
                        'discount_badge' => '-7%',
                        'status' => 'Tersedia',
                        'stock' => 'Siap kirim hari ini',
                        'sold_label' => '523 terjual',
                    ],
                ],
                'meta' => [
                    'categories' => ['Perlindungan Tanaman'],
                    'statuses' => ['Tersedia'],
                ],
            ]),
        ]);

        $response = $this->get('/catalog?search=organik&category=Perlindungan+Tanaman');

        $response->assertOk()
            ->assertSee('Pestisida Organik')
            ->assertSee('Menampilkan 1 produk sesuai filter.', false)
            ->assertSee('Cari: organik', false)
            ->assertSee('Kategori: Perlindungan Tanaman', false);
    }

    public function test_product_page_renders_gallery_and_related_products_from_api(): void
    {
        Http::fake([
            $this->apiUrl('/products/pestisida-organik') => Http::response([
                'data' => [
                    'product' => [
                        'id' => 2,
                        'slug' => 'pestisida-organik',
                        'name' => 'Pestisida Organik',
                        'image' => '/images/products/pestisida-organik.svg',
                        'category' => 'Perlindungan Tanaman',
                        'price' => 'Rp64.000',
                        'weight' => '1 liter',
                        'status' => 'Tersedia',
                        'stock' => 16,
                        'badge' => 'Rekomendasi',
                        'description' => 'Formulasi organik untuk perlindungan tanaman.',
                        'highlights' => ['Aplikasi fleksibel'],
                        'images' => [
                            ['id' => 1, 'path' => '/images/products/pestisida-organik.svg', 'alt' => 'Foto utama', 'is_primary' => true],
                            ['id' => 2, 'path' => '/images/products/gallery-usage.svg', 'alt' => 'Cara penggunaan', 'is_primary' => false],
                        ],
                        'default_variant' => [
                            'id' => 21,
                            'label' => '1 liter',
                            'price' => 'Rp64.000',
                            'price_value' => 64000,
                            'stock' => 16,
                            'weight_grams' => 1000,
                            'dimension' => '10 x 10 x 25 cm',
                            'sku' => 'PRD-002-1L',
                            'is_default' => true,
                        ],
                        'variants' => [
                            [
                                'id' => 21,
                                'label' => '1 liter',
                                'price' => 'Rp64.000',
                                'price_value' => 64000,
                                'stock' => 16,
                                'weight_grams' => 1000,
                                'dimension' => '10 x 10 x 25 cm',
                                'sku' => 'PRD-002-1L',
                                'is_default' => true,
                            ],
                            [
                                'id' => 22,
                                'label' => '500 ml',
                                'price' => 'Rp36.000',
                                'price_value' => 36000,
                                'stock' => 10,
                                'weight_grams' => 500,
                                'dimension' => '8 x 8 x 18 cm',
                                'sku' => 'PRD-002-500',
                                'is_default' => false,
                            ],
                        ],
                    ],
                    'related_products' => [
                        [
                            'slug' => 'pupuk-npk-premium',
                            'name' => 'Pupuk NPK Premium',
                            'image' => '/images/products/pupuk-npk-premium.svg',
                            'category' => 'Pupuk',
                            'price' => 'Rp75.000',
                            'weight' => '5 kg',
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->get('/products/pestisida-organik?search=organik');

        $response->assertOk()
            ->assertSee('/images/products/pestisida-organik.svg', false)
            ->assertSee('/images/products/gallery-usage.svg', false)
            ->assertSee('/products/pupuk-npk-premium?search=organik', false)
            ->assertSee('data-gallery-lightbox', false)
            ->assertSee('name="product_variant_id"', false)
            ->assertSee('PRD-002-500');
    }

    public function test_product_page_add_to_cart_submits_selected_variant_to_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/customer/cart/items') => Http::response([
                'data' => [
                    'item' => [
                        'id' => 91,
                        'product_id' => 2,
                        'product_variant_id' => 22,
                    ],
                    'summary' => [
                        'selected_product_count' => 0,
                        'selected_total_value' => 72000,
                        'selected_total' => 'Rp72.000',
                    ],
                ],
            ], 201),
        ]);

        $response = $this->withSession(['customer.token' => 'customer-token'])->post('/cart/items', [
            'product_id' => 2,
            'product_variant_id' => 22,
            'quantity' => 2,
        ]);

        $response->assertRedirect('/cart')
            ->assertSessionHas('success');

        Http::assertSent(function ($request) {
            return $request->url() === $this->apiUrl('/customer/cart/items')
                && $request['product_id'] === 2
                && $request['product_variant_id'] === 22
                && $request['quantity'] === 2
                && $request['selected'] === false;
        });
    }

    public function test_customer_login_uses_backend_auth_and_stores_session(): void
    {
        Http::fake([
            $this->apiUrl('/auth/login') => Http::response([
                'data' => [
                    'token' => 'customer-token',
                    'token_type' => 'Bearer',
                    'user' => [
                        'id' => 2,
                        'code' => 'CST-001',
                        'name' => 'Budi Santoso',
                        'username' => 'budisantoso',
                        'phone' => '081234567890',
                        'role' => 'customer',
                    ],
                ],
            ]),
        ]);

        $response = $this->post('/login', [
            'login' => 'budisantoso',
            'password' => 'password',
        ]);

        $response->assertRedirect('/')
            ->assertSessionHas('customer.token', 'customer-token')
            ->assertSessionHas('customer.user.username', 'budisantoso');
    }

    public function test_storefront_login_rejects_admin_account(): void
    {
        Http::fake([
            $this->apiUrl('/auth/login') => Http::response([
                'data' => [
                    'token' => 'admin-token',
                    'token_type' => 'Bearer',
                    'user' => [
                        'id' => 1,
                        'code' => 'ADM-001',
                        'name' => 'Larashop Admin',
                        'username' => 'adminlarashop',
                        'phone' => '081100000001',
                        'role' => 'admin',
                    ],
                ],
            ]),
            $this->apiUrl('/auth/logout') => Http::response([
                'message' => 'Token berhasil dicabut.',
            ]),
        ]);

        $response = $this->from('/login')->post('/login', [
            'login' => 'adminlarashop',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login')
            ->assertSessionHasErrors('login');
    }

    public function test_checkout_reads_address_and_shipping_from_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/checkout') => Http::response([
                'data' => [
                    'address' => [
                        'id' => 1,
                        'label' => 'Alamat Utama',
                        'name' => 'Budi Santoso',
                        'phone' => '081234567890',
                        'detail' => 'Jl. Melati No. 12, Sukamaju, Kec. Cibungbulang, Bogor, Jawa Barat, 16630',
                        'note' => 'Rumah pagar putih.',
                        'is_primary' => true,
                    ],
                    'addresses' => [
                        [
                            'id' => 1,
                            'label' => 'Alamat Utama',
                            'name' => 'Budi Santoso',
                            'phone' => '081234567890',
                            'detail' => 'Jl. Melati No. 12, Sukamaju, Kec. Cibungbulang, Bogor, Jawa Barat, 16630',
                            'note' => 'Rumah pagar putih.',
                            'is_primary' => true,
                        ],
                        [
                            'id' => 2,
                            'label' => 'Gudang Kebun',
                            'name' => 'Budi Santoso',
                            'phone' => '081234567890',
                            'detail' => 'Kp. Sukamaju RT 02/RW 01, Leuweung Kolot, Kec. Cibungbulang, Bogor, Jawa Barat, 16630',
                            'note' => 'Dekat pasar tani.',
                            'is_primary' => false,
                        ],
                    ],
                    'shipping_options' => [
                        ['id' => 'jnt-ez', 'service' => 'JNT Regular', 'estimate' => '2-4 hari', 'price' => 'Rp18.000', 'price_value' => 18000, 'selected' => true],
                        ['id' => 'jnt-exp', 'service' => 'JNT Express', 'estimate' => '1-2 hari', 'price' => 'Rp26.000', 'price_value' => 26000, 'selected' => false],
                    ],
                    'payment_summary' => [
                        'items_total' => 'Rp178.000',
                        'items_total_value' => 178000,
                        'shipping_total' => 'Rp18.000',
                        'shipping_total_value' => 18000,
                        'unique_code' => 'Rp153',
                        'unique_code_value' => 153,
                        'used_unique_code' => 'Rp0',
                        'used_unique_code_value' => 0,
                        'grand_total' => 'Rp196.153',
                        'grand_total_value' => 196153,
                    ],
                ],
            ]),
        ]);

        $response = $this->withSession(['customer.token' => 'customer-token'])->get('/checkout');

        $response->assertOk()
            ->assertSee('Alamat Utama')
            ->assertSee('Gudang Kebun')
            ->assertSee('JNT Regular')
            ->assertSee('JNT Express')
            ->assertSee('data-summary-grand-total', false);
    }

    public function test_checkout_place_order_submits_to_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/customer/orders') => Http::response([
                'data' => [
                    'code' => 'ORD-004',
                ],
            ], 201),
        ]);

        $response = $this->withSession(['customer.token' => 'customer-token'])->post('/checkout', [
            'address_id' => 1,
            'shipping_option_id' => 'jnt-ez',
            'use_unique_code_balance' => '1',
        ]);

        $response->assertRedirect('/orders/ORD-004')
            ->assertSessionHas('success');
    }

    public function test_customer_can_cancel_pending_payment_order_from_storefront(): void
    {
        Http::fake([
            $this->apiUrl('/customer/orders/ORD-004/cancel') => Http::response([
                'data' => [
                    'code' => 'ORD-004',
                    'status' => 'cancelled',
                ],
            ]),
        ]);

        $response = $this->withSession(['customer.token' => 'customer-token'])->post('/orders/ORD-004/cancel');

        $response->assertRedirect('/orders/ORD-004')
            ->assertSessionHas('success');
    }

    public function test_customer_can_complete_shipped_order_from_storefront(): void
    {
        Http::fake([
            $this->apiUrl('/customer/orders/ORD-003/complete') => Http::response([
                'data' => [
                    'code' => 'ORD-003',
                    'status' => 'completed',
                ],
            ]),
        ]);

        $response = $this->withSession(['customer.token' => 'customer-token'])->post('/orders/ORD-003/complete');

        $response->assertRedirect('/orders/ORD-003')
            ->assertSessionHas('success');
    }

    public function test_cancelled_order_detail_shows_cancelled_state_notice(): void
    {
        Http::fake([
            $this->apiUrl('/customer/orders/ORD-004') => Http::response([
                'data' => [
                    'code' => 'ORD-004',
                    'date' => '16 Mei 2026',
                    'status' => 'cancelled',
                    'status_label' => 'Dibatalkan',
                    'items' => [
                        ['name' => 'Pupuk NPK Premium', 'variant' => '1 kg', 'qty' => 1, 'subtotal' => 'Rp75.000'],
                    ],
                    'shipping' => [
                        'service' => 'J&T Express - Reguler',
                        'estimate' => '2 hari',
                        'address' => 'Jl. Melati No. 12',
                        'awb' => null,
                    ],
                    'payment' => [
                        'items_total' => 'Rp75.000',
                        'shipping_total' => 'Rp16.000',
                        'unique_code' => 'Rp153',
                        'used_unique_code' => 'Rp0',
                        'grand_total' => 'Rp91.153',
                    ],
                ],
            ]),
        ]);

        $response = $this->withSession(['customer.token' => 'customer-token'])->get('/orders/ORD-004');

        $response->assertOk()
            ->assertSee('Pesanan ini sudah dibatalkan.')
            ->assertSee('Dibatalkan');
    }

    public function test_profile_redirects_to_login_when_session_token_is_not_customer(): void
    {
        Http::fake([
            $this->apiUrl('/customer/profile') => Http::response([
                'message' => 'You are not allowed to access this resource.',
            ], 403),
        ]);

        $response = $this->withSession([
            'customer.token' => 'admin-token',
            'customer.user' => [
                'role' => 'admin',
            ],
        ])->get('/profile');

        $response->assertRedirect('/login')
            ->assertSessionHas('error');

        $this->assertNull(session('customer.token'));
    }

    public function test_profile_update_submits_to_backend_api_and_refreshes_session_user(): void
    {
        Http::fake([
            $this->apiUrl('/customer/profile') => Http::response([
                'data' => [
                    'id' => 2,
                    'code' => 'CST-001',
                    'name' => 'Budi Santoso Baru',
                    'username' => 'budisantoso',
                    'email' => 'budi.baru@larashop.test',
                    'phone' => '081234567890',
                    'status' => 'Aktif',
                    'role' => 'customer',
                ],
                'message' => 'Profil customer berhasil diperbarui.',
            ]),
        ]);

        $response = $this->withSession([
            'customer.token' => 'customer-token',
            'customer.user' => [
                'id' => 2,
                'code' => 'CST-001',
                'name' => 'Budi Santoso',
                'username' => 'budisantoso',
                'email' => 'budi@lama.test',
                'phone' => '081234567890',
                'role' => 'customer',
            ],
        ])->put('/profile', [
            'name' => 'Budi Santoso Baru',
            'username' => 'budisantoso',
            'email' => 'budi.baru@larashop.test',
            'phone' => '081234567890',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect('/profile')
            ->assertSessionHas('success')
            ->assertSessionHas('customer.user.name', 'Budi Santoso Baru')
            ->assertSessionHas('customer.user.email', 'budi.baru@larashop.test');
    }

    public function test_addresses_page_shows_address_actions(): void
    {
        Http::fake([
            $this->apiUrl('/customer/addresses') => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'label' => 'Alamat Utama',
                        'name' => 'Budi Santoso',
                        'phone' => '081234567890',
                        'province' => 'Jawa Barat',
                        'city' => 'Bogor',
                        'district' => 'Cibungbulang',
                        'subdistrict' => 'Sukamaju',
                        'postal_code' => '16630',
                        'address_line' => 'Jl. Melati No. 12',
                        'note' => 'Rumah pagar putih.',
                        'detail' => 'Jl. Melati No. 12, Sukamaju, Kec. Cibungbulang, Bogor, Jawa Barat, 16630',
                        'is_primary' => true,
                    ],
                    [
                        'id' => 2,
                        'label' => 'Gudang Kebun',
                        'name' => 'Budi Santoso',
                        'phone' => '081234567890',
                        'province' => 'Jawa Barat',
                        'city' => 'Bogor',
                        'district' => 'Cibungbulang',
                        'subdistrict' => 'Leuweung Kolot',
                        'postal_code' => '16630',
                        'address_line' => 'Kp. Sukamaju RT 02/RW 01',
                        'note' => 'Dekat pasar tani.',
                        'detail' => 'Kp. Sukamaju RT 02/RW 01, Leuweung Kolot, Kec. Cibungbulang, Bogor, Jawa Barat, 16630',
                        'is_primary' => false,
                    ],
                ],
            ]),
        ]);

        $response = $this->withSession(['customer.token' => 'customer-token'])->get('/addresses');

        $response->assertOk()
            ->assertSee('Tambah alamat baru')
            ->assertSee('Jadikan utama')
            ->assertSee('Edit')
            ->assertSee('action="'.route('addresses.save').'"', false);
    }

    public function test_address_create_submits_to_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/customer/addresses') => Http::response([
                'data' => [
                    'id' => 3,
                    'label' => 'Cabang Kebun',
                    'name' => 'Budi Santoso',
                    'phone' => '081234567890',
                ],
            ], 201),
        ]);

        $response = $this->withSession(['customer.token' => 'customer-token'])->post('/addresses', [
            'label' => 'Cabang Kebun',
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '081234567890',
            'province' => 'Jawa Barat',
            'city' => 'Bogor',
            'district' => 'Cibungbulang',
            'subdistrict' => 'Galuga',
            'postal_code' => '16630',
            'address_line' => 'Jl. Kebun Raya Blok C1',
            'note' => 'Samping kios bibit.',
        ]);

        $response->assertRedirect('/addresses')
            ->assertSessionHas('success');
    }

    public function test_admin_products_index_reads_from_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/auth/login') => Http::response([
                'data' => ['token' => 'admin-token'],
            ]),
            $this->apiUrl('/admin/products*') => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'sku' => 'PRD-001',
                        'slug' => 'pupuk-npk-premium',
                        'name' => 'Pupuk NPK Premium',
                        'image' => '/images/products/pupuk-npk-premium.svg',
                        'category' => 'Pupuk',
                        'category_slug' => 'pupuk',
                        'price' => 'Rp75.000',
                        'price_value' => 75000,
                        'stock' => 48,
                        'weight_label' => '5 kg',
                        'public_status' => 'active',
                        'status' => 'Aktif',
                        'description' => 'Pupuk majemuk',
                        'images' => [],
                    ],
                ],
            ]),
        ]);

        $response = $this->withSession($this->adminSession())->get('/admin/products');

        $response->assertOk()
            ->assertSee('Pupuk NPK Premium')
            ->assertSee('/images/products/pupuk-npk-premium.svg', false);
    }

    public function test_admin_product_store_submits_to_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/auth/login') => Http::response([
                'data' => ['token' => 'admin-token'],
            ]),
            $this->apiUrl('/admin/products') => Http::response([
                'data' => [
                    'id' => 9,
                    'sku' => 'PRD-900',
                    'name' => 'Sprayer Mini',
                ],
            ], 201),
        ]);

        $response = $this->withSession($this->adminSession())->post('/admin/products', [
            'name' => 'Sprayer Mini',
            'sku' => 'PRD-900',
            'category' => 'perlindungan-tanaman',
            'status' => 'active',
            'description' => 'Sprayer mini',
            'price' => '55000',
            'stock' => 9,
            'unit' => '1 unit',
            'weight' => '1',
            'length' => '10',
            'width' => '10',
            'height' => '20',
        ]);

        $response->assertRedirect('/admin/products/PRD-900')
            ->assertSessionHas('success');
    }

    public function test_admin_customers_index_reads_from_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/auth/login') => Http::response([
                'data' => ['token' => 'admin-token'],
            ]),
            $this->apiUrl('/admin/customers*') => Http::response([
                'data' => [
                    [
                        'id' => 3,
                        'code' => 'CST-003',
                        'name' => 'Mitra Sawah',
                        'username' => 'mitrasawah',
                        'phone' => '081988887123',
                        'status' => 'Menunggu verifikasi',
                        'status_key' => 'pending_verification',
                        'address_count' => 1,
                    ],
                ],
            ]),
        ]);

        $response = $this->withSession($this->adminSession())->get('/admin/customers?search=mitrasawah');

        $response->assertOk()
            ->assertSee('Mitra Sawah')
            ->assertSee('Menunggu verifikasi');
    }

    public function test_admin_can_create_customer_via_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/auth/login') => Http::response([
                'data' => ['token' => 'admin-token'],
            ]),
            $this->apiUrl('/admin/customers') => Http::response([
                'data' => [
                    'id' => 7,
                    'code' => 'CST-007',
                    'name' => 'Toko Tani Makmur',
                ],
            ], 201),
        ]);

        $response = $this->withSession($this->adminSession())->post('/admin/customers', [
            'name' => 'Toko Tani Makmur',
            'username' => 'tokotanimakmur',
            'phone' => '081299887766',
            'status' => 'active',
            'shipping_addresses' => json_encode([
                [
                    'id' => 'addr-new-1',
                    'label' => 'Toko',
                    'recipient_name' => 'Toko Tani Makmur',
                    'recipient_phone' => '081299887766',
                    'province' => 'Jawa Barat',
                    'city' => 'Bandung',
                    'district' => 'Lembang',
                    'subdistrict' => 'Jayagiri',
                    'postal_code' => '40391',
                    'address_line' => 'Jl. Raya Lembang No. 88',
                    'address_note' => 'Ruko dekat pertigaan utama.',
                    'is_primary' => true,
                ],
            ]),
            'password' => 'Password123',
        ]);

        $response->assertRedirect('/admin/customers/CST-007')
            ->assertSessionHas('success');
    }

    public function test_customer_orders_read_from_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/customer/orders') => Http::response([
                'data' => [
                    [
                        'code' => 'ORD-002',
                        'date' => '14 Mei 2026',
                        'status' => 'pending_payment',
                        'status_label' => 'Belum bayar',
                        'total' => 'Rp81.500',
                    ],
                    [
                        'code' => 'ORD-003',
                        'date' => '15 Mei 2026',
                        'status' => 'shipped',
                        'status_label' => 'Shipped',
                        'total' => 'Rp93.000',
                    ],
                ],
            ]),
        ]);

        $response = $this->withSession(['customer.token' => 'customer-token'])->get('/orders');

        $response->assertOk()
            ->assertSee('Belum bayar')
            ->assertSee('ORD-003')
            ->assertSee('Shipped');
    }

    public function test_customer_orders_can_be_filtered_with_status_tabs(): void
    {
        Http::fake([
            $this->apiUrl('/customer/orders') => Http::response([
                'data' => [
                    [
                        'code' => 'ORD-002',
                        'date' => '14 Mei 2026',
                        'status' => 'pending_payment',
                        'status_label' => 'Belum bayar',
                        'total' => 'Rp81.500',
                    ],
                    [
                        'code' => 'ORD-003',
                        'date' => '15 Mei 2026',
                        'status' => 'shipped',
                        'status_label' => 'Dikirim',
                        'total' => 'Rp93.000',
                    ],
                ],
            ]),
        ]);

        $response = $this->withSession(['customer.token' => 'customer-token'])->get('/orders?status=pending_payment');

        $response->assertOk()
            ->assertSee('ORD-002')
            ->assertDontSee('ORD-003')
            ->assertSee('Belum bayar');
    }

    public function test_admin_accounts_index_reads_from_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/auth/login') => Http::response([
                'data' => ['token' => 'admin-token'],
            ]),
            $this->apiUrl('/admin/accounts*') => Http::response([
                'data' => [
                    [
                        'id' => 'ADM-001',
                        'user_id' => 1,
                        'name' => 'Larashop Admin',
                        'username' => 'adminlarashop',
                        'email' => 'admin@larashop.test',
                        'phone' => '081100000001',
                        'role' => 'Super Admin',
                        'status' => 'Aktif',
                        'last_login' => '15 Mei 2026, 08:10',
                        'note' => 'Memegang akses penuh dashboard.',
                    ],
                ],
            ]),
        ]);

        $response = $this->withSession($this->adminSession())->get('/admin/accounts');

        $response->assertOk()
            ->assertSee('Larashop Admin')
            ->assertSee('Super Admin');
    }

    public function test_admin_can_create_account_via_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/auth/login') => Http::response([
                'data' => ['token' => 'admin-token'],
            ]),
            $this->apiUrl('/admin/accounts') => Http::response([
                'data' => [
                    'id' => 'ADM-004',
                    'user_id' => 4,
                    'name' => 'Nadia Operasional',
                ],
            ], 201),
        ]);

        $response = $this->withSession($this->adminSession())->post('/admin/accounts', [
            'name' => 'Nadia Operasional',
            'username' => 'nadiaops',
            'email' => 'nadia.ops@larashop.test',
            'phone' => '081244455566',
            'role' => 'Admin Operasional',
            'status' => 'Aktif',
            'password' => 'Password123',
        ]);

        $response->assertRedirect('/admin/accounts/ADM-004/edit')
            ->assertSessionHas('success');
    }

    public function test_admin_can_delete_account_via_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/admin/accounts*') => Http::response([
                'data' => [
                    [
                        'id' => 'ADM-004',
                        'user_id' => 4,
                        'name' => 'Nadia Operasional',
                        'username' => 'nadiaops',
                        'email' => 'nadia.ops@larashop.test',
                        'phone' => '081244455566',
                        'role' => 'Admin Operasional',
                        'role_key' => 'operational_admin',
                        'status' => 'Aktif',
                        'last_login' => '-',
                        'note' => 'Fokus pada validasi order dan pembaruan katalog.',
                    ],
                ],
            ]),
            $this->apiUrl('/admin/accounts/4') => Http::response([
                'message' => 'Account admin berhasil dihapus.',
            ]),
        ]);

        $response = $this->withSession($this->adminSession() + ['admin.token' => 'admin-token'])->delete('/admin/accounts/ADM-004');

        $response->assertRedirect('/admin/accounts')
            ->assertSessionHas('success');
    }

    public function test_admin_orders_and_shipments_read_from_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/auth/login') => Http::response([
                'data' => ['token' => 'admin-token'],
            ]),
            $this->apiUrl('/admin/orders') => Http::response([
                'data' => [
                    [
                        'id' => 3,
                        'code' => 'ORD-003',
                        'date' => '15 Mei 2026',
                        'status' => 'shipped',
                        'status_label' => 'Shipped',
                        'total' => 'Rp93.000',
                        'customer' => 'Budi Santoso',
                        'phone' => '081234567890',
                        'payment_status' => 'Tervalidasi',
                        'shipping_service' => 'JNT Regular',
                        'shipping_estimate' => '2-4 hari',
                        'awb' => 'JNT00123456789',
                    ],
                ],
            ]),
            $this->apiUrl('/admin/shipments') => Http::response([
                'data' => [
                    [
                        'code' => 'SHP-ORD-003',
                        'order_code' => 'ORD-003',
                        'customer' => 'Budi Santoso',
                        'courier' => 'JNT Regular',
                        'awb' => 'JNT00123456789',
                        'status' => 'in_transit',
                        'note' => 'Paket sudah dijemput kurir.',
                    ],
                ],
            ]),
            $this->apiUrl('/admin/shipment-settings') => Http::response([
                'data' => [
                    'label' => 'Gudang Utama Malang',
                    'contact_name' => 'Tim Gudang Larashop',
                    'contact_phone' => '0341123456',
                    'origin_id' => 47071,
                    'province' => 'JAWA TIMUR',
                    'city' => 'MALANG',
                    'district' => 'KEPANJEN',
                    'subdistrict' => 'ARDIREJO',
                    'postal_code' => '65163',
                    'address_line' => 'Jl. Raya Kebun No. 8',
                    'note' => 'Origin default.',
                ],
            ]),
        ]);

        $ordersResponse = $this->withSession($this->adminSession())->get('/admin/orders');
        $shipmentsResponse = $this->withSession($this->adminSession())->get('/admin/shipments');

        $ordersResponse->assertOk()
            ->assertSee('ORD-003')
            ->assertSee('Shipped');

        $shipmentsResponse->assertOk()
            ->assertSee('SHP-ORD-003')
            ->assertSee('JNT00123456789');
    }

    public function test_admin_orders_can_be_filtered_with_status_tabs(): void
    {
        Http::fake([
            $this->apiUrl('/auth/login') => Http::response([
                'data' => ['token' => 'admin-token'],
            ]),
            $this->apiUrl('/admin/orders') => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'code' => 'ORD-001',
                        'date' => '14 Mei 2026',
                        'status' => 'pending_payment',
                        'status_label' => 'Belum bayar',
                        'total' => 'Rp81.500',
                        'customer' => 'Budi Santoso',
                        'phone' => '081234567890',
                        'payment_status' => 'Menunggu transfer',
                        'shipping_service' => 'JNT Regular',
                        'shipping_estimate' => '2-4 hari',
                        'awb' => null,
                    ],
                    [
                        'id' => 2,
                        'code' => 'ORD-003',
                        'date' => '15 Mei 2026',
                        'status' => 'shipped',
                        'status_label' => 'Dikirim',
                        'total' => 'Rp93.000',
                        'customer' => 'Budi Santoso',
                        'phone' => '081234567890',
                        'payment_status' => 'Tervalidasi',
                        'shipping_service' => 'JNT Regular',
                        'shipping_estimate' => '2-4 hari',
                        'awb' => 'JNT00123456789',
                    ],
                ],
            ]),
        ]);

        $response = $this->withSession($this->adminSession())->get('/admin/orders?status=pending_payment');

        $response->assertOk()
            ->assertSee('ORD-001')
            ->assertDontSee('ORD-003')
            ->assertSee('Belum bayar (1)');
    }

    public function test_admin_can_complete_order_via_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/auth/login') => Http::response([
                'data' => ['token' => 'admin-token'],
            ]),
            $this->apiUrl('/admin/orders') => Http::response([
                'data' => [
                    [
                        'id' => 3,
                        'code' => 'ORD-003',
                    ],
                ],
            ]),
            $this->apiUrl('/admin/orders/3') => Http::response([
                'data' => [
                    'id' => 3,
                    'code' => 'ORD-003',
                ],
            ]),
            $this->apiUrl('/admin/orders/3/complete') => Http::response([
                'data' => [
                    'code' => 'ORD-003',
                    'status' => 'completed',
                ],
            ]),
        ]);

        $response = $this->withSession($this->adminSession())->post('/admin/orders/ORD-003/complete');

        $response->assertRedirect('/admin/orders/ORD-003')
            ->assertSessionHas('success');
    }

    public function test_admin_can_cancel_order_via_backend_api(): void
    {
        Http::fake([
            $this->apiUrl('/auth/login') => Http::response([
                'data' => ['token' => 'admin-token'],
            ]),
            $this->apiUrl('/admin/orders') => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'code' => 'ORD-001',
                    ],
                ],
            ]),
            $this->apiUrl('/admin/orders/1') => Http::response([
                'data' => [
                    'id' => 1,
                    'code' => 'ORD-001',
                ],
            ]),
            $this->apiUrl('/admin/orders/1/cancel') => Http::response([
                'data' => [
                    'code' => 'ORD-001',
                    'status' => 'cancelled',
                ],
            ]),
        ]);

        $response = $this->withSession($this->adminSession())->post('/admin/orders/ORD-001/cancel');

        $response->assertRedirect('/admin/orders/ORD-001')
            ->assertSessionHas('success');
    }

    public function test_admin_can_logout_and_clear_cached_token(): void
    {
        Cache::put('larashop-fe.admin-token', 'admin-token', now()->addHour());

        Http::fake([
            $this->apiUrl('/auth/logout') => Http::response([
                'message' => 'Token berhasil dicabut.',
            ]),
        ]);

        $response = $this->withSession($this->adminSession() + ['admin.token' => 'admin-token'])->post('/admin/logout');

        $response->assertRedirect('/admin/login')
            ->assertSessionHas('success');

        $this->assertNull(Cache::get('larashop-fe.admin-token'));
    }

    protected function apiUrl(string $path): string
    {
        return rtrim((string) config('services.larashop_api.base_url'), '/').'/'.ltrim($path, '/');
    }

    protected function adminSession(): array
    {
        return [
            'admin.authenticated' => true,
            'admin.user' => [
                'role' => 'admin',
                'username' => 'adminlarashop',
            ],
        ];
    }
}
