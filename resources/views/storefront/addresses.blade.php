<x-layouts.customer title="Larashop | Alamat">
    @php
        $openOnLoad = $errors->any();
        $formMode = old('_address_mode', 'create');
        $formAddressId = old('_address_id', '');
    @endphp

    <section class="space-y-6">
        <x-customer-section-title
            eyebrow="Alamat Pengiriman"
            title="Customer bisa menyimpan beberapa alamat untuk checkout lebih cepat"
            description="Kelola beberapa alamat kirim dan tentukan alamat utama yang akan dipakai otomatis saat checkout."
        />

        <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)] lg:items-start">
            <x-customer.account-nav />

            <div
                class="space-y-6"
                data-customer-address-manager
                data-search-url="{{ route('addresses.destination-search') }}"
                data-save-action="{{ route('addresses.save') }}"
                data-detail-template="{{ route('addresses.detail', ['id' => '__ID__']) }}"
                data-open-on-load="{{ $openOnLoad ? 'true' : 'false' }}"
                data-initial-mode="{{ $formMode }}"
                data-initial-address-id="{{ $formAddressId }}"
            >
                @if ($errors->any())
                    <div class="rounded-[1.75rem] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <section class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-stone-950">Alamat saya</h2>
                        </div>
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white"
                            data-address-open-create
                        >
                            Tambah alamat baru
                        </button>
                    </div>
                </section>

                <div class="space-y-4">
                    @foreach ($addresses as $address)
                        <x-customer.address-card :address="$address" />
                    @endforeach
                </div>

                <x-customer.address-modal-form :form-mode="$formMode" :form-address-id="$formAddressId" />
            </div>
        </div>
    </section>
</x-layouts.customer>
