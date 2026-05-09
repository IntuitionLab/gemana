{{-- resources/views/partials/nav-dropdown.blade.php --}}
{{-- Used on the welcome page when user is logged in --}}

<div x-data="{ open: false }" class="relative" @keydown.escape="open = false">

    {{-- Trigger --}}
    <button
        @click="open = !open"
        @click.outside="open = false"
        style="display:flex;align-items:center;gap:8px;background:none;border:none;cursor:pointer;padding:0;"
        aria-haspopup="true"
        :aria-expanded="open"
    >
        <div style="width:34px;height:34px;border-radius:50%;overflow:hidden;flex-shrink:0;
                    background:linear-gradient(135deg,#2683d4,#21b7e7);
                    display:flex;align-items:center;justify-content:center;
                    font-size:12px;font-weight:700;color:#fff;">
            @if(auth()->user()->profile_photo_path)
                <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" style="width:100%;height:100%;object-fit:cover">
            @else
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name)[1] ?? '', 0, 1)) }}
            @endif
        </div>
        <span style="font-family:'Montserrat',sans-serif;font-weight:700;font-size:13px;color:#0b1a75;">
            {{ explode(' ', auth()->user()->name)[0] }}
        </span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#8492b4"
             style="width:14px;height:14px;transition:transform 0.2s;"
             :style="open ? 'transform:rotate(180deg)' : ''">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    {{-- Dropdown panel --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
        style="display:none;position:absolute;right:0;top:calc(100% + 10px);width:224px;z-index:100;
               background:#fff;border-radius:14px;border:0.5px solid #e4e9f5;
               box-shadow:0 4px 24px rgba(11,26,117,0.12),0 1px 4px rgba(11,26,117,0.06);
               overflow:hidden;"
    >
        {{-- User header --}}
        <div style="padding:14px 16px 12px;border-bottom:0.5px solid #f0f2f8;display:flex;align-items:center;gap:10px;">
            <div style="width:38px;height:38px;border-radius:50%;overflow:hidden;flex-shrink:0;
                        background:linear-gradient(135deg,#2683d4,#21b7e7);
                        display:flex;align-items:center;justify-content:center;
                        font-size:13px;font-weight:700;color:#fff;">
                @if(auth()->user()->profile_photo_path)
                    <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" style="width:100%;height:100%;object-fit:cover">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name)[1] ?? '', 0, 1)) }}
                @endif
            </div>
            <div style="min-width:0">
                <div style="font-family:'Montserrat',sans-serif;font-weight:700;font-size:13px;color:#0b1a75;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    {{ auth()->user()->name }}
                </div>
                <div style="font-size:11px;color:#8492b4;margin-top:1px;">
                    {{ auth()->user()->membershipLevel?->name ?? 'Member' }}
                </div>
                <div style="display:inline-block;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;
                            background:#e6f1fb;color:#185fa5;margin-top:4px;
                            font-family:'Montserrat',sans-serif;letter-spacing:0.04em;">
                    {{ auth()->user()->statusLabel() }}
                </div>
            </div>
        </div>

        {{-- Menu items --}}
        <div style="padding:6px;">
            @php
                $home = auth()->user()->hasAnyRole(['super-admin','admin','team']) ? '/admin' : '/portal/dashboard';
            @endphp
            <a href="{{ $home }}" style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;font-size:13px;color:#0b1a75;font-weight:700;text-decoration:none;" onmouseover="this.style.background='#f4f6fb'" onmouseout="this.style.background='transparent'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#8492b4" style="width:17px;height:17px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Go to dashboard
            </a>

            <div style="height:0.5px;background:#f0f2f8;margin:4px 6px;"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;font-size:13px;color:#b91c1c;font-weight:700;width:100%;background:none;border:none;cursor:pointer;text-align:left;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#fca5a5" style="width:17px;height:17px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                    Sign out
                </button>
            </form>
        </div>
    </div>

</div>
