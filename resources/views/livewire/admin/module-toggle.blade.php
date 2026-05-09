<div
    class="bg-white rounded-2xl flex flex-col justify-between gap-4 transition-all duration-200"
    style="padding: 1.25rem; border: 1.5px solid #dde3f0; box-shadow: 0 2px 12px rgba(11,26,117,0.06);"
>
    {{-- Module Info --}}
    <div>
        <div class="flex items-center gap-2 mb-1.5">
            {{-- Module icon circle --}}
            <div
                class="flex items-center justify-center w-8 h-8 rounded-lg text-white text-xs font-bold flex-shrink-0"
                style="background: linear-gradient(135deg, #0b1a75, #2025b1);"
            >
                {{ strtoupper(substr($name, 0, 1)) }}
            </div>

            <h3 style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 14px; color: #0b1a75;">
                {{ $name }}
            </h3>

            @if($core)
                <span
                    class="text-[10px] px-2 py-0.5 rounded-full font-bold tracking-wide uppercase"
                    style="background: rgba(33,183,231,0.12); color: #0b1a75;"
                >
                    Core
                </span>
            @endif
        </div>

        <p style="font-size: 12.5px; color: #8492b4; line-height: 1.5; margin-left: 40px;">
            {{ $description ?: 'No description provided.' }}
        </p>

        <p style="font-size: 11px; color: #b0bcd4; margin-top: 4px; margin-left: 40px;">
            v{{ $version }}
        </p>
    </div>

    {{-- Divider --}}
    <div style="border-top: 1px solid #dde3f0;"></div>

    {{-- Toggle Row --}}
    <div class="flex items-center justify-between">

        {{-- Status pill --}}
        <span
            class="inline-flex items-center gap-1.5 text-[12px] font-bold px-2.5 py-1 rounded-full"
            style="{{ $enabled
                ? 'background: rgba(39,174,96,0.10); color: #27ae60;'
                : 'background: rgba(132,146,180,0.12); color: #8492b4;' }}"
        >
            <span
                class="w-1.5 h-1.5 rounded-full inline-block"
                style="{{ $enabled ? 'background: #27ae60;' : 'background: #8492b4;' }}"
            ></span>
            {{ $enabled ? 'Enabled' : 'Disabled' }}
        </span>

        {{-- Toggle control --}}
        @if($core)
            <span
                class="text-[11px] px-3 py-1.5 rounded-lg font-bold tracking-wide uppercase"
                style="background: #f4f6fb; color: #b0bcd4; cursor: not-allowed;"
            >
                Always On
            </span>
        @else
            <button
                wire:click="toggle"
                wire:loading.attr="disabled"
                title="{{ $enabled ? 'Disable' : 'Enable' }} {{ $name }}"
                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2"
                style="{{ $enabled
                    ? 'background: linear-gradient(135deg, #0b1a75, #2025b1); focus-ring-color: #2025b1;'
                    : 'background: #dde3f0;' }}"
            >
                {{-- Loading spinner --}}
                <span
                    wire:loading
                    wire:target="toggle"
                    class="absolute inset-0 flex items-center justify-center"
                >
                    <svg class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                </span>

                {{-- Knob --}}
                <span
                    wire:loading.class="opacity-0"
                    wire:target="toggle"
                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform duration-200"
                    style="{{ $enabled ? 'transform: translateX(24px);' : 'transform: translateX(4px);' }}"
                ></span>
            </button>
        @endif

    </div>
</div>