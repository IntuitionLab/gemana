<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Gemana Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --dark-blue: #0b1a75;
            --blue:      #2025b1;
            --purple:    #4d1e99;
            --azure:     #2683d4;
            --vivid:     #21b7e7;
            --off-white: #f4f6fb;
            --muted:     #8492b4;
            --border:    #e4e9f5;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Lato', sans-serif; background: var(--off-white); }

        /* ── Sidebar ─────────────────────────────────────── */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: 220px;
            background: var(--dark-blue);
            display: flex; flex-direction: column;
            z-index: 40;
            transform: translateX(-100%);
            transition: transform 0.25s ease;
        }
        .sidebar.open { transform: translateX(0); }
        @media (min-width: 1024px) {
            .sidebar { transform: translateX(0); }
        }

        .sb-brand {
            padding: 20px 16px 16px;
            display: flex; align-items: center; gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            flex-shrink: 0;
        }
        .sb-brand img {
            width: 40px; height: 40px;
            background: #fff;
            border-radius: 8px;
            padding: 3px;}

        .sb-brand span {
            color: #fff;
            font-family: 'Montserrat', sans-serif;
            font-weight: 800; font-size: 13px;
            letter-spacing: 0.1em; text-transform: uppercase;
        }

        .sb-nav { flex: 1; overflow-y: auto; padding: 12px 0; }

        .sb-section {
            padding: 8px 16px 4px;
            font-size: 10px; font-weight: 700;
            letter-spacing: 0.12em; text-transform: uppercase;
            color: rgba(255,255,255,0.6);
            margin-top: 6px;
        }

        .sb-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 16px;
            font-size: 13px; color: rgba(255,255,255,0.75);
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
            border-left: 3px solid transparent;
        }
        .sb-item:hover { background: rgba(255,255,255,0.06); color: #fff; }
        .sb-item.active {
            background: rgba(33,183,231,0.15);
            color: var(--vivid);
            border-left-color: var(--vivid);
        }
        .sb-item svg { width: 18px; height: 18px; flex-shrink: 0; }

        .sb-footer {
            padding: 12px 16px;
            border-top: 1px solid rgba(255,255,255,0.12);
            flex-shrink: 0;
        }
        .sb-user { display: flex; align-items: center; gap: 10px; }
        .sb-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            overflow: hidden; flex-shrink: 0;
            background: linear-gradient(135deg, var(--azure), var(--vivid));
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: #fff;
        }
        .sb-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .sb-uname { color: #fff; font-size: 12px; font-weight: 700; }
        .sb-urole { color: rgba(255,255,255,0.5); font-size: 11px; }

        /* ── Overlay (mobile) ────────────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 39;
        }
        .sidebar-overlay.open { display: block; }

        /* ── Main area ───────────────────────────────────── */
        .main-wrap {
            min-height: 100vh;
            display: flex; flex-direction: column;
            transition: margin-left 0.25s ease;
        }
        @media (min-width: 1024px) {
            .main-wrap { margin-left: 220px; }
        }

        /* ── Topbar ──────────────────────────────────────── */
        .topbar {
            position: sticky; top: 0; z-index: 30;
            background: #fff;
            height: 56px;
            display: flex; align-items: center;
            padding: 0 20px;
            border-bottom: 1px solid var(--border);
            gap: 12px;
        }
        .topbar-hamburger {
            display: flex; align-items: center; justify-content: center;
            width: 36px; height: 36px;
            background: none; border: none; cursor: pointer;
            color: var(--dark-blue);
        }
        @media (min-width: 1024px) { .topbar-hamburger { display: none; } }

        .topbar-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700; font-size: 15px;
            color: var(--dark-blue);
            flex: 1;
        }
        .topbar-search {
            display: none;
            align-items: center; gap: 8px;
            background: var(--off-white);
            border: 1px solid var(--border);
            border-radius: 8px; padding: 6px 12px;
        }
        @media (min-width: 640px) { .topbar-search { display: flex; } }
        .topbar-search svg { width: 15px; height: 15px; color: var(--muted); }
        .topbar-search span { font-size: 13px; color: var(--muted); }

        .topbar-actions { display: flex; align-items: center; gap: 14px; }
        .topbar-icon {
            position: relative;
            background: none; border: none; cursor: pointer;
            color: var(--muted);
            display: flex; align-items: center; justify-content: center;
        }
        .topbar-icon svg { width: 22px; height: 22px; }
        .notif-dot {
            position: absolute; top: -1px; right: -1px;
            width: 8px; height: 8px;
            background: var(--vivid); border-radius: 50%;
            border: 2px solid #fff;
        }
        .topbar-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            overflow: hidden; cursor: pointer;
            background: linear-gradient(135deg, var(--azure), var(--vivid));
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; color: #fff;
        }
        .topbar-avatar img { width: 100%; height: 100%; object-fit: cover; }

        /* ── Page content ────────────────────────────────── */
        .page-content { flex: 1; padding: 20px 24px; }
        @media (max-width: 639px) { .page-content { padding: 16px; } }

        /* ── Bottom nav (mobile only) ────────────────────── */
        .bottom-nav {
            display: flex;
            background: var(--dark-blue);
            border-top: 1px solid rgba(255,255,255,0.1);
            position: sticky; bottom: 0; z-index: 30;
        }
        @media (min-width: 1024px) { .bottom-nav { display: none; } }
        .bn-item {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; gap: 3px;
            padding: 10px 4px;
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            font-size: 10px; font-weight: 700;
            letter-spacing: 0.04em;
            transition: color 0.15s;
        }
        .bn-item.active { color: var(--vivid); }
        .bn-item svg { width: 22px; height: 22px; }

        /* ── Flash messages ──────────────────────────────── */
        .flash {
            padding: 0.85rem 1rem; border-radius: 10px;
            font-size: 0.875rem; margin-bottom: 1.25rem; line-height: 1.5;
        }
        .flash-success { background: #e8fdf5; border: 1px solid #a3e9cc; color: #1a7a4e; }
        .flash-warning { background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; }
        .flash-error   { background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; }
    </style>
</head>
<body x-data="{ sidebarOpen: false }">

    {{-- Sidebar overlay (mobile) --}}
    <div class="sidebar-overlay" :class="{ open: sidebarOpen }" @click="sidebarOpen = false"></div>

    {{-- Sidebar --}}
    <aside class="sidebar" :class="{ open: sidebarOpen }">
        <div class="sb-brand">
            <img src="{{ asset('images/gemana-logo.svg') }}" alt="Gemana">
            <span>Gemana</span>
        </div>

        <nav class="sb-nav">
            <div class="sb-section">Member Portal</div>
            <a href="{{ route('portal.dashboard') }}" class="sb-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Dashboard
            </a>
            <a href="{{ route('portal.profile') }}" class="sb-item {{ request()->routeIs('portal.profile') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                My profile
            </a>
            <a href="#" class="sb-item {{ request()->routeIs('portal.events*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                Events
            </a>
            <a href="#" class="sb-item {{ request()->routeIs('portal.donations*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                Donations
            </a>
            <a href="#" class="sb-item {{ request()->routeIs('portal.documents*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                Documents
            </a>

            <div class="sb-section">Account</div>
            <a href="{{ route('portal.security') }}" class="sb-item {{ request()->routeIs('portal.security') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                Security & 2FA
            </a>
            <a href="#" class="sb-item">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                Preferences
            </a>
        </nav>

        <div class="sb-footer">
            <div class="sb-user">
                <div class="sb-avatar">
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ Storage::url(auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name)[1] ?? '', 0, 1)) }}
                    @endif
                </div>
                <div>
                    <div class="sb-uname">{{ auth()->user()->name }}</div>
                    <div class="sb-urole">{{ auth()->user()->membershipLevel?->name ?? 'Member' }}</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- Main wrapper --}}
    <div class="main-wrap">
        {{-- Topbar --}}
        <header class="topbar">
            <button class="topbar-hamburger" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle menu">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:24px;height:24px"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>

            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>

            <div class="topbar-search">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                <span>Search...</span>
            </div>

            <div class="topbar-actions">
                <button class="topbar-icon" aria-label="Notifications">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                    <span class="notif-dot"></span>
                </button>
                @include('partials.avatar-dropdown', ['variant' => 'portal'])
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success') || session('warning') || session('error'))
            <div class="page-content" style="padding-bottom:0">
                @if(session('success'))
                    <div class="flash flash-success">{{ session('success') }}</div>
                @endif
                @if(session('warning'))
                    <div class="flash flash-warning">{{ session('warning') }}</div>
                @endif
                @if(session('error'))
                    <div class="flash flash-error">{{ session('error') }}</div>
                @endif
            </div>
        @endif

        {{-- Page content --}}
        <main class="page-content">
            @yield('content')
        </main>

        {{-- Bottom nav (mobile) --}}
        <nav class="bottom-nav">
            <a href="{{ route('portal.dashboard') }}" class="bn-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Home
            </a>
            <a href="#" class="bn-item {{ request()->routeIs('portal.events*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                Events
            </a>
            <a href="#" class="bn-item {{ request()->routeIs('portal.donations*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                Donate
            </a>
            <a href="#" class="bn-item {{ request()->routeIs('portal.documents*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                Docs
            </a>
            <a href="{{ route('portal.profile') }}" class="bn-item {{ request()->routeIs('portal.profile') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                Profile
            </a>
        </nav>
    </div>

</body>
</html>