@props([
    'eyebrow',
    'title',
    'description' => null,
])

<div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
    <div>
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">{{ $eyebrow }}</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">{{ $title }}</h1>
        @if ($description)
            <p class="mt-3 max-w-3xl text-sm leading-6 text-stone-600">
                {{ $description }}
            </p>
        @endif
    </div>

    @if (isset($actions))
        <div class="flex gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
