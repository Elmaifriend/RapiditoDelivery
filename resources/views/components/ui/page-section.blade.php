@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-4 px-4']) }}>
    @isset($title)
        <div>
            <h2 class="line-clamp-2 break-words font-bold text-gray-800">{{ $title }}</h2>
            @isset($subtitle)
                <h3 class="text-sm font-medium text-gray-400">{{ $subtitle }}</h3>
            @endisset
        </div>
    @endisset

    <div class="flex flex-col gap-4">
        {{ $slot }}
    </div>
</div>
