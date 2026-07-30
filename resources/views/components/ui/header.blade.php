@props(['title', 'backUrl' => null])

<div {{ $attributes->merge(['class' => 'flex gap-4 items-center']) }}>
    @if($backUrl)
        <x-ui.icon-button :href="$backUrl" icon="bxf bx-chevron-left" />
    @else
        <x-ui.icon-button onclick="history.back();" icon="bxf bx-chevron-left" />
    @endif
    <h2 class="text-lg font-bold tracking-tight text-gray-900">{{ $title }}</h2>
</div>
