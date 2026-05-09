{{-- app/Modules/Donations/Views/form.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Make a Donation — {{ config('gemana.org_name', 'Gemana') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">

    {{-- Stripe Elements --}}
    <script src="https://js.stripe.com/v3/"></script>

    {{-- PayPal SDK --}}
    {{-- Note: Replace YOUR_PAYPAL_CLIENT_ID with env var output below --}}
    <script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=AUD" defer></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --dark-blue: #0b1a75;
            --blue:      #2025b1;
            --purple:    #4d1e99;
            --azure:     #2683d4;
            --vivid:     #21b7e7;
            --white:     #ffffff;
            --off-white: #f4f6fb;
            --muted:     #8492b4;
            --border:    #dde3f0;
            --error:     #e74c3c;
            --success:   #27ae60;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Lato', sans-serif;
            background: var(--off-white);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Decorative background orbs */
        body::before {
            content: '';
            position: fixed;
            top: -15%;
            right: -10%;
            width: 45vw;
            height: 45vw;
            background: radial-gradient(circle, rgba(33,183,231,0.10) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -20%;
            left: -10%;
            width: 40vw;
            height: 40vw;
            background: radial-gradient(circle, rgba(77,30,153,0.08) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        /* ── Header ── */
        .donate-header {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--blue) 50%, var(--purple) 100%);
            padding: 1.5rem 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
            z-index: 10;
        }
        .donate-header a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }
        .donate-header img {
            width: 40px;
            height: 40px;
            filter: drop-shadow(0 2px 8px rgba(255,255,255,0.4));
        }
        .donate-header .brand-name {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--white);
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        /* ── Page layout ── */
        .donate-page {
            position: relative;
            z-index: 1;
            max-width: 680px;
            margin: 2.5rem auto;
            padding: 0 1.25rem 4rem;
        }

        .donate-intro {
            text-align: center;
            margin-bottom: 2rem;
        }
        .donate-intro h1 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 2rem;
            color: var(--dark-blue);
            margin-bottom: 0.5rem;
        }
        .donate-intro p {
            color: var(--muted);
            font-size: 1rem;
            font-weight: 300;
            line-height: 1.6;
        }

        /* ── Card ── */
        .donate-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 32px rgba(11,26,117,0.08);
            overflow: hidden;
        }

        /* ── Section inside card ── */
        .donate-section {
            padding: 1.75rem 2rem;
            border-bottom: 1px solid var(--border);
        }
        .donate-section:last-child { border-bottom: none; }

        .section-label {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.7rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 1rem;
        }

        /* ── Donation Type Tabs ── */
        .type-tabs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
        }
        .type-tab {
            padding: 0.65rem 0.5rem;
            border-radius: 8px;
            border: 2px solid var(--border);
            background: transparent;
            font-family: 'Lato', sans-serif;
            font-weight: 700;
            font-size: 0.8rem;
            color: var(--muted);
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }
        .type-tab.active {
            border-color: var(--vivid);
            background: rgba(33,183,231,0.06);
            color: var(--blue);
        }

        /* ── Amount Pills ── */
        .amount-pills {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }
        .amount-pill {
            padding: 0.65rem 0.25rem;
            border-radius: 8px;
            border: 2px solid var(--border);
            background: transparent;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--dark-blue);
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }
        .amount-pill.active {
            border-color: var(--dark-blue);
            background: var(--dark-blue);
            color: var(--white);
        }
        .amount-pill:hover:not(.active) {
            border-color: var(--azure);
            color: var(--azure);
        }

        /* Custom amount input */
        .custom-amount-wrap {
            position: relative;
        }
        .custom-amount-wrap .currency-symbol {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: var(--dark-blue);
            font-size: 1rem;
            pointer-events: none;
        }
        .custom-amount-wrap input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--dark-blue);
            outline: none;
            transition: border-color 0.2s;
        }
        .custom-amount-wrap input:focus {
            border-color: var(--vivid);
        }

        /* ── Frequency Selector ── */
        .frequency-tabs {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
        }
        .frequency-tab {
            padding: 0.6rem 0.25rem;
            border-radius: 8px;
            border: 2px solid var(--border);
            background: transparent;
            font-family: 'Lato', sans-serif;
            font-weight: 700;
            font-size: 0.78rem;
            color: var(--muted);
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }
        .frequency-tab.active {
            border-color: var(--purple);
            background: rgba(77,30,153,0.06);
            color: var(--purple);
        }

        /* ── Form fields ── */
        .field-group {
            display: grid;
            gap: 0.75rem;
        }
        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }
        @media (max-width: 520px) { .field-row { grid-template-columns: 1fr; } }

        .field label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--dark-blue);
            margin-bottom: 0.3rem;
            letter-spacing: 0.02em;
        }
        .field input, .field select, .field textarea {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-family: 'Lato', sans-serif;
            font-size: 0.9rem;
            color: var(--dark-blue);
            outline: none;
            transition: border-color 0.2s;
            background: var(--white);
        }
        .field input:focus, .field select:focus, .field textarea:focus {
            border-color: var(--vivid);
        }
        .field textarea { resize: vertical; min-height: 80px; }

        /* ── Toggle checkbox ── */
        .toggle-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }
        .toggle-row input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--dark-blue);
            cursor: pointer;
        }
        .toggle-row span {
            font-size: 0.88rem;
            color: var(--dark-blue);
            font-weight: 400;
        }

        /* ── Stripe card element ── */
        #stripe-card-element {
            padding: 0.75rem 0.85rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            transition: border-color 0.2s;
            background: var(--white);
        }
        #stripe-card-element.StripeElement--focus {
            border-color: var(--vivid);
        }
        #stripe-card-element.StripeElement--invalid {
            border-color: var(--error);
        }

        /* ── PayPal container ── */
        #paypal-button-container {
            min-height: 48px;
        }

        /* ── Submit button ── */
        .btn-donate {
            width: 100%;
            padding: 1rem;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, var(--dark-blue), var(--blue));
            color: var(--white);
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 1rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
            margin-top: 0.25rem;
        }
        .btn-donate:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-donate:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        /* ── Gateway selector ── */
        .gateway-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
        }
        .gateway-tab {
            padding: 0.75rem;
            border-radius: 8px;
            border: 2px solid var(--border);
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-family: 'Lato', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--muted);
            transition: all 0.2s;
        }
        .gateway-tab.active {
            border-color: var(--azure);
            background: rgba(38,131,212,0.06);
            color: var(--azure);
        }

        /* ── Error / info messages ── */
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.88rem;
            margin-bottom: 1rem;
        }
        .alert-error {
            background: rgba(231,76,60,0.08);
            border: 1px solid rgba(231,76,60,0.25);
            color: var(--error);
        }
        .alert-info {
            background: rgba(33,183,231,0.08);
            border: 1px solid rgba(33,183,231,0.25);
            color: var(--azure);
        }

        /* Security badge */
        .security-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            font-size: 0.75rem;
            color: var(--muted);
            margin-top: 1rem;
        }

        /* Hidden utility */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body>

    {{-- Header --}}
    <header class="donate-header">
        <a href="/">
            <img src="/images/gemana-logo.svg" alt="Gemana">
            <span class="brand-name">Gemana</span>
        </a>
    </header>

    <div class="donate-page" x-data="donationForm()" x-cloak>

        <div class="donate-intro">
            <h1>Make a Donation</h1>
            <p>Your generosity makes a real difference. Every contribution supports our community.</p>
        </div>

        {{-- Error flash --}}
        @if ($errors->any())
            <div class="alert alert-error">
                {{ $errors->first() }}
            </div>
        @endif
        @if (session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <div class="donate-card">

            {{-- ① Donation Type --}}
            <div class="donate-section">
                <div class="section-label">Donation Type</div>
                <div class="type-tabs">
                    <button type="button" class="type-tab" :class="{ active: type === 'one_off' }" @click="type = 'one_off'">One-Off</button>
                    <button type="button" class="type-tab" :class="{ active: type === 'recurring' }" @click="type = 'recurring'">Recurring</button>
                    <button type="button" class="type-tab" :class="{ active: type === 'in_memory' }" @click="type = 'in_memory'">In Memory</button>
                </div>
            </div>

            {{-- ② Amount --}}
            <div class="donate-section">
                <div class="section-label">
                    Amount
                    <span x-show="type === 'recurring'" x-text="'— ' + frequencyLabel()"></span>
                </div>

                <div class="amount-pills">
                    <template x-for="preset in presets" :key="preset">
                        <button
                            type="button"
                            class="amount-pill"
                            :class="{ active: amount === preset && !customActive }"
                            @click="selectPreset(preset)"
                            x-text="'$' + preset"
                        ></button>
                    </template>
                </div>

                <div class="custom-amount-wrap">
                    <span class="currency-symbol">$</span>
                    <input
                        type="number"
                        placeholder="Other amount"
                        min="1"
                        step="1"
                        x-model.number="customAmount"
                        @focus="customActive = true; amount = customAmount || 0"
                        @input="amount = customAmount"
                    >
                </div>

                {{-- Frequency (recurring only) --}}
                <div x-show="type === 'recurring'" class="mt-3" style="margin-top: 0.75rem;">
                    <div class="section-label" style="margin-bottom: 0.5rem;">Frequency</div>
                    <div class="frequency-tabs">
                        <template x-for="freq in frequencies" :key="freq.value">
                            <button
                                type="button"
                                class="frequency-tab"
                                :class="{ active: frequency === freq.value }"
                                @click="frequency = freq.value"
                                x-text="freq.label"
                            ></button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ③ Tribute (In Memory only) --}}
            <div class="donate-section" x-show="type === 'in_memory'">
                <div class="section-label">Tribute Details</div>
                <div class="field-group">
                    <div class="field-row">
                        <div class="field">
                            <label>Tribute Type</label>
                            <select x-model="tributeType">
                                <option value="in_memory">In Memory of</option>
                                <option value="in_honour">In Honour of</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Name</label>
                            <input type="text" x-model="tributeName" placeholder="e.g. John Smith">
                        </div>
                    </div>
                    <div class="section-label" style="margin-top: 0.5rem; margin-bottom: 0.25rem;">Notify Someone (optional)</div>
                    <div class="field-row">
                        <div class="field">
                            <label>Their Name</label>
                            <input type="text" x-model="tributeNotifyName" placeholder="e.g. Jane Smith">
                        </div>
                        <div class="field">
                            <label>Their Email</label>
                            <input type="email" x-model="tributeNotifyEmail" placeholder="jane@example.com">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ④ Donor Details --}}
            <div class="donate-section">
                <div class="section-label">Your Details</div>

                <div class="field-group">
                    <label class="toggle-row">
                        <input type="checkbox" x-model="isAnonymous">
                        <span>Donate anonymously (no tax receipt will be issued)</span>
                    </label>

                    <div x-show="!isAnonymous">
                        <div class="field-row" style="margin-bottom: 0.75rem;">
                            <div class="field">
                                <label>Full Name</label>
                                <input type="text" x-model="donorName" placeholder="Your full name">
                            </div>
                            <div class="field">
                                <label>Email Address</label>
                                <input type="email" x-model="donorEmail" placeholder="your@email.com">
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label>Message <span style="font-weight:300; color: var(--muted)">(optional)</span></label>
                        <textarea x-model="message" placeholder="Leave a message with your donation…"></textarea>
                    </div>
                </div>
            </div>

            {{-- ⑤ Payment --}}
            <div class="donate-section">
                <div class="section-label">Payment Method</div>

                <div class="gateway-tabs" style="margin-bottom: 1.25rem;">
                    <button type="button" class="gateway-tab" :class="{ active: gateway === 'stripe' }" @click="gateway = 'stripe'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="4" fill="#635BFF"/><path d="M11.1 9.4c0-.7.6-1 1.5-1 1.4 0 2.8.4 3.8 1l.7-4.2C16 4.7 14.3 4 12.4 4 8.9 4 6.5 5.8 6.5 9c0 4.9 6.7 4.1 6.7 6.2 0 .8-.7 1.1-1.7 1.1-1.5 0-3.2-.6-4.4-1.5l-.7 4.3c1.1.7 3 1.2 4.9 1.2 3.6 0 6.2-1.8 6.2-5.1 0-5.3-6.6-4.3-6.4-5.8z" fill="white"/></svg>
                        Card (Stripe)
                    </button>
                    <button type="button" class="gateway-tab" :class="{ active: gateway === 'paypal' }" @click="gateway = 'paypal'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.26-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l-.24 1.516a.56.56 0 0 0 .554.647h3.882c.46 0 .85-.334.922-.788.06-.26.76-4.852.816-5.09a.932.932 0 0 1 .923-.788h.58c3.76 0 6.705-1.528 7.565-5.946.36-1.847.174-3.388-.777-4.477z"/></svg>
                        PayPal
                    </button>
                </div>

                {{-- Stripe Elements --}}
                <div x-show="gateway === 'stripe'">
                    <div id="stripe-card-element"></div>
                    <div id="stripe-errors" style="color: var(--error); font-size: 0.82rem; margin-top: 0.4rem;"></div>
                </div>

                {{-- PayPal Button --}}
                <div x-show="gateway === 'paypal'">
                    <div id="paypal-button-container"></div>
                </div>

                {{-- Stripe submit --}}
                <div x-show="gateway === 'stripe'" style="margin-top: 1rem;">
                    <button
                        type="button"
                        class="btn-donate"
                        @click="submitStripe()"
                        :disabled="processing"
                    >
                        <span x-show="!processing">
                            Donate <span x-text="amount > 0 ? '$' + amount.toFixed(2) : ''"></span>
                        </span>
                        <span x-show="processing">Processing…</span>
                    </button>
                </div>
            </div>

        </div>

        <div class="security-note">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Secure payment — your details are protected with 256-bit SSL encryption.
        </div>

    </div>

    <script>
        function donationForm() {
            return {
                type:        'one_off',
                amount:      50,
                customAmount: null,
                customActive: false,
                frequency:   'monthly',
                gateway:     'stripe',
                isAnonymous: false,
                donorName:   '{{ auth()->user()?->name ?? "" }}',
                donorEmail:  '{{ auth()->user()?->email ?? "" }}',
                message:     '',
                tributeType:        'in_memory',
                tributeName:        '',
                tributeNotifyName:  '',
                tributeNotifyEmail: '',
                processing:  false,

                presets:     [25, 50, 100, 250],
                frequencies: [
                    { value: 'weekly',    label: 'Weekly' },
                    { value: 'monthly',   label: 'Monthly' },
                    { value: 'quarterly', label: 'Quarterly' },
                    { value: 'annual',    label: 'Annually' },
                ],

                stripeInstance: null,
                cardElement:    null,

                init() {
                    this.$nextTick(() => {
                        this.initStripe();
                        this.initPayPal();
                    });
                },

                selectPreset(val) {
                    this.amount      = val;
                    this.customActive = false;
                    this.customAmount = null;
                },

                frequencyLabel() {
                    return this.frequencies.find(f => f.value === this.frequency)?.label ?? '';
                },

                // ── Stripe ──────────────────────────────────────────────────

                initStripe() {
                    this.stripeInstance = Stripe('{{ $stripeKey }}');
                    const elements = this.stripeInstance.elements();
                    this.cardElement = elements.create('card', {
                        style: {
                            base: {
                                fontFamily: 'Lato, sans-serif',
                                fontSize: '15px',
                                color: '#0b1a75',
                                '::placeholder': { color: '#8492b4' },
                            },
                            invalid: { color: '#e74c3c' },
                        }
                    });
                    this.cardElement.mount('#stripe-card-element');
                    this.cardElement.on('change', (e) => {
                        document.getElementById('stripe-errors').textContent = e.error ? e.error.message : '';
                    });
                },

                async submitStripe() {
                    if (this.amount <= 0) {
                        alert('Please enter a donation amount.'); return;
                    }
                    if (!this.isAnonymous && (!this.donorName || !this.donorEmail)) {
                        alert('Please enter your name and email, or choose to donate anonymously.'); return;
                    }

                    this.processing = true;

                    // 1. Hit our backend to create a PaymentIntent and get client_secret
                    const response = await fetch('{{ route("donations.initiate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.buildPayload()),
                    });

                    const data = await response.json();

                    if (!data.client_secret) {
                        document.getElementById('stripe-errors').textContent = data.message ?? 'Payment could not be initiated.';
                        this.processing = false;
                        return;
                    }

                    // 2. Confirm payment with Stripe Elements
                    const { error } = await this.stripeInstance.confirmCardPayment(data.client_secret, {
                        payment_method: {
                            card: this.cardElement,
                            billing_details: {
                                name:  this.donorName  || 'Anonymous',
                                email: this.donorEmail || undefined,
                            },
                        },
                    });

                    if (error) {
                        document.getElementById('stripe-errors').textContent = error.message;
                        this.processing = false;
                    } else {
                        window.location.href = '{{ route("donations.thank-you") }}';
                    }
                },

                // ── PayPal ──────────────────────────────────────────────────

                initPayPal() {
                    if (typeof paypal === 'undefined') return;

                    paypal.Buttons({
                        createOrder: async () => {
                            const response = await fetch('{{ route("donations.initiate") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ ...this.buildPayload(), gateway: 'paypal' }),
                            });
                            const data = await response.json();
                            // The PayPal approval URL is returned — PayPal SDK needs the order ID
                            // Extract from the URL: ?token=ORDER_ID
                            const url  = new URL(data.approval_url);
                            return url.searchParams.get('token');
                        },
                        onApprove: async (data) => {
                            window.location.href = '{{ route("donations.paypal.return") }}?token=' + data.orderID;
                        },
                        onCancel: () => {
                            window.location.href = '{{ route("donations.paypal.cancel") }}';
                        },
                        onError: (err) => {
                            console.error('PayPal error', err);
                        },
                    }).render('#paypal-button-container');
                },

                // ── Shared payload builder ──────────────────────────────────

                buildPayload() {
                    return {
                        amount:                this.amount,
                        currency:              'AUD',
                        type:                  this.type,
                        frequency:             this.type === 'recurring' ? this.frequency : null,
                        gateway:               this.gateway,
                        is_anonymous:          this.isAnonymous,
                        donor_name:            this.isAnonymous ? null : this.donorName,
                        donor_email:           this.isAnonymous ? null : this.donorEmail,
                        message:               this.message,
                        tribute_name:          this.type === 'in_memory' ? this.tributeName : null,
                        tribute_type:          this.type === 'in_memory' ? this.tributeType : null,
                        tribute_notify_name:   this.type === 'in_memory' ? this.tributeNotifyName  : null,
                        tribute_notify_email:  this.type === 'in_memory' ? this.tributeNotifyEmail : null,
                    };
                },
            };
        }
    </script>

</body>
</html>