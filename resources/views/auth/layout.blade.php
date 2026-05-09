{{-- resources/views/auth/layout.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) — Gemana</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --dark-blue:  #0b1a75;
            --blue:       #2025b1;
            --purple:     #4d1e99;
            --azure:      #2683d4;
            --vivid:      #21b7e7;
            --white:      #ffffff;
            --off-white:  #f4f6fb;
            --muted:      #8492b4;
            --error:      #e74c3c;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Lato', sans-serif;
            background: var(--off-white);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Decorative gradient orbs */
        body::before {
            content: '';
            position: fixed;
            top: -20%;
            left: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(33,183,231,0.12) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -20%;
            right: -10%;
            width: 45vw;
            height: 45vw;
            background: radial-gradient(circle, rgba(77,30,153,0.10) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .auth-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 460px;
            padding: 2rem 1.25rem;
        }

        /* Logo area */
.auth-brand {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem;
}
.auth-brand img {
    width: 64px; height: 64px;
    flex-shrink: 0;
    filter: drop-shadow(0 4px 16px rgb(255, 255, 255));
}
.auth-brand .brand-text { text-align: left; }
.auth-brand .brand-name {
    font-family: 'Montserrat', sans-serif;
    font-weight: 800; font-size: 1.9rem;
    letter-spacing: 0.12em; text-transform: uppercase;
    background: linear-gradient(135deg, var(--blue) 0%, var(--vivid) 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text; line-height: 1.1;
}
.auth-brand .brand-tagline {
    font-family: 'Lato', sans-serif;
    font-weight: 300; font-size: 0.8rem;
    color: var(--muted); margin-top: 0.25rem; letter-spacing: 0.04em;
}

        /* Card */
        .auth-card {
            background: var(--white);
            border-radius: 20px;
            padding: 2.5rem 2.25rem;
            box-shadow:
                0 1px 2px rgba(11,26,117,0.04),
                0 4px 16px rgba(11,26,117,0.08),
                0 20px 40px rgba(11,26,117,0.06);
            border: 1px solid rgba(33,183,231,0.12);
        }

        .auth-card h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--dark-blue);
            margin-bottom: 1.75rem;
            letter-spacing: 0.02em;
        }

        /* Alerts */
        .alert {
            padding: 0.85rem 1rem;
            border-radius: 10px;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
            line-height: 1.5;
        }
        .alert-success {
            background: #e8fdf5;
            border: 1px solid #a3e9cc;
            color: #1a7a4e;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
        }
        .alert-warning {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            color: #92400e;
        }
        .alert ul { list-style: disc; padding-left: 1.2rem; }
        .alert ul li + li { margin-top: 0.3rem; }

        /* Form elements */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--dark-blue);
            margin-bottom: 0.45rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #dde3f0;
            border-radius: 10px;
            font-family: 'Lato', sans-serif;
            font-size: 0.9375rem;
            color: var(--dark-blue);
            background: var(--off-white);
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--vivid);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(33,183,231,0.15);
        }

        .form-input.error {
            border-color: var(--error);
        }

        .form-error {
            font-size: 0.8rem;
            color: var(--error);
            margin-top: 0.35rem;
        }

        /* Checkbox row */
        .form-check {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
        }

        .form-check input[type="checkbox"] {
            margin-top: 0.15rem;
            width: 16px;
            height: 16px;
            accent-color: var(--blue);
            flex-shrink: 0;
        }

        .form-check label {
            font-size: 0.875rem;
            color: #4a5578;
            line-height: 1.5;
        }

        /* Label-link row */
        .label-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.45rem;
        }

        .label-row .form-label { margin-bottom: 0; }

        /* Primary button */
        .btn-primary {
            width: 100%;
            padding: 0.85rem 1.5rem;
            background: linear-gradient(135deg, var(--blue) 0%, var(--vivid) 100%);
            color: var(--white);
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.9375rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 18px rgba(32,37,177,0.25);
            margin-top: 0.5rem;
        }

        .btn-primary:hover {
            opacity: 0.92;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(32,37,177,0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
            opacity: 1;
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Links */
        a.auth-link {
            color: var(--azure);
            font-weight: 700;
            text-decoration: none;
            font-size: 0.8125rem;
            transition: color 0.15s;
        }
        a.auth-link:hover { color: var(--blue); text-decoration: underline; }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: var(--muted);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.5rem 0;
        }
        .divider span { font-size: 0.8rem; color: var(--muted); flex-shrink: 0; }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e4e9f5;
        }

        /* Select */
        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' fill='none'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%232683d4' stroke-width='1.8' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            padding-right: 2.5rem;
        }

        /* Hint text */
        .form-hint {
            font-size: 0.78rem;
            color: var(--muted);
            margin-top: 0.3rem;
        }

        /* Subtle card for info */
        .info-card {
            background: linear-gradient(135deg, rgba(33,183,231,0.07) 0%, rgba(32,37,177,0.05) 100%);
            border: 1px solid rgba(33,183,231,0.2);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            color: #3a4a7a;
            line-height: 1.6;
        }

        /* OTP input */
        input[inputmode="numeric"].form-input {
            text-align: center;
            letter-spacing: 0.25em;
            font-size: 1.25rem;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }

        /* Monospace (recovery code) */
        input.mono.form-input {
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }

        /* Toggle link */
        .toggle-link {
            background: none;
            border: none;
            color: var(--azure);
            font-family: 'Lato', sans-serif;
            font-weight: 700;
            font-size: 0.8125rem;
            cursor: pointer;
            padding: 0;
            transition: color 0.15s;
        }
        .toggle-link:hover { color: var(--blue); text-decoration: underline; }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-brand">
    <img src="{{ asset('images/gemana-logo.svg') }}" alt="Gemana">
    <div class="brand-text">
        <div class="brand-name">Gemana</div>
        <div class="brand-tagline">Community Management Platform</div>
    </div>
</div>

    <div class="auth-card">
        @yield('content')
    </div>

    @hasSection('footer')
        <div class="auth-footer">
            @yield('footer')
        </div>
    @endif
</div>
</body>
</html>
