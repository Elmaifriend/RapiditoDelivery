@props(['lat', 'lng', 'height' => 'h-40', 'zoom' => 14])

<div {{ $attributes->merge(['class' => "relative w-full {$height} bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 shadow-xs"]) }}>
<div
        x-init="
            $nextTick(() => {
                const init = () => {
                    const map = L.map($el, {
                        zoomControl: false,
                        attributionControl: false,
                        dragging: false,
                        scrollWheelZoom: false,
                        touchZoom: false,
                        doubleClickZoom: false
                    }).setView([{{ $lat }}, {{ $lng }}], {{ $zoom }});
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { maxZoom: 20 }).addTo(map);
                };

                if (typeof L !== 'undefined') {
                    init();
                    return;
                }

                const waitForLeaflet = () => {
                    if (typeof L !== 'undefined') {
                        init();
                        return;
                    }

                    setTimeout(waitForLeaflet, 100);
                };

                setTimeout(waitForLeaflet, 100);
            })
        "
        class="absolute inset-0 z-0 h-full w-full outline-none"
        wire:ignore
    ></div>
    <div class="pointer-events-none absolute left-1/2 top-1/2 z-10 flex -translate-x-1/2 -translate-y-[90%] flex-col items-center">
        <x-ui.map-pin/>
    </div>
</div>
