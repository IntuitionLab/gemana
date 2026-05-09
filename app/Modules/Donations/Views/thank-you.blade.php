{{-- app/Modules/Donations/Views/thank-you.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank You — {{ config('gemana.org_name', 'Gemana') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --dark-blue: #0b1a75;
            --blue:      #2025b1;
            --purple:    #4d1e99;
            --vivid:     #21b7e7;
            --white:     #ffffff;
            --off-white: #f4f6fb;
            --muted:     #8492b4;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Lato', sans-serif;
            background: var(--off-white);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: fixed;
            top: -20%;
            right: -10%;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(33,183,231,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .thank-you-card {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 8px 40px rgba(11,26,117,0.10);
            padding: 3rem 2.5rem;
            text-align: center;
            max-width: 480px;
            width: 100%;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.5s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .check-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--dark-blue), var(--vivid));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .check-circle svg { width: 36px; height: 36px; }

        h1 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 1.75rem;
            color: var(--dark-blue);
            margin-bottom: 0.75rem;
        }
        p {
            color: var(--muted);
            font-weight: 300;
            line-height: 1.65;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .receipt-note {
            margin: 1.5rem 0;
            padding: 1rem;
            background: rgba(33,183,231,0.06);
            border: 1px solid rgba(33,183,231,0.2);
            border-radius: 8px;
            font-size: 0.85rem;
            color: var(--dark-blue);
        }

        .actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            margin-top: 1.75rem;
            flex-wrap: wrap;
        }
        .btn-primary {
            padding: 0.7rem 1.5rem;
            background: linear-gradient(135deg, var(--dark-blue), var(--blue));
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            text-decoration: none;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn-primary:hover { opacity: 0.85; }
        .btn-secondary {
            padding: 0.7rem 1.5rem;
            background: transparent;
            color: var(--dark-blue);
            border: 2px solid #dde3f0;
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            text-decoration: none;
            transition: border-color 0.2s;
        }
        .btn-secondary:hover { border-color: var(--vivid); }
    </style>
</head>
<body>
    <div class="thank-you-card">

        <div class="check-circle">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>

        <h1>Thank You!</h1>
        <p>Your donation has been received and is deeply appreciated.</p>
        <p>Every contribution helps us continue our important work in the community.</p>

        <div class="receipt-note">
            📧 A tax receipt will be emailed to you shortly. You can also download it from your member portal at any time.
        </div>

        <div class="actions">
            @auth
                <a href="{{ route('portal.donations.index') }}" class="btn-primary">View My Donations</a>
            @endauth
            <a href="{{ route('donations.form') }}" class="btn-secondary">Donate Again</a>
        </div>

    </div>
</body>
</html>