@props([
    'title' => null,
    'description' => null,
])

<section class="rounded-[2rem] border border-stone-200 bg-white p-5 shadow-sm">
    @if (filled($title))
        <h2 class="text-xl font-semibold text-stone-950">{{ $title }}</h2>
    @endif
    @if ($description)
        <p class="mt-1 text-sm text-stone-500">{{ $description }}</p>
    @endif

    <div {{ $attributes->merge(['class' => filled($title) || $description ? 'mt-5' : '']) }}>
        {{ $slot }}
    </div>
</section>
