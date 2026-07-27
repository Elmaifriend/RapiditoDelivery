@props(['label', 'color' => 'bg-red-500', 'icon' => 'bxf bx-chevron-right', 'iconColor' => 'text-red-500', 'action'])

<div x-data="{ 
    startX: 0, currentX: 0, maxSwipe: 0, completed: false, isDragging: false,
    dragStart(e) {
        if (this.completed) return;
        this.maxSwipe = this.$refs.track.clientWidth - this.$refs.thumb.clientWidth - 8; 
        this.isDragging = true;
        this.startX = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;
    },
    dragMove(e) {
        if (!this.isDragging || this.completed) return;
        const x = e.type.startsWith('touch') ? e.touches[0].clientX : e.clientX;
        let diff = x - this.startX;
        if (diff < 0) diff = 0;
        if (diff > this.maxSwipe) diff = this.maxSwipe;
        this.currentX = diff;
    },
    dragEnd() {
        if (!this.isDragging || this.completed) return;
        this.isDragging = false;
        if (this.currentX >= this.maxSwipe * 0.85) {
            this.currentX = this.maxSwipe;
            this.completed = true;
            {{ $action }};
        } else {
            this.currentX = 0;
        }
    }
}" 
{{ $attributes }}
class="relative select-none">
    <div x-ref="track" @mousemove="dragMove" @mouseup="dragEnd" @mouseleave="dragEnd" @touchmove.prevent="dragMove" @touchend="dragEnd" class="h-14 {{ $color }} rounded-2xl p-1 flex items-center justify-center relative overflow-hidden shadow-inner">
        <span :style="`opacity: ${maxSwipe > 0 ? 1 - (currentX / maxSwipe) : 1}; filter: blur(${maxSwipe > 0 ? (currentX / maxSwipe) * 4 : 0}px)`" class="text-xs font-bold text-white tracking-wider uppercase opacity-90 pointer-events-none transition-all duration-75">
            {{ $label }}
        </span>
        <div x-ref="thumb" :style="`transform: translateX(${currentX}px)`" @mousedown="dragStart" @touchstart="dragStart" class="absolute left-1 top-1 bottom-1 w-12 bg-white rounded-xl flex items-center justify-center shadow-lg cursor-pointer transition-transform duration-75">
            <i class="{{ $icon }} text-xl {{ $iconColor }}"></i>
        </div>
    </div>
</div>
