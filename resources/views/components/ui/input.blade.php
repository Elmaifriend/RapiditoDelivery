@props(['label' => null, 'error' => null])

<div class="space-y-1.5 w-full">
    @if($label)
        <label class="block text-xs font-medium text-gray-400">{{ $label }}</label>
    @endif
    <input {{ $attributes->merge(['class' => 'w-full rounded-2xl border border-gray-100 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800 placeholder-gray-400 transition-all duration-200 focus:border-red-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-red-500/10']) }}>
    @if($error)
        <span class="mt-1 block text-xs font-bold text-red-500">{{ $error }}</span>
    @endif
</div>
