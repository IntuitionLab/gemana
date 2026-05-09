<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Gemana Admin</title>
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

        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: 220px; background: var(--dark-blue);
            display: flex; flex-direction: column;
            z-index: 40;
            transform: translateX(-100%);
            transition: transform 0.25s ease;
        }
        .sidebar.open { transform: translateX(0); }
        @media (min-width: 1024px) { .sidebar { transform: translateX(0); } }

        .sb-brand {
            padding: 20px 16px 16px;
            display: flex; align-items: center; gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.12); flex-shrink: 0;
        }
        .sb-brand img { width: 36px; height: 36px; }
        .sb-brand span {
            color: #fff; font-family: 'Montserrat', sans-serif;
            font-weight: 800; font-size: 13px;
            letter-spacing: 0.1em; text-transform: uppercase;
        }
        .sb-nav { flex: 1; overflow-y: auto; padding: 12px 0; }
        .sb-section {
            padding: 8px 16px 4px; font-size: 10px; font-weight: 700;
            letter-spacing: 0.12em; text-transform: uppercase;
            color: rgba(255,255,255,0.6); margin-top: 6px;
        }
        .sb-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 16px; font-size: 13px;
            color: rgba(255,255,255,0.75); text-decoration: none;
            transition: background 0.15s, color 0.15s;
            border-left: 3px solid transparent;
        }
        .sb-item:hover { background: rgba(255,255,255,0.06); color: #fff; }
        .sb-item.active { background: rgba(33,183,231,0.15); color: var(--vivid); border-left-color: var(--vivid); }
        .sb-item svg { width: 18px; height: 18px; flex-shrink: 0; }
        .sb-footer {
            padding: 12px 16px;
            border-top: 1px solid rgba(255,255,255,0.12); flex-shrink: 0;
        }
        .sb-user { display: flex; align-items: center; gap: 10px; }
        .sb-avatar {
            width: 34px; height: 34px; border-radius: 50%; overflow: hidden;
            flex-shrink: 0; background: linear-gradient(135deg, var(--purple), var(--azure));
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; color: #fff;
        }
        .sb-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .sb-uname { color: #fff; font-size: 12px; font-weight: 700; }
        .sb-urole { color: rgba(255,255,255,0.5); font-size: 11px; }

        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 39; }
        .sidebar-overlay.open { display: block; }

        .main-wrap { min-height: 100vh; display: flex; flex-direction: column; }
        @media (min-width: 1024px) { .main-wrap { margin-left: 220px; } }

        .topbar {
            position: sticky; top: 0; z-index: 30; background: #fff;
            height: 56px; display: flex; align-items: center;
            padding: 0 20px; border-bottom: 1px solid var(--border); gap: 12px;
        }
        .topbar-hamburger {
            display: flex; align-items: center; justify-content: center;
            width: 36px; height: 36px; background: none; border: none;
            cursor: pointer; color: var(--dark-blue);
        }
        @media (min-width: 1024px) { .topbar-hamburger { display: none; } }
        .topbar-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700; font-size: 15px; color: var(--dark-blue); flex: 1;
        }
        .topbar-actions { display: flex; align-items: center; gap: 12px; }
        .topbar-icon {
            position: relative; background: none; border: none;
            cursor: pointer; color: var(--muted);
            display: flex; align-items: center; justify-content: center;
        }
        .topbar-icon svg { width: 22px; height: 22px; }
        .notif-dot {
            position: absolute; top: -1px; right: -1px;
            width: 8px; height: 8px; background: #e74c3c;
            border-radius: 50%; border: 2px solid #fff;
        }
        .topbar-btn {
            background: linear-gradient(135deg, var(--blue), var(--vivid));
            color: #fff; border: none; border-radius: 8px;
            padding: 7px 14px; font-size: 12px;
            font-family: 'Montserrat', sans-serif; font-weight: 700;
            letter-spacing: 0.06em; cursor: pointer; white-space: nowrap;
        }
        .topbar-avatar {
            width: 32px; height: 32px; border-radius: 50%; overflow: hidden;
            cursor: pointer; background: linear-gradient(135deg, var(--purple), var(--azure));
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; color: #fff;
        }
        .topbar-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .page-content { flex: 1; padding: 20px 24px; }
        @media (max-width: 639px) { .page-content { padding: 16px; } }

        .bottom-nav {
            display: flex; background: var(--dark-blue);
            border-top: 1px solid rgba(255,255,255,0.1);
            position: sticky; bottom: 0; z-index: 30;
        }
        @media (min-width: 1024px) { .bottom-nav { display: none; } }
        .bn-item {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; gap: 3px; padding: 10px 4px;
            color: rgba(255,255,255,0.5); text-decoration: none;
            font-size: 10px; font-weight: 700; letter-spacing: 0.04em;
        }
        .bn-item.active { color: var(--vivid); }
        .bn-item svg { width: 22px; height: 22px; }

        .flash { padding: 0.85rem 1rem; border-radius: 10px; font-size: 0.875rem; margin-bottom: 1.25rem; line-height: 1.5; }
        .flash-success { background: #e8fdf5; border: 1px solid #a3e9cc; color: #1a7a4e; }
        .flash-warning { background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; }
        .flash-error   { background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; }
    </style>
</head>
<body x-data="{ sidebarOpen: false }">

    <div class="sidebar-overlay" :class="{ open: sidebarOpen }" @click="sidebarOpen = false"></div>

    <aside class="sidebar" :class="{ open: sidebarOpen }">
        <div class="sb-brand">
            <img src="{{ asset('images/gemana-logo.svg') }}" alt="Gemana">
            <span>Gemana</span>
        </div>
        <nav class="sb-nav">
            <div class="sb-section">Administration</div>
            <a href="{{ route('admin.dashboard') }}" class="sb-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.members') }}" class="sb-item {{ request()->routeIs('admin.members*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                Members
            </a>
            <a href="{{ route('admin.donations.index') }}" class="sb-item {{ request()->routeIs('admin.donations.index') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                Donations
            </a>
            <a href="#" class="sb-item {{ request()->routeIs('admin.events*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                Events
            </a>
            <a href="#" class="sb-item {{ request()->routeIs('admin.blog*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" /></svg>
                Blog
            </a>
            <a href="#" class="sb-item {{ request()->routeIs('admin.documents*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg>
                Documents
            </a>
            <a href="#" class="sb-item {{ request()->routeIs('admin.newsletter*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                Newsletter
            </a>
            <a href="#" class="sb-item {{ request()->routeIs('admin.volunteering*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                Volunteering
            </a>

            <div class="sb-section">System</div>
            <a href="{{ route('admin.modules') }}" class="sb-item {{ request()->routeIs('admin.modules') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.4.959.4v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z" /></svg>
                Modules
            </a>
            <a href="{{ route('admin.themes') }}" class="sb-item {{ request()->routeIs('admin.themes') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 005.304 0l6.401-6.402M6.75 21A3.75 3.75 0 013 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 003.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008z" /></svg>
                Themes
            </a>
            <a href="{{ route('admin.settings') }}" class="sb-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                Settings
            </a>
        </nav>
        <div class="sb-footer">
            <div class="sb-user">
                @include('partials.avatar-dropdown', ['variant' => 'portal'])
                <div>
                    <div class="sb-uname">{{ auth()->user()->name }}</div>
                    <div class="sb-urole">{{ ucfirst(auth()->user()->getRoleNames()->first() ?? 'Admin') }}</div>
                </div>
            </div>
        </div>
    </aside>

    <div class="main-wrap">
        <header class="topbar">
            <button class="topbar-hamburger" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle menu">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:24px;height:24px"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
            <div class="topbar-title">@yield('page-title', 'Admin Dashboard')</div>
            <div class="topbar-actions">
                <button class="topbar-icon" aria-label="Notifications">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                    <span class="notif-dot"></span>
                </button>
                @yield('topbar-actions')
                @include('partials.avatar-dropdown', ['variant' => 'admin'])
            </div>
        </header>

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

        <main class="page-content">
            @yield('content')
        </main>

        <nav class="bottom-nav">
            <a href="{{ route('admin.dashboard') }}" class="bn-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                Home
            </a>
            <a href="{{ route('admin.members') }}" class="bn-item {{ request()->routeIs('admin.members*') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                Members
            </a>
            <a href="#" class="bn-item">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                Events
            </a>
            <a href="{{ route('admin.modules') }}" class="bn-item {{ request()->routeIs('admin.modules') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.4.959.4v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z" /></svg>
                Modules
            </a>
            <a href="{{ route('admin.settings') }}" class="bn-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                Settings
            </a>
        </nav>
    </div>

</body>
</html>