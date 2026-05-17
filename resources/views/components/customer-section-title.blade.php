@props([
    'eyebrow' => null,
    'title',
    'description' => null,
])

<div class="space-y-2">
    @if ($eyebrow)
        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-700">{{ $eyebrow }}</p>
    @endif
    <h2 class="text-2xl font-semibold tracking-tight text-stone-950 sm:text-3xl">{{ $title }}</h2>
    @if ($description)
        <p class="max-w-2xl text-sm leading-6 text-stone-600 sm:text-base">{{ $description }}</p>
    @endif
</div>
