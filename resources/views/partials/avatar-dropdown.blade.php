{{-- resources/views/partials/avatar-dropdown.blade.php --}}
{{-- Usage: @include('partials.avatar-dropdown', ['variant' => 'portal']) --}}
{{-- variant options: 'portal', 'admin' --}}

<div x-data="{ open: false }" class="relative" @keydown.escape="open = false">

    {{-- Trigger --}}
    <button
        @click="open = !open"
        @click.outside="open = false"
        class="topbar-avatar focus:outline-none"
        aria-haspopup="true"
        :aria-expanded="open"
        aria-label="User menu"
    >
        @if(auth()->user()->profile_photo_path)
            <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}">
        @else
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name)[1] ?? '', 0, 1)) }}
        @endif
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
                    {{ auth()->user()->membershipLevel?->name ?? ucfirst(auth()->user()->getRoleNames()->first() ?? 'Member') }}
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
            @if($variant === 'portal')
                <a href="{{ route('portal.profile') }}" style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;font-size:13px;color:#0b1a75;font-weight:700;text-decoration:none;transition:background 0.12s;" onmouseover="this.style.background='#f4f6fb'" onmouseout="this.style.background='transparent'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#8492b4" style="width:17px;height:17px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    My profile
                </a>
                <a href="{{ route('portal.security') }}" style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;font-size:13px;color:#0b1a75;font-weight:700;text-decoration:none;" onmouseover="this.style.background='#f4f6fb'" onmouseout="this.style.background='transparent'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#8492b4" style="width:17px;height:17px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                    Security & 2FA
                </a>
                <a href="#" style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;font-size:13px;color:#0b1a75;font-weight:700;text-decoration:none;" onmouseover="this.style.background='#f4f6fb'" onmouseout="this.style.background='transparent'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#8492b4" style="width:17px;height:17px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Preferences
                </a>
            @elseif($variant === 'admin')
                <a href="{{ route('admin.dashboard') }}" style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;font-size:13px;color:#0b1a75;font-weight:700;text-decoration:none;" onmouseover="this.style.background='#f4f6fb'" onmouseout="this.style.background='transparent'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#8492b4" style="width:17px;height:17px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.settings') }}" style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;font-size:13px;color:#0b1a75;font-weight:700;text-decoration:none;" onmouseover="this.style.background='#f4f6fb'" onmouseout="this.style.background='transparent'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#8492b4" style="width:17px;height:17px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Settings
                </a>
            @endif

            <div style="height:0.5px;background:#f0f2f8;margin:4px 6px;"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" style="display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;font-size:13px;color:#b91c1c;font-weight:700;text-decoration:none;width:100%;background:none;border:none;cursor:pointer;text-align:left;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#fca5a5" style="width:17px;height:17px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                    Sign out
                </button>
            </form>
        </div>
    </div>

</div>
