{{-- app/Modules/Donations/Views/portal/index.blade.php --}}
@extends('portal.layout')

@section('title', 'My Donations')

@section('content')

<div style="max-width: 900px;">

    {{-- ── Page Header ── --}}
    <div style="margin-bottom: 1.75rem;">
        <h1 style="font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 1.5rem; color: #0b1a75; margin-bottom: 0.25rem;">
            My Donations
        </h1>
        <p style="color: #8492b4; font-size: 0.9rem;">
            Your giving history and active recurring donations.
        </p>
    </div>

    {{-- ── Flash Messages ── --}}
    @if(session('success'))
        <div style="background: rgba(39,174,96,0.08); border: 1px solid rgba(39,174,96,0.25); color: #27ae60; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.875rem; margin-bottom: 1.25rem;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: rgba(231,76,60,0.08); border: 1px solid rgba(231,76,60,0.25); color: #e74c3c; padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.875rem; margin-bottom: 1.25rem;">
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Stats Row ── --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.75rem;">

        {{-- Total Given --}}
        <div style="background: white; border-radius: 14px; padding: 1.25rem 1.5rem; border: 1.5px solid #dde3f0; box-shadow: 0 2px 12px rgba(11,26,117,0.05);">
            <p style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; color: #8492b4; margin-bottom: 0.4rem;">Total Given</p>
            <p style="font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 1.5rem; color: #0b1a75;">
                ${{ number_format($totalGiven, 2) }}
            </p>
            <p style="font-size: 0.75rem; color: #8492b4; margin-top: 0.2rem;">All time</p>
        </div>

        {{-- This Financial Year --}}
        <div style="background: linear-gradient(135deg, #0b1a75, #2025b1); border-radius: 14px; padding: 1.25rem 1.5rem; box-shadow: 0 4px 16px rgba(11,26,117,0.2);">
            <p style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.6); margin-bottom: 0.4rem;">This Financial Year</p>
            <p style="font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 1.5rem; color: white;">
                ${{ number_format($totalThisFy, 2) }}
            </p>
            <p style="font-size: 0.75rem; color: #21b7e7; margin-top: 0.2rem;">
                {{ now()->month >= 7 ? now()->year : now()->year - 1 }}–{{ now()->month >= 7 ? substr(now()->year + 1, -2) : substr(now()->year, -2) }} FY
            </p>
        </div>

        {{-- Total Donations --}}
        <div style="background: white; border-radius: 14px; padding: 1.25rem 1.5rem; border: 1.5px solid #dde3f0; box-shadow: 0 2px 12px rgba(11,26,117,0.05);">
            <p style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.1em; color: #8492b4; margin-bottom: 0.4rem;">Total Donations</p>
            <p style="font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 1.5rem; color: #0b1a75;">
                {{ $totalCount }}
            </p>
            <p style="font-size: 0.75rem; color: #8492b4; margin-top: 0.2rem;">Contributions</p>
        </div>

    </div>

    {{-- ── Active Recurring Plans ── --}}
    @if($plans->isNotEmpty())
    <div style="background: white; border-radius: 14px; border: 1.5px solid #dde3f0; box-shadow: 0 2px 12px rgba(11,26,117,0.05); margin-bottom: 1.75rem; overflow: hidden;">

        <div style="padding: 1.1rem 1.5rem; border-bottom: 1px solid #dde3f0; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h2 style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 0.95rem; color: #0b1a75;">
                    Active Recurring Donations
                </h2>
                <p style="font-size: 0.78rem; color: #8492b4; margin-top: 0.1rem;">Your ongoing giving commitments</p>
            </div>
            <span style="background: rgba(39,174,96,0.10); color: #27ae60; font-size: 0.7rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.08em;">
                {{ $plans->count() }} Active
            </span>
        </div>

        @foreach($plans as $plan)
        <div style="padding: 1.1rem 1.5rem; border-bottom: 1px solid #dde3f0; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                {{-- Gateway icon --}}
                <div style="width: 38px; height: 38px; border-radius: 10px; background: #f4f6fb; border: 1.5px solid #dde3f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    @if($plan->gateway === 'stripe')
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="4" fill="#635BFF"/><path d="M11.1 9.4c0-.7.6-1 1.5-1 1.4 0 2.8.4 3.8 1l.7-4.2C16 4.7 14.3 4 12.4 4 8.9 4 6.5 5.8 6.5 9c0 4.9 6.7 4.1 6.7 6.2 0 .8-.7 1.1-1.7 1.1-1.5 0-3.2-.6-4.4-1.5l-.7 4.3c1.1.7 3 1.2 4.9 1.2 3.6 0 6.2-1.8 6.2-5.1 0-5.3-6.6-4.3-6.4-5.8z" fill="white"/></svg>
                    @else
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="#003087"><path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106z"/></svg>
                    @endif
                </div>

                <div>
                    <p style="font-weight: 700; color: #0b1a75; font-size: 0.9rem;">
                        ${{ number_format($plan->amount, 2) }} {{ $plan->currency }}
                        <span style="color: #8492b4; font-weight: 400;">/ {{ $plan->frequencyLabel() }}</span>
                    </p>
                    <p style="font-size: 0.78rem; color: #8492b4; margin-top: 0.15rem;">
                        Started {{ $plan->started_at?->format('d M Y') ?? 'N/A' }}
                        @if($plan->next_billing_date)
                            &nbsp;·&nbsp; Next: {{ $plan->next_billing_date->format('d M Y') }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- Cancel button --}}
            <form method="POST" action="{{ route('portal.donations.plan.cancel', $plan) }}"
                onsubmit="return confirm('Are you sure you want to cancel this recurring donation? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    style="padding: 0.45rem 1rem; border-radius: 7px; border: 1.5px solid #dde3f0; background: white; color: #8492b4; font-size: 0.78rem; font-weight: 700; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.borderColor='#e74c3c'; this.style.color='#e74c3c';"
                    onmouseout="this.style.borderColor='#dde3f0'; this.style.color='#8492b4';"
                >
                    Cancel
                </button>
            </form>
        </div>
        @endforeach

    </div>
    @endif

    {{-- ── Donation History ── --}}
    <div style="background: white; border-radius: 14px; border: 1.5px solid #dde3f0; box-shadow: 0 2px 12px rgba(11,26,117,0.05); overflow: hidden;">

        <div style="padding: 1.1rem 1.5rem; border-bottom: 1px solid #dde3f0; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h2 style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 0.95rem; color: #0b1a75;">
                    Donation History
                </h2>
                <p style="font-size: 0.78rem; color: #8492b4; margin-top: 0.1rem;">All your completed donations</p>
            </div>
            <a href="{{ route('donations.form') }}"
                style="padding: 0.5rem 1.1rem; border-radius: 8px; background: linear-gradient(135deg, #0b1a75, #2025b1); color: white; font-size: 0.78rem; font-weight: 700; text-decoration: none; letter-spacing: 0.05em; text-transform: uppercase;">
                + Donate
            </a>
        </div>

        @forelse($donations as $donation)
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #f4f6fb; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">

            {{-- Type icon --}}
            <div style="width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
                {{ $donation->type === 'recurring' ? 'background: rgba(38,131,212,0.10);' : ($donation->type === 'in_memory' ? 'background: rgba(77,30,153,0.10);' : 'background: rgba(11,26,117,0.07);') }}">
                @if($donation->type === 'recurring')
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2683d4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                @elseif($donation->type === 'in_memory')
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4d1e99" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                @else
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0b1a75" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                @endif
            </div>

            {{-- Details --}}
            <div style="flex: 1; min-width: 0;">
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <span style="font-weight: 700; color: #0b1a75; font-size: 0.9rem;">
                        ${{ number_format($donation->amount, 2) }} {{ $donation->currency }}
                    </span>
                    {{-- Type badge --}}
                    <span style="font-size: 0.68rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.08em;
                        {{ $donation->type === 'recurring' ? 'background: rgba(38,131,212,0.10); color: #2683d4;' : ($donation->type === 'in_memory' ? 'background: rgba(77,30,153,0.10); color: #4d1e99;' : 'background: rgba(11,26,117,0.07); color: #0b1a75;') }}">
                        {{ $donation->type === 'one_off' ? 'One-Off' : ($donation->type === 'recurring' ? 'Recurring' : 'In Memory') }}
                    </span>
                </div>
                <p style="font-size: 0.78rem; color: #8492b4; margin-top: 0.15rem;">
                    {{ $donation->created_at->format('d M Y') }}
                    @if($donation->isInMemory() && $donation->tribute_name)
                        &nbsp;·&nbsp; In {{ $donation->tribute_type === 'in_honour' ? 'honour' : 'memory' }} of {{ $donation->tribute_name }}
                    @endif
                </p>
            </div>

            {{-- Receipt download --}}
            <div style="flex-shrink: 0;">
                @if($donation->hasReceipt() && $donation->receipt?->hasPdf())
                    <a href="{{ route('portal.donations.receipt.download', $donation) }}"
                        style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.9rem; border-radius: 7px; border: 1.5px solid #dde3f0; background: white; color: #0b1a75; font-size: 0.78rem; font-weight: 700; text-decoration: none; transition: all 0.2s;"
                        onmouseover="this.style.borderColor='#21b7e7'; this.style.color='#21b7e7';"
                        onmouseout="this.style.borderColor='#dde3f0'; this.style.color='#0b1a75';"
                    >
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Receipt
                    </a>
                @elseif($donation->hasReceipt())
                    <span style="font-size: 0.75rem; color: #8492b4; font-style: italic;">Generating…</span>
                @else
                    <span style="font-size: 0.75rem; color: #b0bcd4;">No receipt</span>
                @endif
            </div>

        </div>
        @empty
        <div style="padding: 3rem 1.5rem; text-align: center;">
            <div style="width: 52px; height: 52px; border-radius: 50%; background: #f4f6fb; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#8492b4" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <p style="font-family: 'Montserrat', sans-serif; font-weight: 700; color: #0b1a75; margin-bottom: 0.4rem;">No donations yet</p>
            <p style="font-size: 0.85rem; color: #8492b4; margin-bottom: 1.25rem;">Your donation history will appear here.</p>
            <a href="{{ route('donations.form') }}"
                style="display: inline-block; padding: 0.6rem 1.5rem; border-radius: 8px; background: linear-gradient(135deg, #0b1a75, #2025b1); color: white; font-size: 0.82rem; font-weight: 700; text-decoration: none; letter-spacing: 0.05em; text-transform: uppercase;">
                Make Your First Donation
            </a>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if($donations->hasPages())
        <div style="padding: 1rem 1.5rem; border-top: 1px solid #dde3f0;">
            {{ $donations->links() }}
        </div>
        @endif

    </div>

</div>

@endsection
