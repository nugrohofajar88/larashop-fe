@props([
    'address',
    'editable' => true,
    'showPrimaryAction' => true,
])

<article class="rounded-[1.75rem] border border-stone-200 bg-white p-5 shadow-sm">
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <p class="text-sm font-semibold text-stone-950">{{ $address['label'] }}</p>
                @if ($address['is_primary'])
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Utama</span>
                @endif
            </div>
            <p class="mt-2 text-sm text-stone-500">{{ $address['name'] }} · {{ $address['phone'] }}</p>
        </div>
    </div>

    <p class="mt-4 text-sm leading-6 text-stone-600">{{ $address['detail'] }}</p>

    @if ($editable)
        <div class="mt-4 flex flex-wrap gap-3">
            <button
                type="button"
                class="rounded-full border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700"
                data-address-open-edit
                data-address-id="{{ $address['id'] }}"
                data-address-destination-id="{{ $address['destination_id'] ?? '' }}"
                data-address-label="{{ $address['label'] }}"
                data-address-is-primary="{{ $address['is_primary'] ? '1' : '0' }}"
                data-address-recipient-name="{{ $address['name'] }}"
                data-address-recipient-phone="{{ $address['phone'] }}"
                data-address-province="{{ $address['province'] }}"
                data-address-city="{{ $address['city'] }}"
                data-address-district="{{ $address['district'] }}"
                data-address-subdistrict="{{ $address['subdistrict'] }}"
                data-address-postal-code="{{ $address['postal_code'] }}"
                data-address-line="{{ $address['address_line'] }}"
                data-address-note="{{ $address['note'] }}"
                data-address-destination-label="{{ $address['subdistrict'] }}, {{ $address['district'] }}, {{ $address['city'] }}, {{ $address['province'] }}, {{ $address['postal_code'] }}"
            >
                Edit
            </button>

            @if ($showPrimaryAction && ! $address['is_primary'])
                <form method="POST" action="{{ route('addresses.primary', $address['id']) }}">
                    @csrf
                    <button type="submit" class="rounded-full bg-stone-900 px-4 py-2 text-sm font-medium text-white">
                        Jadikan utama
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('addresses.destroy', $address['id']) }}" onsubmit="return confirm('Hapus alamat ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-full bg-rose-50 px-4 py-2 text-sm font-medium text-rose-700">
                    Hapus
                </button>
            </form>
        </div>
    @endif
</article>
