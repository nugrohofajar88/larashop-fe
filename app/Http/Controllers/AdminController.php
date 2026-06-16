<?php

namespace App\Http\Controllers;

use App\Support\LarashopApi;
use App\Support\LarashopApiException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(
        private readonly LarashopApi $api,
    ) {
    }

    public function dashboard(): View
    {
        return view('admin.dashboard.index', [
            'dashboard' => $this->api->adminDashboard(),
        ]);
    }

    public function login(): View|RedirectResponse
    {
        if (session('admin.authenticated')) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        try {
            $payload = $this->api->login($validated['login'], $validated['password'], 'larashop-fe-admin-panel');
        } catch (LarashopApiException $exception) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors($exception->errors !== [] ? $exception->errors : ['login' => $exception->getMessage()]);
        }

        if (data_get($payload, 'user.role') !== 'admin') {
            if (! empty($payload['token'])) {
                try {
                    $this->api->logout($payload['token']);
                } catch (LarashopApiException) {
                    // Abaikan revoke token jika backend sudah menolak.
                }
            }

            return back()
                ->withInput($request->except('password'))
                ->withErrors(['login' => 'Akun ini bukan akun admin.']);
        }

        session([
            'admin.authenticated' => true,
            'admin.token' => $payload['token'],
            'admin.user' => $payload['user'],
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Login admin berhasil.');
    }

    public function logout(): RedirectResponse
    {
        $this->api->logoutAdmin();

        return redirect()->route('admin.login')->with('success', 'Session admin frontend berhasil dikeluarkan.');
    }

    public function accounts(Request $request): View|RedirectResponse
    {
        // Non-super tidak punya daftar akun — diarahkan ke profilnya sendiri.
        if (! $this->isSuperAdmin()) {
            return redirect()->route('admin.accounts.edit', session('admin.user.code'));
        }

        $accounts = collect($this->api->adminAccounts(['search' => $request->string('search')->toString()]));
        $search = trim($request->string('search')->toString());
        $role = $request->string('role')->toString();
        $status = $request->string('status')->toString();

        if ($role !== '' && $role !== 'all') {
            $accounts = $accounts->where('role', $role);
        }

        if ($status !== '' && $status !== 'all') {
            $accounts = $accounts->where('status', $status);
        }

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $accounts = $accounts->filter(fn (array $account) => str_contains(mb_strtolower($account['name']), $needle) || str_contains(mb_strtolower($account['email']), $needle));
        }

        return view('admin.accounts.index', [
            'accounts' => $accounts->values()->all(),
            'search' => $search,
            'activeRole' => $role === '' ? 'all' : $role,
            'activeStatus' => $status === '' ? 'all' : $status,
            'roles' => $this->adminAccountRoles(),
            'statuses' => $this->adminAccountStatuses(),
        ]);
    }

    public function createAccount(): View|RedirectResponse
    {
        if ($redirect = $this->denyIfNotSuperAdmin()) {
            return $redirect;
        }

        return view('admin.accounts.create', [
            'account' => [],
            'roles' => $this->adminAccountRoles(),
            'statuses' => $this->adminAccountStatuses(),
            'isSuper' => true,
            'isSelf' => false,
        ]);
    }

    public function showAccount(string $id): View
    {
        return redirect()->route('admin.accounts.edit', $id);
    }

    public function editAccount(string $id): View|RedirectResponse
    {
        // Non-super hanya boleh membuka profilnya sendiri.
        if (! $this->isSuperAdmin() && $id !== session('admin.user.code')) {
            return redirect()->route('admin.accounts.edit', session('admin.user.code'))
                ->with('error', 'Kamu hanya bisa mengubah profil sendiri.');
        }

        $account = $this->findAccountByCode($id);

        return view('admin.accounts.edit', [
            'account' => $account,
            'roles' => $this->adminAccountRoles(),
            'statuses' => $this->adminAccountStatuses(),
            'isSuper' => $this->isSuperAdmin(),
            'isSelf' => $id === session('admin.user.code'),
        ]);
    }

    public function storeAccount(Request $request): RedirectResponse
    {
        if ($redirect = $this->denyIfNotSuperAdmin()) {
            return $redirect;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:30'],
            'role' => ['required', 'string'],
            'status' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        try {
            $account = $this->api->createAdminAccount([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'admin_role' => $this->adminRoleKey($validated['role']),
                'status' => $this->adminStatusKey($validated['status']),
                'password' => $validated['password'],
                'password_confirmation' => $validated['password'],
            ]);
        } catch (LarashopApiException $exception) {
            return back()->withInput()->withErrors($exception->errors !== [] ? $exception->errors : ['name' => $exception->getMessage()]);
        }

        return redirect()->route('admin.accounts.edit', $account['id'])->with('success', "Account {$validated['name']} berhasil dibuat.");
    }

    public function updateAccount(Request $request, string $id): RedirectResponse
    {
        $isSuper = $this->isSuperAdmin();

        // Non-super hanya boleh mengubah profilnya sendiri.
        if (! $isSuper && $id !== session('admin.user.code')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Kamu hanya bisa mengubah profil sendiri.');
        }

        $existingAccount = $this->findAccountByCode($id);
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8'],
        ];
        if ($isSuper) {
            $rules['role'] = ['required', 'string'];
            $rules['status'] = ['required', 'string'];
        }
        $validated = $request->validate($rules);

        // Role & status hanya dikirim oleh super admin; non-super pakai nilai lama.
        $payload = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $validated['password'] ?: null,
            'password_confirmation' => $validated['password'] ?: null,
        ];
        if ($isSuper) {
            $payload['admin_role'] = $this->adminRoleKey($validated['role']);
            $payload['status'] = $this->adminStatusKey($validated['status']);
        }

        try {
            $this->api->updateAdminAccount($existingAccount['user_id'], $payload);
        } catch (LarashopApiException $exception) {
            return back()->withInput()->withErrors($exception->errors !== [] ? $exception->errors : ['name' => $exception->getMessage()]);
        }

        return redirect()->route('admin.accounts.edit', $id)->with('success', "Perubahan account {$validated['name']} berhasil disimpan.");
    }

    public function destroyAccount(string $id): RedirectResponse
    {
        if ($redirect = $this->denyIfNotSuperAdmin()) {
            return $redirect;
        }

        $existingAccount = $this->findAccountByCode($id);

        try {
            $this->api->deleteAdminAccount($existingAccount['user_id']);
        } catch (LarashopApiException $exception) {
            return redirect()
                ->route('admin.accounts.index')
                ->with('error', $exception->errors['account'][0] ?? $exception->getMessage());
        }

        return redirect()->route('admin.accounts.index')->with('success', "Account {$existingAccount['name']} berhasil dihapus.");
    }

    /** Admin yang sedang login adalah super admin? (dari payload login di session) */
    private function isSuperAdmin(): bool
    {
        return (bool) (session('admin.user.is_super_admin')
            ?? (session('admin.user.admin_role') === 'super_admin'));
    }

    /** Tolak akses kelola akun untuk non-super-admin. Null = boleh lanjut. */
    private function denyIfNotSuperAdmin(): ?RedirectResponse
    {
        if ($this->isSuperAdmin()) {
            return null;
        }

        return redirect()->route('admin.dashboard')
            ->with('error', 'Hanya super admin yang dapat mengakses & mengelola akun admin.');
    }

    public function customers(Request $request): View
    {
        $customers = collect($this->api->adminCustomers(['search' => $request->string('search')->toString()]))
            ->map(fn (array $customer) => $this->mapCustomerSummary($customer));

        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();

        if ($status !== '' && $status !== 'all') {
            $customers = $customers->where('status', $this->customerStatuses()[$status] ?? $status);
        }

        $items = $customers->values()->all();

        return view('admin.customers.index', [
            'customers' => $items,
            'search' => $search,
            'activeStatus' => $status === '' ? 'all' : $status,
            'statuses' => $this->customerStatuses(),
        ]);
    }

    public function createCustomer(): View
    {
        return view('admin.customers.create', [
            'customer' => [],
            'addresses' => [],
            'statuses' => $this->customerStatuses(),
        ]);
    }

    public function showCustomer(string $code): View
    {
        $customer = $this->findCustomerByCode($code);
        $primaryAddress = collect($customer['addresses'])->firstWhere('is_primary', true) ?? $customer['addresses'][0] ?? [];

        return view('admin.customers.show', [
            'customer' => $customer,
            'addresses' => $customer['addresses'],
            'primaryAddress' => $primaryAddress,
            'shippingAddress' => $this->shippingAddressSummary($primaryAddress),
        ]);
    }

    public function editCustomer(string $code): View
    {
        $customer = $this->findCustomerByCode($code);

        return view('admin.customers.edit', [
            'customer' => $customer,
            'addresses' => $customer['addresses'],
            'statuses' => $this->customerStatuses(),
        ]);
    }

    public function storeCustomer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'status' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
            'shipping_addresses' => ['required', 'json'],
        ]);

        try {
            $customer = $this->api->createAdminCustomer([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'phone' => $validated['phone'],
                'status' => $validated['status'],
                'password' => $validated['password'],
                'password_confirmation' => $validated['password'],
                'addresses' => $this->normalizeCustomerAddresses($validated['shipping_addresses']),
            ]);
        } catch (LarashopApiException $exception) {
            return back()->withInput()->withErrors($exception->errors !== [] ? $exception->errors : ['name' => $exception->getMessage()]);
        }

        return redirect()->route('admin.customers.show', $customer['code'])->with('success', "Customer {$customer['name']} berhasil dibuat.");
    }

    public function updateCustomer(Request $request, string $code): RedirectResponse
    {
        $existingCustomer = $this->findCustomerByCode($code);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'status' => ['required', 'string'],
            'password' => ['nullable', 'string', 'min:8'],
            'shipping_addresses' => ['required', 'json'],
        ]);
        $submittedAddresses = $this->normalizeCustomerAddresses($validated['shipping_addresses']);

        try {
            $this->api->updateAdminCustomer($existingCustomer['id'], [
                'name' => $validated['name'],
                'username' => $validated['username'],
                'phone' => $validated['phone'],
                'status' => $validated['status'],
                'password' => $validated['password'] ?: null,
                'password_confirmation' => $validated['password'] ?: null,
            ]);

            $existingAddresses = collect($existingCustomer['addresses'])->keyBy('id');
            $submittedAddressIds = collect($submittedAddresses)->pluck('id')->filter(fn ($id) => is_numeric($id));

            foreach ($existingAddresses as $addressId => $address) {
                if (! $submittedAddressIds->contains((string) $addressId) && ! $submittedAddressIds->contains((int) $addressId)) {
                    $this->api->deleteAdminCustomerAddress($existingCustomer['id'], (int) $addressId);
                }
            }

            foreach ($submittedAddresses as $address) {
                $payload = Arr::except($address, ['id']);

                if (isset($address['id']) && is_numeric($address['id'])) {
                    $this->api->updateAdminCustomerAddress($existingCustomer['id'], (int) $address['id'], $payload);
                    continue;
                }

                $this->api->createAdminCustomerAddress($existingCustomer['id'], $payload);
            }
        } catch (LarashopApiException $exception) {
            return back()->withInput()->withErrors($exception->errors !== [] ? $exception->errors : ['name' => $exception->getMessage()]);
        }

        return redirect()->route('admin.customers.show', $code)->with('success', "Perubahan customer {$validated['name']} berhasil disimpan.");
    }

    public function destroyCustomer(string $code): RedirectResponse
    {
        $customer = $this->findCustomerByCode($code);

        try {
            $this->api->deleteAdminCustomer($customer['id']);
        } catch (LarashopApiException $exception) {
            return redirect()->route('admin.customers.show', $code)
                ->with('error', $exception->errors['customer'][0] ?? $exception->getMessage());
        }

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer '.($customer['name'] ?? '').' berhasil dihapus.');
    }

    public function bulkDestroyCustomers(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_codes' => ['required', 'array', 'min:1'],
            'customer_codes.*' => ['string'],
        ]);

        try {
            $result = $this->api->bulkDeleteAdminCustomers($validated['customer_codes']);
        } catch (LarashopApiException $exception) {
            return redirect()->route('admin.customers.index')
                ->with('error', $exception->getMessage());
        }

        $deleted = count($result['deleted'] ?? []);
        $deactivated = count($result['deactivated'] ?? []);

        return redirect()->route('admin.customers.index')
            ->with('success', "{$deleted} customer dihapus, {$deactivated} dinonaktifkan (punya pesanan).");
    }

    public function products(Request $request): View
    {
        $products = collect($this->api->adminProducts(['search' => $request->string('search')->toString()]))
            ->map(fn (array $product) => $this->mapProductSummary($product));

        $search = trim($request->string('search')->toString());
        $category = $request->string('category')->toString();
        $status = $request->string('status')->toString();
        $sort = $request->string('sort')->toString();

        if ($category !== '' && $category !== 'all') {
            $products = $products->where('category', $this->productCategories()[$category] ?? $category);
        }

        if ($status !== '' && $status !== 'all') {
            $products = $products->where('status', $this->productStatuses()[$status] ?? $status);
        }

        $products = match ($sort) {
            'price_desc' => $products->sortByDesc('price_value'),
            'price_asc' => $products->sortBy('price_value'),
            'stock_asc' => $products->sortBy('stock'),
            'stock_desc' => $products->sortByDesc('stock'),
            'name_asc' => $products->sortBy(fn (array $product) => mb_strtolower($product['name'])),
            default => $products,
        };

        $items = $products->values()->all();

        return view('admin.products.index', [
            'products' => $items,
            'lowStockProducts' => array_values(array_filter($items, fn (array $product) => $product['stock'] <= 12)),
            'search' => $search,
            'activeCategory' => $category === '' ? 'all' : $category,
            'activeStatus' => $status === '' ? 'all' : $status,
            'activeSort' => $sort === '' ? 'default' : $sort,
            'categories' => $this->productCategories(),
            'statuses' => $this->productStatuses(),
        ]);
    }

    public function createProduct(): View
    {
        return view('admin.products.create', [
            'categories' => $this->productCategories(),
            'statuses' => $this->productStatuses(),
            'images' => [],
        ]);
    }

    public function showProduct(string $sku): View
    {
        $product = $this->findProductBySku($sku);

        return view('admin.products.show', [
            'product' => $product,
            'images' => $product['images'],
        ]);
    }

    public function editProduct(string $sku): View
    {
        $product = $this->findProductBySku($sku);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $this->productCategories(),
            'statuses' => $this->productStatuses(),
            'images' => $product['images'],
        ]);
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $validated = $this->validateProductForm($request);

        try {
            $product = $this->api->createAdminProduct($this->buildProductPayload($validated, null, $request));
        } catch (LarashopApiException $exception) {
            return back()->withInput()->withErrors($exception->errors !== [] ? $exception->errors : ['name' => $exception->getMessage()]);
        }

        return redirect()->route('admin.products.show', $product['sku'])->with('success', "Produk {$product['name']} berhasil disimpan.");
    }

    public function updateProduct(Request $request, string $sku): RedirectResponse
    {
        $existingProduct = $this->findProductBySku($sku);
        $validated = $this->validateProductForm($request);

        try {
            $product = $this->api->updateAdminProduct($existingProduct['id'], $this->buildProductPayload($validated, $existingProduct, $request));
        } catch (LarashopApiException $exception) {
            return back()->withInput()->withErrors($exception->errors !== [] ? $exception->errors : ['name' => $exception->getMessage()]);
        }

        return redirect()->route('admin.products.show', $product['sku'])->with('success', "Perubahan produk {$product['name']} berhasil disimpan.");
    }

    public function orders(Request $request): View
    {
        $orders = collect($this->api->adminOrders());
        $allOrders = $orders;
        $status = $request->string('status')->toString();
        $search = trim($request->string('search')->toString());

        $statusTabs = [
            ['key' => 'all', 'label' => 'Semua', 'count' => $allOrders->count()],
            ['key' => 'pending_payment', 'label' => 'Belum bayar', 'count' => $allOrders->where('status', 'pending_payment')->count()],
            ['key' => 'paid', 'label' => 'Dibayar', 'count' => $allOrders->where('status', 'paid')->count()],
            ['key' => 'processing', 'label' => 'Diproses', 'count' => $allOrders->where('status', 'processing')->count()],
            ['key' => 'shipped', 'label' => 'Dikirim', 'count' => $allOrders->where('status', 'shipped')->count()],
            ['key' => 'completed', 'label' => 'Selesai', 'count' => $allOrders->where('status', 'completed')->count()],
            ['key' => 'cancelled', 'label' => 'Dibatalkan', 'count' => $allOrders->where('status', 'cancelled')->count()],
        ];

        if ($status !== '' && $status !== 'all') {
            $orders = $orders->where('status', $status);
        }

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $orders = $orders->filter(function (array $order) use ($needle): bool {
                return str_contains(mb_strtolower($order['code']), $needle)
                    || str_contains(mb_strtolower($order['customer'] ?? ''), $needle)
                    || str_contains(mb_strtolower($order['phone'] ?? ''), $needle);
            });
        }

        $items = $orders->values()->all();

        return view('admin.orders.index', [
            'orders' => $items,
            'activeStatus' => $status ?: 'all',
            'statusTabs' => $statusTabs,
            'search' => $search,
            'stats' => [
                ['label' => 'Pending Payment', 'value' => (string) collect($items)->where('status', 'pending_payment')->count(), 'note' => 'Perlu follow up pembayaran'],
                ['label' => 'Paid', 'value' => (string) collect($items)->where('status', 'paid')->count(), 'note' => 'Siap dibuat shipment'],
                ['label' => 'Processing', 'value' => (string) collect($items)->where('status', 'processing')->count(), 'note' => 'Sedang dipacking'],
                ['label' => 'Shipped', 'value' => (string) collect($items)->where('status', 'shipped')->count(), 'note' => 'Sudah memiliki AWB'],
            ],
        ]);
    }

    public function showOrder(string $code): View
    {
        $order = $this->findOrderByCode($code);

        return view('admin.orders.show', [
            'order' => $order,
            'timeline' => [
                ['label' => 'Order masuk', 'note' => 'Pesanan tercatat di sistem.', 'active' => true],
                ['label' => 'Validasi pembayaran', 'note' => 'Admin mengecek mutasi dan nominal unik.', 'active' => in_array($order['status'], ['paid', 'processing', 'shipped', 'completed'], true)],
                ['label' => 'Packing', 'note' => 'Pesanan disiapkan untuk pengiriman.', 'active' => in_array($order['status'], ['processing', 'shipped', 'completed'], true)],
                ['label' => 'Shipment', 'note' => 'Order pengiriman dibuat dan resi diterbitkan.', 'active' => ! empty($order['awb']) || in_array($order['status'], ['shipped', 'completed'], true)],
                ['label' => 'Order selesai', 'note' => 'Customer sudah menerima pesanan dan order ditutup.', 'active' => ($order['status'] ?? null) === 'completed'],
                ['label' => 'Order dibatalkan', 'note' => 'Order dihentikan dan tidak dilanjutkan ke proses berikutnya.', 'active' => ($order['status'] ?? null) === 'cancelled', 'tone' => 'cancelled'],
            ],
        ]);
    }

    public function validatePayment(string $code): RedirectResponse
    {
        $order = $this->findOrderByCode($code);
        $updated = $this->api->validateAdminOrderPayment($order['id']);

        return redirect()->route('admin.orders.show', $updated['code'])->with('success', "Pembayaran order {$updated['code']} berhasil divalidasi.");
    }

    public function processShipment(string $code): RedirectResponse
    {
        $order = $this->findOrderByCode($code);
        $updated = $this->api->processAdminOrderShipment($order['id']);

        return redirect()->route('admin.shipments.index')->with('success', "Shipment untuk order {$updated['code']} berhasil diproses.");
    }

    public function schedulePickup(Request $request, string $code): RedirectResponse
    {
        $validated = $request->validate([
            'pickup_date' => ['required', 'date'],
            'pickup_time' => ['required', 'string'],
            'pickup_vehicle' => ['required', 'in:Motor,Mobil,Truk'],
        ]);

        $order = $this->findOrderByCode($code);

        try {
            $updated = $this->api->scheduleAdminPickup($order['id'], $validated);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menjadwalkan pickup: '.$e->getMessage());
        }

        return redirect()->route('admin.orders.show', $updated['code'])->with('success', "Pickup order {$updated['code']} berhasil dijadwalkan.");
    }

    public function schedulePickupBulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_codes' => ['required', 'array', 'min:1'],
            'order_codes.*' => ['string'],
            'pickup_date' => ['required', 'date'],
            'pickup_time' => ['required', 'string'],
            'pickup_vehicle' => ['required', 'in:Motor,Mobil,Truk'],
        ]);

        try {
            $res = $this->api->scheduleAdminPickupBulk($validated);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menjadwalkan pickup massal: '.$e->getMessage());
        }

        $summary = $res['summary'] ?? [];
        $msg = $res['message'] ?? 'Pickup massal diproses.';
        if (! empty($summary['failed'])) {
            return redirect()->route('admin.orders.index')
                ->with('error', $msg.' Gagal: '.implode(', ', $summary['failed']));
        }

        return redirect()->route('admin.orders.index')->with('success', $msg);
    }

    public function markShippedBulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_codes' => ['required', 'array', 'min:1'],
            'order_codes.*' => ['string'],
        ]);

        try {
            $res = $this->api->markAdminShippedBulk($validated);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menandai dikirim: '.$e->getMessage());
        }

        return redirect()->route('admin.orders.index')->with('success', $res['message'] ?? 'Order ditandai dikirim.');
    }

    public function printLabelsBulk(Request $request)
    {
        $validated = $request->validate([
            'order_codes' => ['required', 'array', 'min:1'],
            'order_codes.*' => ['string'],
        ]);

        try {
            $label = $this->api->adminLabelsBulk($validated['order_codes']);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal cetak label: '.$e->getMessage());
        }

        return response($label['content'], 200, [
            'Content-Type' => $label['content_type'] ?: 'application/pdf',
            'Content-Disposition' => 'inline; filename="labels.pdf"',
        ]);
    }

    public function orderLabel(string $code)
    {
        $order = $this->findOrderByCode($code);

        try {
            $label = $this->api->adminOrderLabel($order['id']);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengambil label: '.$e->getMessage());
        }

        return response($label['content'], 200, [
            'Content-Type' => $label['content_type'] ?: 'application/pdf',
            'Content-Disposition' => 'inline; filename="label-'.$order['code'].'.pdf"',
        ]);
    }

    public function orderLabelDiy(string $code)
    {
        $order = $this->findOrderByCode($code);

        try {
            $label = $this->api->adminOrderLabelDiy($order['id']);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuat label DIY: '.$e->getMessage());
        }

        return response($label['content'], 200, [
            'Content-Type' => $label['content_type'] ?: 'application/pdf',
            'Content-Disposition' => 'inline; filename="label-diy-'.$order['code'].'.pdf"',
        ]);
    }

    public function qris()
    {
        try {
            $res = $this->api->adminQrisList();
        } catch (\Throwable $e) {
            return view('admin.qris.index', [
                'qrisList' => [],
                'meta' => ['enabled' => false, 'active_qris_id' => ''],
            ])->with('error', 'Gagal memuat data QRIS: '.$e->getMessage());
        }

        return view('admin.qris.index', [
            'qrisList' => $res['data'] ?? [],
            'meta' => $res['meta'] ?? ['enabled' => false, 'active_qris_id' => ''],
        ]);
    }

    public function qrisUpload(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'qris_image' => ['required', 'file', 'mimes:png,jpg,jpeg', 'max:5120'],
        ]);

        try {
            $file = $request->file('qris_image');
            $this->api->adminQrisUpload($file->getRealPath(), $file->getClientOriginalName(), $request->input('name'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal upload QRIS: '.$e->getMessage());
        }

        return redirect()->route('admin.qris.index')->with('success', 'QRIS berhasil di-upload & diaktifkan.');
    }

    public function qrisActivate(int $id): RedirectResponse
    {
        try {
            $this->api->adminQrisActivate($id);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengaktifkan QRIS: '.$e->getMessage());
        }

        return back()->with('success', 'QRIS diaktifkan.');
    }

    public function qrisDelete(int $id): RedirectResponse
    {
        try {
            $this->api->adminQrisDelete($id);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus QRIS: '.$e->getMessage());
        }

        return back()->with('success', 'QRIS dihapus dari daftar.');
    }

    public function completeOrder(string $code): RedirectResponse
    {
        $order = $this->findOrderByCode($code);
        $updated = $this->api->completeAdminOrder($order['id']);

        return redirect()->route('admin.orders.show', $updated['code'])->with('success', "Order {$updated['code']} berhasil ditandai selesai.");
    }

    public function cancelOrder(string $code): RedirectResponse
    {
        $order = $this->findOrderByCode($code);
        $updated = $this->api->cancelAdminOrder($order['id']);

        return redirect()->route('admin.orders.show', $updated['code'])->with('success', "Order {$updated['code']} berhasil dibatalkan.");
    }

    public function rejectOrderCancellation(string $code): RedirectResponse
    {
        $order = $this->findOrderByCode($code);

        try {
            $this->api->rejectAdminOrderCancellation($order['id']);
        } catch (LarashopApiException $exception) {
            return redirect()->route('admin.orders.show', $code)->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.orders.show', $code)->with('success', 'Permintaan pembatalan ditolak. Order tetap berjalan.');
    }

    public function shipments(Request $request): View
    {
        $shipments = collect($this->api->adminShipments());
        $status = $request->string('status')->toString();

        if ($status !== '' && $status !== 'all') {
            $shipments = $shipments->where('status', $status);
        }

        $items = $shipments->values()->all();

        return view('admin.shipments.index', [
            'shipments' => $items,
            'activeStatus' => $status ?: 'all',
            'shipmentSettings' => $this->api->adminShipmentSettings(),
            'stats' => [
                ['label' => 'Ready to Create', 'value' => (string) collect($items)->where('status', 'ready_to_create')->count(), 'note' => 'Menunggu action admin'],
                ['label' => 'Pickup Scheduled', 'value' => (string) collect($items)->where('status', 'pickup_scheduled')->count(), 'note' => 'Pickup sudah diminta'],
                ['label' => 'In Transit', 'value' => (string) collect($items)->where('status', 'in_transit')->count(), 'note' => 'Dalam perjalanan'],
            ],
        ]);
    }

    public function shipmentSettings(): View
    {
        $settings = $this->api->adminShipmentSettings();
        $settings['destination_label'] = collect([
            $settings['subdistrict'] ?? null,
            $settings['district'] ?? null,
            $settings['city'] ?? null,
            $settings['province'] ?? null,
            $settings['postal_code'] ?? null,
        ])->filter()->implode(', ');
        $settings['selected_couriers'] = collect(explode(':', (string) ($settings['selected_courier'] ?? '')))
            ->map(fn (string $courier) => trim($courier))
            ->filter()
            ->values()
            ->all();

        return view('admin.shipments.settings', [
            'settings' => $settings,
        ]);
    }

    public function searchShipmentDestinations(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        try {
            $destinations = $this->api->adminShipmentDestinations($validated['search']);
        } catch (LarashopApiException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->status >= 400 ? $exception->status : 500);
        }

        return response()->json([
            'data' => $destinations,
        ]);
    }

    public function updateShipmentSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:30'],
            'origin_id' => ['nullable', 'integer'],
            'selected_couriers' => ['required', 'array', 'min:1'],
            'selected_couriers.*' => ['required', 'string', 'max:30'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'subdistrict' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
            'address_line' => ['required', 'string'],
            'pin_point' => ['nullable', 'string', 'max:60'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['selected_courier'] = collect($validated['selected_couriers'])
            ->map(fn (string $courier) => trim($courier))
            ->filter()
            ->unique()
            ->implode(':');
        unset($validated['selected_couriers']);

        try {
            $this->api->updateAdminShipmentSettings($validated);
        } catch (LarashopApiException $exception) {
            return back()->withInput()->withErrors($exception->errors !== [] ? $exception->errors : ['label' => $exception->getMessage()]);
        }

        return redirect()->route('admin.shipments.settings')->with('success', 'Setting shipment berhasil diperbarui.');
    }

    public function paymentSettings(): View
    {
        // Apakah sudah ada QRIS toko aktif (qris_id). Kalau QRIS dicentang tapi belum
        // ada QRIS aktif, pembayaran QRIS akan diam-diam jatuh ke transfer — beri peringatan.
        $qrisReady = false;
        try {
            $qris = $this->api->adminQrisList();
            $qrisReady = trim((string) ($qris['meta']['active_qris_id'] ?? '')) !== '';
        } catch (\Throwable $e) {
            // Abaikan; anggap belum siap.
        }

        return view('admin.payments.settings', [
            'accounts' => $this->api->adminPaymentAccounts(),
            'settings' => $this->api->adminSettings(),
            'qrisReady' => $qrisReady,
        ]);
    }

    public function updateStoreSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_whatsapp' => ['nullable', 'string', 'max:20'],
            'store_brand' => ['nullable', 'string', 'max:100'],
            'store_email' => ['nullable', 'email', 'max:100'],
            'unique_code_enabled' => ['nullable', 'boolean'],
            'payment_transfer_enabled' => ['nullable', 'boolean'],
            'payment_qris_enabled' => ['nullable', 'boolean'],
        ]);

        // Checkbox: selalu kirim true/false eksplisit (kalau tak dicentang, key tak ada).
        $validated['unique_code_enabled'] = $request->boolean('unique_code_enabled');
        $validated['payment_transfer_enabled'] = $request->boolean('payment_transfer_enabled');
        $validated['payment_qris_enabled'] = $request->boolean('payment_qris_enabled');

        try {
            $this->api->updateAdminSettings($validated);
        } catch (LarashopApiException $exception) {
            return back()->withInput()->withErrors($exception->errors !== [] ? $exception->errors : ['store_whatsapp' => $exception->getMessage()]);
        }

        // Segarkan cache nomor WA toko supaya tombol WhatsApp melayang langsung ikut berubah.
        Cache::forget('storefront.store_whatsapp');

        return redirect()->route('admin.payments.settings')->with('success', 'Nomor WhatsApp toko berhasil disimpan.');
    }

    public function storePaymentAccount(Request $request): RedirectResponse
    {
        $validated = $this->validatePaymentAccount($request);

        try {
            $this->api->createAdminPaymentAccount($validated);
        } catch (LarashopApiException $exception) {
            return back()->withInput()->withErrors($exception->errors !== [] ? $exception->errors : ['bank_name' => $exception->getMessage()]);
        }

        return redirect()->route('admin.payments.settings')->with('success', 'Rekening pembayaran berhasil ditambahkan.');
    }

    public function updatePaymentAccount(Request $request, int $id): RedirectResponse
    {
        $validated = $this->validatePaymentAccount($request);

        try {
            $this->api->updateAdminPaymentAccount($id, $validated);
        } catch (LarashopApiException $exception) {
            return back()->withInput()->withErrors($exception->errors !== [] ? $exception->errors : ['bank_name' => $exception->getMessage()]);
        }

        return redirect()->route('admin.payments.settings')->with('success', 'Rekening pembayaran berhasil diperbarui.');
    }

    public function deletePaymentAccount(int $id): RedirectResponse
    {
        try {
            $this->api->deleteAdminPaymentAccount($id);
        } catch (LarashopApiException $exception) {
            return back()->withErrors(['payment' => $exception->getMessage()]);
        }

        return redirect()->route('admin.payments.settings')->with('success', 'Rekening pembayaran berhasil dihapus.');
    }

    public function categories(): View
    {
        return view('admin.categories.index', [
            'categories' => $this->api->adminCategories(),
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $this->validateCategory($request);

        try {
            $this->api->createAdminCategory($validated);
        } catch (LarashopApiException $exception) {
            return back()->withInput()->withErrors($exception->errors !== [] ? $exception->errors : ['name' => $exception->getMessage()]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, int $id): RedirectResponse
    {
        $validated = $this->validateCategory($request);

        try {
            $this->api->updateAdminCategory($id, $validated);
        } catch (LarashopApiException $exception) {
            return back()->withInput()->withErrors($exception->errors !== [] ? $exception->errors : ['name' => $exception->getMessage()]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function deleteCategory(int $id): RedirectResponse
    {
        try {
            $this->api->deleteAdminCategory($id);
        } catch (LarashopApiException $exception) {
            return back()->withErrors(['category' => $exception->errors['category'][0] ?? $exception->getMessage()]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    protected function validateCategory(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    protected function validatePaymentAccount(Request $request): array
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'account_holder' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    public function showShipment(string $code): View
    {
        $shipment = $this->api->adminShipment($code);

        return view('admin.shipments.show', [
            'shipment' => $shipment,
        ]);
    }

    private function validateProductForm(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:50'],
            'category' => ['required', 'string'],
            'status' => ['required', 'string'],
            'badge_label' => ['nullable', 'string', 'max:50'],
            'description' => ['required', 'string'],
            'variants_json' => ['nullable', 'json'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'product_images' => ['nullable', 'array'],
            'product_images.*' => ['file'],
            'primary_image' => ['nullable', 'string'],
            'removed_images' => ['nullable', 'string'],
            'existing_image_count' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function buildProductPayload(array $validated, ?array $existingProduct, Request $request): array
    {
        $existingImages = collect($existingProduct['images'] ?? []);
        $removedIds = collect(json_decode((string) ($validated['removed_images'] ?? '[]'), true) ?: []);
        $remainingImages = $existingImages
            ->reject(fn (array $image) => $removedIds->contains($image['id']))
            ->pluck('path')
            ->values()
            ->all();

        $newImages = collect($request->file('product_images', []))
            ->filter()
            ->map(fn ($file) => '/storage/'.$file->store('products', 'public'))
            ->values()
            ->all();

        $imagePaths = array_values(array_filter([...$remainingImages, ...$newImages]));

        if ($imagePaths === []) {
            $imagePaths = ['/images/products/gallery-detail.svg'];
        }

        $variants = filled($validated['variants_json'] ?? null)
            ? $this->normalizeProductVariants($validated['variants_json'])
            : [[
                'sku' => strtoupper($validated['sku']).'-1',
                'label' => $validated['unit'] ?? 'Default',
                'price' => (int) ($validated['price'] ?? 0),
                'compare_at_price' => null,
                'stock' => (int) ($validated['stock'] ?? 0),
                'weight_grams' => isset($validated['weight']) ? (int) round(((float) $validated['weight']) * 1000) : null,
                'length_cm' => filled($validated['length'] ?? null) ? (float) $validated['length'] : null,
                'width_cm' => filled($validated['width'] ?? null) ? (float) $validated['width'] : null,
                'height_cm' => filled($validated['height'] ?? null) ? (float) $validated['height'] : null,
                'is_default' => true,
                'is_active' => true,
            ]];
        $defaultVariant = collect($variants)->firstWhere('is_default', true) ?? $variants[0];
        $totalStock = collect($variants)
            ->filter(fn (array $variant) => $variant['is_active'])
            ->sum('stock');

        if (Str::startsWith((string) ($validated['primary_image'] ?? ''), 'new-') && count($newImages) > 0) {
            $primaryPath = $newImages[0];
            $imagePaths = collect($imagePaths)->reject(fn (string $path) => $path === $primaryPath)->prepend($primaryPath)->values()->all();
        } elseif (Str::startsWith((string) ($validated['primary_image'] ?? ''), 'existing-')) {
            $primaryIndex = (int) Str::after((string) $validated['primary_image'], 'existing-');
            $primaryPath = $existingImages[$primaryIndex]['path'] ?? null;
            if ($primaryPath !== null) {
                $imagePaths = collect($imagePaths)->reject(fn (string $path) => $path === $primaryPath)->prepend($primaryPath)->values()->all();
            }
        }

        return [
            'sku' => strtoupper($validated['sku']),
            'slug' => Str::slug($validated['name']),
            'name' => $validated['name'],
            'category_slug' => $validated['category'],
            'short_description' => Str::limit(trim(strip_tags($validated['description'])), 180, ''),
            'description' => $this->sanitizeRichText($validated['description']),
            'variants' => $variants,
            'price' => $defaultVariant['price'],
            'compare_at_price' => $defaultVariant['compare_at_price'],
            'stock' => (int) $totalStock,
            'weight_label' => $defaultVariant['label'],
            'weight_grams' => $defaultVariant['weight_grams'],
            'public_status' => $validated['status'],
            'catalog_status' => $validated['status'] === 'preorder'
                ? 'preorder'
                : ((int) $totalStock === 0 ? 'sold_out' : 'available'),
            'badge_label' => trim((string) ($validated['badge_label'] ?? '')) ?: null,
            'sold_count' => $existingProduct['sold_count'] ?? 0,
            'highlights' => $existingProduct['highlights'] ?? [],
            'image_paths' => $imagePaths,
            'is_featured' => $existingProduct['is_featured'] ?? false,
            'published_at' => $validated['status'] === 'active' ? now()->toISOString() : null,
        ];
    }

    private function findProductBySku(string $sku): array
    {
        $summary = collect($this->api->adminProducts())->firstWhere('sku', strtoupper($sku));
        abort_if($summary === null, 404);

        return $this->mapProductDetail($this->api->adminProduct((int) $summary['id']));
    }

    private function findCustomerByCode(string $code): array
    {
        $summary = collect($this->api->adminCustomers())->firstWhere('code', strtoupper($code));
        abort_if($summary === null, 404);

        return $this->mapCustomerDetail($this->api->adminCustomer((int) $summary['id']));
    }

    private function mapProductSummary(array $product): array
    {
        return [
            'id' => $product['id'],
            'slug' => $product['slug'],
            'sku' => $product['sku'],
            'name' => $product['name'],
            'image' => $product['image'] ?? '/images/products/gallery-detail.svg',
            'category' => $product['category'],
            'category_key' => $product['category_slug'] ?? Str::slug($product['category']),
            'price' => $product['price'],
            'price_value' => $product['price_value'] ?? (int) preg_replace('/\D/', '', $product['price']),
            'stock' => $product['stock'],
            'weight' => $product['weight_label'] ?? '-',
            'dimension' => $product['dimension'] ?? 'Belum diatur',
            'length' => (string) ($product['length_cm'] ?? 0),
            'width' => (string) ($product['width_cm'] ?? 0),
            'height' => (string) ($product['height_cm'] ?? 0),
            'unit' => $product['default_variant']['label'] ?? ($product['weight_label'] ?? '-'),
            'default_variant' => $product['default_variant'] ?? null,
            'variant_count' => (int) ($product['variant_count'] ?? 0),
            'variants' => collect($product['variants'] ?? [])->map(fn (array $variant) => $this->mapProductVariant($variant))->values()->all(),
            'status' => $product['status'],
            'status_key' => $product['public_status'],
            'badge_label' => $product['badge_label'] ?? null,
            'highlight' => ($product['badge_label'] ?? null) ?: ($product['stock'] <= 12 ? 'Stok menipis' : 'Siap tampil'),
            'description' => $product['description'] ?? '',
            'images' => [],
        ];
    }

    private function mapProductDetail(array $product): array
    {
        $summary = $this->mapProductSummary($product);
        $summary['description'] = $product['description'] ?? '';
        $summary['images'] = collect($product['images'] ?? [])
            ->map(fn (array $image, int $index) => [
                'id' => $image['id'],
                'path' => $image['path'],
                'label' => $image['alt'] ?: 'Foto '.($index + 1),
                'name' => $image['alt'] ?: $product['name'],
                'is_primary' => $image['is_primary'] ?? false,
            ])
            ->values()
            ->all();

        return $summary;
    }

    private function mapProductVariant(array $variant): array
    {
        return [
            'sku' => $variant['sku'],
            'label' => $variant['label'],
            'price_value' => (int) ($variant['price_value'] ?? 0),
            'price' => $variant['price'] ?? 'Rp0',
            'compare_at_price' => $variant['compare_at_price'] ?? null,
            'stock' => (int) ($variant['stock'] ?? 0),
            'weight_grams' => $variant['weight_grams'] ?? null,
            'length_cm' => $variant['length_cm'] ?? null,
            'width_cm' => $variant['width_cm'] ?? null,
            'height_cm' => $variant['height_cm'] ?? null,
            'dimension' => $variant['dimension'] ?? 'Belum diatur',
            'is_default' => (bool) ($variant['is_default'] ?? false),
            'is_active' => (bool) ($variant['is_active'] ?? true),
        ];
    }

    private function normalizeProductVariants(string $json): array
    {
        $variants = collect(json_decode($json, true) ?: [])
            ->values()
            ->map(function (array $variant, int $index): array {
                return [
                    'sku' => strtoupper(trim((string) ($variant['sku'] ?? ''))),
                    'label' => trim((string) ($variant['label'] ?? '')),
                    'price' => (int) ($variant['price'] ?? 0),
                    'compare_at_price' => filled($variant['compare_at_price'] ?? null) ? (int) $variant['compare_at_price'] : null,
                    'stock' => (int) ($variant['stock'] ?? 0),
                    'weight_grams' => filled($variant['weight_grams'] ?? null) ? (int) $variant['weight_grams'] : null,
                    'length_cm' => filled($variant['length_cm'] ?? null) ? (float) $variant['length_cm'] : null,
                    'width_cm' => filled($variant['width_cm'] ?? null) ? (float) $variant['width_cm'] : null,
                    'height_cm' => filled($variant['height_cm'] ?? null) ? (float) $variant['height_cm'] : null,
                    'is_default' => (bool) ($variant['is_default'] ?? false),
                    'is_active' => array_key_exists('is_active', $variant) ? (bool) $variant['is_active'] : true,
                ];
            })
            ->filter(fn (array $variant) => $variant['sku'] !== '' && $variant['label'] !== '')
            ->values();

        if ($variants->isEmpty()) {
            return [[
                'sku' => strtoupper(Str::slug((string) now()->timestamp)),
                'label' => 'Default',
                'price' => 0,
                'compare_at_price' => null,
                'stock' => 0,
                'weight_grams' => null,
                'length_cm' => null,
                'width_cm' => null,
                'height_cm' => null,
                'is_default' => true,
                'is_active' => true,
            ]];
        }

        if (! $variants->contains(fn (array $variant) => $variant['is_default'])) {
            $variants[0]['is_default'] = true;
        }

        $defaultIndex = $variants->search(fn (array $variant) => $variant['is_default']);

        return $variants
            ->map(function (array $variant, int $index) use ($defaultIndex): array {
                $variant['is_default'] = $index === $defaultIndex;

                return $variant;
            })
            ->all();
    }

    private function mapCustomerSummary(array $customer): array
    {
        return [
            'id' => $customer['id'],
            'code' => $customer['code'],
            'name' => $customer['name'],
            'username' => $customer['username'],
            'phone' => $customer['phone'],
            'status' => $customer['status'],
            'status_key' => $customer['status_key'] ?? 'active',
            'address_count' => $customer['address_count'] ?? 0,
            'addresses' => [],
            'joined_at' => '-',
            'last_order' => '-',
            'total_orders' => 0,
            'total_spent' => 'Rp0',
        ];
    }

    private function mapCustomerDetail(array $customer): array
    {
        $summary = $this->mapCustomerSummary($customer);
        $summary['addresses'] = collect($customer['addresses'] ?? [])
            ->map(fn (array $address) => [
                'id' => $address['id'],
                'label' => $address['label'],
                'recipient_name' => $address['name'],
                'recipient_phone' => $address['phone'],
                'destination_id' => $address['destination_id'] ?? null,
                'province' => $address['province'],
                'city' => $address['city'],
                'district' => $address['district'],
                'subdistrict' => $address['subdistrict'],
                'postal_code' => $address['postal_code'],
                'address_line' => $address['address_line'],
                'address_note' => $address['note'] ?? '',
                'is_primary' => $address['is_primary'],
            ])
            ->values()
            ->all();
        $summary['address_count'] = count($summary['addresses']);

        return $summary;
    }

    private function normalizeCustomerAddresses(string $json): array
    {
        return collect(json_decode($json, true) ?: [])
            ->map(fn (array $address) => [
                'id' => $address['id'] ?? null,
                'label' => $address['label'] ?? 'Alamat',
                'recipient_name' => $address['recipient_name'] ?? '',
                'recipient_phone' => $address['recipient_phone'] ?? '',
                'province' => $address['province'] ?? '',
                'city' => $address['city'] ?? '',
                'district' => $address['district'] ?? '',
                'subdistrict' => $address['subdistrict'] ?? '',
                'postal_code' => $address['postal_code'] ?? '',
                'destination_id' => isset($address['destination_id']) && $address['destination_id'] !== '' ? (int) $address['destination_id'] : null,
                'address_line' => $address['address_line'] ?? '',
                'note' => $address['address_note'] ?? '',
                'is_primary' => (bool) ($address['is_primary'] ?? false),
            ])
            ->values()
            ->all();
    }

    private function shippingAddressSummary(array $address): string
    {
        return collect([
            $address['address_line'] ?? null,
            $address['subdistrict'] ?? null,
            isset($address['district']) ? 'Kec. '.$address['district'] : null,
            $address['city'] ?? null,
            $address['province'] ?? null,
            $address['postal_code'] ?? null,
        ])->filter()->implode(', ');
    }

    /**
     * Bersihkan HTML rich text dari Trix: hanya izinkan tag format aman,
     * buang atribut event handler / skema berbahaya (deskripsi tampil ke publik).
     */
    private function sanitizeRichText(string $html): string
    {
        $allowed = '<p><br><strong><b><em><i><u><h1><h2><h3><ul><ol><li><blockquote><pre><div><span>';
        $clean = strip_tags($html, $allowed);
        $clean = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', (string) $clean);
        $clean = preg_replace('/\sstyle\s*=\s*("[^"]*"|\'[^\']*\')/i', '', (string) $clean);

        return trim((string) $clean);
    }

    private function productCategories(): array
    {
        // Ambil kategori AKTIF dari API (sumber tunggal) -> slug => nama.
        // Supaya dropdown form produk & filter selalu sinkron dengan CRUD kategori.
        try {
            $map = collect($this->api->adminCategories())
                ->filter(fn (array $c) => $c['is_active'] ?? true)
                ->mapWithKeys(fn (array $c) => [$c['slug'] => $c['name']])
                ->all();

            if ($map !== []) {
                return $map;
            }
        } catch (\Throwable) {
            // Fallback ke default kalau API gagal.
        }

        return [
            'pupuk' => 'Pupuk',
            'benih' => 'Benih',
            'perlindungan-tanaman' => 'Perlindungan Tanaman',
        ];
    }

    private function productStatuses(): array
    {
        return [
            'draft' => 'Draft',
            'active' => 'Aktif',
            'inactive' => 'Nonaktif',
            'preorder' => 'Pre-order',
        ];
    }

    private function customerStatuses(): array
    {
        return [
            'active' => 'Aktif',
            'pending_verification' => 'Menunggu verifikasi',
            'inactive' => 'Nonaktif',
        ];
    }

    private function adminAccountRoles(): array
    {
        return ['Super Admin', 'Admin Operasional', 'Admin Gudang'];
    }

    private function adminAccountStatuses(): array
    {
        return ['Aktif', 'Nonaktif'];
    }

    private function adminRoleKey(string $label): string
    {
        return match ($label) {
            'Super Admin' => 'super_admin',
            'Admin Operasional' => 'operational_admin',
            default => 'warehouse_admin',
        };
    }

    private function adminStatusKey(string $label): string
    {
        return $label === 'Nonaktif' ? 'inactive' : 'active';
    }

    private function findAccountByCode(string $code): array
    {
        $account = collect($this->api->adminAccounts())->firstWhere('id', strtoupper($code));
        abort_if($account === null, 404);

        return $account;
    }

    private function findOrderByCode(string $code): array
    {
        $summary = collect($this->api->adminOrders())->firstWhere('code', strtoupper($code));
        abort_if($summary === null, 404);

        return $this->api->adminOrder((int) $summary['id']);
    }
}
