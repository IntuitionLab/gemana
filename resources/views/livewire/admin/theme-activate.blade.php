<div class="bg-white border border-gray-200 rounded-xl p-6 flex items-center justify-between gap-6"
     x-data
     @theme-activated.window="$wire.active = ($event.detail.name === '{{ $name }}')"
>
    <div>
        <div class="flex items-center gap-2 mb-1">
            <h3 class="font-display text-[15px] font-bold text-gray-900">{{ $name }}</h3>
            @if($active)
                <span class="text-[10px] bg-[#EEF2FF] text-[#4F46E5] px-2 py-0.5 rounded-full font-medium">active</span>
            @endif
        </div>
        <p class="text-[13px] text-gray-500">{{ $description ?: 'No description provided.' }}</p>
        <p class="text-[11px] text-gray-400 mt-1.5">
            v{{ $version }}@if($author) · {{ $author }}@endif
        </p>
    </div>

    @if($active)
        <span class="shrink-0 inline-flex items-center px-4 py-2 rounded-lg text-[13px] font-medium bg-gray-100 text-gray-400 cursor-not-allowed">
            Current Theme
        </span>
    @else
        <button
            wire:click="activate"
            wire:loading.attr="disabled"
            class="shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-lg text-[13px] font-medium bg-[#4F46E5] text-white hover:bg-[#4338CA] transition-colors disabled:opacity-60"
        >
            <span wire:loading.remove wire:target="activate">Activate</span>
            <span wire:loading wire:target="activate" class="flex items-center gap-1.5">
                <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                Activating…
            </span>
        </button>
    @endif
</div>