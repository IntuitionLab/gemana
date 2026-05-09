<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gemana — Community Management Platform</title>
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

        /* ── Nav ───────────────────────────────────────────── */
        .nav {
            background: #fff;
            height: 64px;
            padding: 0 40px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 50;
        }
        .nav-brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-logo {
            width: 38px; height: 38px; flex-shrink: 0;
            background: #fff; border-radius: 8px; padding: 3px;
        }
        .nav-logo img { width: 100%; height: 100%; }
        .nav-name {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800; font-size: 16px;
            letter-spacing: 0.1em; text-transform: uppercase;
            background: linear-gradient(135deg, var(--dark-blue), var(--vivid));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav-links { display: flex; align-items: center; gap: 4px; }
        .nav-link {
            color: var(--muted); font-size: 13px; font-weight: 700;
            padding: 7px 14px; border-radius: 7px; text-decoration: none;
            transition: color 0.15s;
        }
        .nav-link:hover { color: var(--dark-blue); }
        .nav-signin {
            color: var(--dark-blue); font-size: 13px; font-weight: 700;
            padding: 7px 14px; text-decoration: none;
        }
        .nav-cta {
            background: linear-gradient(135deg, var(--blue), var(--vivid));
            color: #fff; font-family: 'Montserrat', sans-serif;
            font-weight: 700; font-size: 12px;
            letter-spacing: 0.06em; text-transform: uppercase;
            padding: 9px 20px; border-radius: 8px; text-decoration: none;
            margin-left: 4px;
            transition: opacity 0.2s, transform 0.15s;
        }
        .nav-cta:hover { opacity: 0.9; transform: translateY(-1px); }

        /* Mobile nav */
        .nav-mobile { display: none; }
        @media (max-width: 640px) {
            .nav { padding: 0 20px; }
            .nav-links { display: none; }
            .nav-mobile { display: flex; align-items: center; gap: 8px; }
        }

        /* ── Hero ──────────────────────────────────────────── */
        .hero {
            background: linear-gradient(160deg, var(--dark-blue) 0%, var(--blue) 45%, var(--azure) 80%, var(--vivid) 100%);
            padding: 72px 40px 88px;
            text-align: center;
            position: relative; overflow: hidden;
        }
        .hero-orb1 {
            position: absolute; top: -80px; right: -80px;
            width: 400px; height: 400px; border-radius: 50%;
            background: radial-gradient(circle, rgba(33,183,231,0.14) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-orb2 {
            position: absolute; bottom: -100px; left: -100px;
            width: 380px; height: 380px; border-radius: 50%;
            background: radial-gradient(circle, rgba(77,30,153,0.16) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-logo-circle {
            width: 106px; height: 106px; border-radius: 50%;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
            position: relative; z-index: 1;
            box-shadow: 0 0 0 10px rgba(255,255,255,0.12);
        }
        .hero-logo-circle img { width: 100px; height: 100px; }

        .hero-eyebrow {
            display: inline-block;
            background: rgba(33,183,231,0.2);
            border: 1px solid rgba(33,183,231,0.4);
            border-radius: 20px; padding: 5px 16px;
            font-size: 11px; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: #7de8f8; font-family: 'Montserrat', sans-serif;
            margin-bottom: 22px; position: relative; z-index: 1;
        }

        .hero h1 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800; font-size: 46px; color: #fff;
            line-height: 1.12; letter-spacing: -0.02em;
            margin-bottom: 18px; position: relative; z-index: 1;
        }
        @media (max-width: 640px) { .hero h1 { font-size: 32px; } }

        .hero h1 .gradient-text {
            background: linear-gradient(90deg, var(--vivid), #fff);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-sub {
            font-size: 16px; color: rgba(255,255,255,0.82);
            line-height: 1.75; max-width: 520px;
            margin: 0 auto 36px; position: relative; z-index: 1;
        }

        .hero-btns {
            display: flex; align-items: center; justify-content: center;
            gap: 12px; position: relative; z-index: 1;
            flex-wrap: wrap;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--blue), var(--vivid));
            color: #fff; font-family: 'Montserrat', sans-serif;
            font-weight: 700; font-size: 13px;
            letter-spacing: 0.06em; text-transform: uppercase;
            padding: 14px 30px; border-radius: 10px; text-decoration: none;
            box-shadow: 0 4px 20px rgba(32,37,177,0.45);
            transition: opacity 0.2s, transform 0.15s;
        }
        .btn-primary:hover { opacity: 0.92; transform: translateY(-1px); }
        .btn-outline {
            background: rgba(255,255,255,0.1);
            border: 1.5px solid rgba(255,255,255,0.35);
            color: #fff; font-family: 'Montserrat', sans-serif;
            font-weight: 700; font-size: 13px;
            letter-spacing: 0.06em; text-transform: uppercase;
            padding: 14px 30px; border-radius: 10px; text-decoration: none;
            transition: background 0.2s;
        }
        .btn-outline:hover { background: rgba(255,255,255,0.18); }

        /* ── Sections ──────────────────────────────────────── */
        .section { padding: 64px 40px; }
        @media (max-width: 640px) { .section { padding: 48px 20px; } }

        .section-label {
            font-size: 11px; font-weight: 700;
            letter-spacing: 0.12em; text-transform: uppercase;
            color: var(--muted); text-align: center; margin-bottom: 10px;
        }
        .section-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800; font-size: 26px; color: var(--dark-blue);
            text-align: center; margin-bottom: 44px;
        }

        /* ── Features ──────────────────────────────────────── */
        .features { background: #fff; }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }
        @media (max-width: 768px) {
            .features-grid { grid-template-columns: 1fr; }
        }
        .feat {
            background: var(--off-white); border-radius: 16px;
            padding: 24px; border: 0.5px solid var(--border);
        }
        .feat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px; font-size: 22px;
        }
        .feat-icon-blue   { background: linear-gradient(135deg, var(--dark-blue), var(--blue)); color: #fff; }
        .feat-icon-azure  { background: linear-gradient(135deg, var(--azure), var(--vivid)); color: #fff; }
        .feat-icon-purple { background: linear-gradient(135deg, var(--purple), var(--blue)); color: #fff; }
        .feat h3 {
            font-family: 'Montserrat', sans-serif; font-weight: 700;
            font-size: 14px; color: var(--dark-blue); margin-bottom: 8px;
        }
        .feat p { font-size: 13px; color: var(--muted); line-height: 1.65; }

        /* ── Modules ───────────────────────────────────────── */
        .modules-section { background: var(--off-white); }
        .modules-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }
        @media (max-width: 640px) {
            .modules-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        .mod {
            background: #fff; border-radius: 14px;
            padding: 20px 14px; border: 0.5px solid var(--border);
            text-align: center;
            transition: border-color 0.2s, transform 0.15s;
        }
        .mod:hover { border-color: var(--vivid); transform: translateY(-2px); }
        .mod-icon { font-size: 26px; color: var(--blue); margin-bottom: 10px; }
        .mod-name {
            font-family: 'Montserrat', sans-serif; font-weight: 700;
            font-size: 12px; color: var(--dark-blue); letter-spacing: 0.04em;
        }

        /* ── CTA ───────────────────────────────────────────── */
        .cta-section {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--blue) 60%, var(--azure) 100%);
            padding: 72px 40px; text-align: center;
        }
        .cta-section h2 {
            font-family: 'Montserrat', sans-serif; font-weight: 800;
            font-size: 30px; color: #fff; margin-bottom: 14px;
        }
        .cta-section p {
            font-size: 15px; color: rgba(255,255,255,0.8);
            margin-bottom: 32px; max-width: 400px; margin-left: auto; margin-right: auto;
        }

        /* ── Footer ────────────────────────────────────────── */
        .footer {
            background: var(--dark-blue);
            padding: 24px 40px;
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px;
        }
        @media (max-width: 640px) { .footer { padding: 20px; } }
        .footer-brand { display: flex; align-items: center; gap: 10px; }
        .footer-logo {
            width: 28px; height: 28px; background: #fff;
            border-radius: 6px; padding: 2px;
        }
        .footer-logo img { width: 100%; height: 100%; }
        .footer-name {
            font-family: 'Montserrat', sans-serif; font-weight: 800;
            font-size: 12px; letter-spacing: 0.1em; text-transform: uppercase;
            color: rgba(255,255,255,0.5);
        }
        .footer-copy { font-size: 11px; color: rgba(255,255,255,0.3); }
    </style>
</head>
<body x-data>
    {{-- Nav --}}
    <nav class="nav">
        <a href="/" class="nav-brand">
            <div class="nav-logo">
                <img src="{{ asset('images/gemana-logo.svg') }}" alt="Gemana">
            </div>
            <span class="nav-name">Gemana</span>
        </a>
        <div class="nav-links">
            <a href="#features" class="nav-link">Features</a>
            <a href="#modules" class="nav-link">Modules</a>
           @auth
    @include('partials.nav-dropdown')
@else
    <a href="{{ route('login') }}" class="nav-signin">Sign in</a>
    <a href="{{ route('register') }}" class="nav-cta">Get started</a>
@endauth
        </div>
        {{-- Mobile --}}
        <div class="nav-mobile">
           @auth
    @include('partials.nav-dropdown')
@else
    <a href="{{ route('login') }}" class="nav-signin">Sign in</a>
    <a href="{{ route('register') }}" class="nav-cta">Get started</a>
@endauth
        </div>
    </nav>

    {{-- Hero --}}
    <section class="hero">
        <div class="hero-orb1"></div>
        <div class="hero-orb2"></div>

        <div class="hero-logo-circle">
            <img src="{{ asset('images/gemana-logo.svg') }}" alt="Gemana">
        </div>

        <div class="hero-eyebrow">Community Management Platform</div>

        <h1>Built for<br><span class="gradient-text">Australian Non-Profits</span></h1>

        <p class="hero-sub">
            Gemana brings together member management, donations, events, volunteering and more —
            in one beautifully simple platform designed for the way your community works.
        </p>

        <div class="hero-btns">
            @auth
                <a href="{{ auth()->user()->hasRole('super-admin') || auth()->user()->hasAnyRole(['admin','team']) ? '/admin' : '/portal/dashboard' }}" class="btn-primary">Go to dashboard</a>
            @else
                <a href="{{ route('register') }}" class="btn-primary">Create your account</a>
                <a href="{{ route('login') }}" class="btn-outline">Sign in</a>
            @endauth
        </div>
    </section>

    {{-- Features --}}
    <section class="section features" id="features">
        <div class="section-label">Why Gemana</div>
        <div class="section-title">Everything your community needs</div>
        <div class="features-grid">
            <div class="feat">
                <div class="feat-icon feat-icon-blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:22px;height:22px"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                </div>
                <h3>Member management</h3>
                <p>Manage memberships, levels, renewals and approvals with full role-based access control built for Australian non-profits.</p>
            </div>
            <div class="feat">
                <div class="feat-icon feat-icon-azure">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:22px;height:22px"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                </div>
                <h3>Donation tracking</h3>
                <p>Accept and record donations, issue tax receipts and track giving history per member across your entire community.</p>
            </div>
            <div class="feat">
                <div class="feat-icon feat-icon-purple">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:22px;height:22px"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 01-.657.643 48.39 48.39 0 01-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 01-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 00-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 01-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 00.657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 01-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 005.427-.63 48.05 48.05 0 00.582-4.717.532.532 0 00-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.4.959.4v0a.656.656 0 00.658-.663 48.422 48.422 0 00-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 01-.61-.58v0z" /></svg>
                </div>
                <h3>Modular by design</h3>
                <p>Enable only the modules you need. Each one slots in independently with no bloat — your platform grows with your organisation.</p>
            </div>
        </div>
    </section>

    {{-- Modules --}}
    <section class="section modules-section" id="modules">
        <div class="section-label">Platform modules</div>
        <div class="section-title">Turn on what you need</div>
        <div class="modules-grid">
            <div class="mod">
                <div class="mod-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:26px;height:26px"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg></div>
                <div class="mod-name">Members</div>
            </div>
            <div class="mod">
                <div class="mod-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:26px;height:26px"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg></div>
                <div class="mod-name">Donations</div>
            </div>
            <div class="mod">
                <div class="mod-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:26px;height:26px"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg></div>
                <div class="mod-name">Events</div>
            </div>
            <div class="mod">
                <div class="mod-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:26px;height:26px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z" /></svg></div>
                <div class="mod-name">Blog</div>
            </div>
            <div class="mod">
                <div class="mod-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:26px;height:26px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" /></svg></div>
                <div class="mod-name">Documents</div>
            </div>
            <div class="mod">
                <div class="mod-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:26px;height:26px"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg></div>
                <div class="mod-name">Newsletter</div>
            </div>
            <div class="mod">
                <div class="mod-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:26px;height:26px"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg></div>
                <div class="mod-name">Volunteering</div>
            </div>
            <div class="mod">
                <div class="mod-icon"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:26px;height:26px"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg></div>
                <div class="mod-name">Notifications</div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="cta-section">
        <h2>Ready to get started?</h2>
        <p>Join Gemana and give your community the platform it deserves.</p>
        @auth
            <a href="/portal/dashboard" class="btn-primary">Go to your portal</a>
        @else
            <a href="{{ route('register') }}" class="btn-primary">Create your account</a>
        @endauth
    </section>

    {{-- Footer --}}
    <footer class="footer">
        <div class="footer-brand">
            <div class="footer-logo">
                <img src="{{ asset('images/gemana-logo.svg') }}" alt="Gemana">
            </div>
            <span class="footer-name">Gemana</span>
        </div>
        <span class="footer-copy">© {{ date('Y') }} Gemana. Built for Australian non-profits.</span>
    </footer>

</body>
</html>