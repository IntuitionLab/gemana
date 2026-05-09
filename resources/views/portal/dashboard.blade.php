@extends('portal.layout')
@section('title', 'Dashboard')
@section('page-title', 'Member Portal')

@section('content')
<style>
    .welcome-bar {
        background: linear-gradient(135deg, #0b1a75 0%, #2025b1 60%, #2683d4 100%);
        border-radius: 14px; padding: 20px 24px;
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 16px; gap: 12px;
    }
    .welcome-bar h2 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 800; font-size: 17px; color: #fff;
    }
    .welcome-bar p { font-size: 13px; color: rgba(255,255,255,0.85); margin-top: 4px; }
    .wb-badge {
        background: rgba(33,183,231,0.25);
        border: 1px solid rgba(33,183,231,0.5);
        border-radius: 20px; padding: 6px 14px;
        font-size: 12px; font-weight: 700; color: #7de8f8;
        font-family: 'Montserrat', sans-serif;
        letter-spacing: 0.06em; text-transform: uppercase; white-space: nowrap;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px; margin-bottom: 16px;
    }
    @media (min-width: 768px) {
        .stats-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .stat-card {
        background: #fff; border-radius: 12px;
        padding: 14px 16px;
        border: 0.5px solid #e4e9f5;
    }
    .stat-label {
        font-size: 11px; font-weight: 700;
        letter-spacing: 0.08em; text-transform: uppercase;
        color: #8492b4; margin-bottom: 6px;
    }
    .stat-val {
        font-family: 'Montserrat', sans-serif;
        font-weight: 800; font-size: 22px; color: #0b1a75;
    }
    .stat-sub { font-size: 11px; color: #8492b4; margin-top: 3px; }
    .stat-dot {
        width: 8px; height: 8px; border-radius: 50%;
        display: inline-block; margin-right: 4px;
    }

    .cards-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 16px;
    }
    @media (min-width: 768px) {
        .cards-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    .dash-card {
        background: #fff; border-radius: 12px;
        border: 0.5px solid #e4e9f5; padding: 16px; min-width: 0;
    }
    .card-hdr {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 14px;
    }
    .card-title {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700; font-size: 13px; color: #0b1a75;
    }
    .card-link { font-size: 12px; color: #2683d4; font-weight: 700; text-decoration: none; }

    .event-item {
        display: flex; gap: 12px; padding: 8px 0;
        border-bottom: 0.5px solid #f0f2f8;
    }
    .event-item:last-child { border-bottom: none; }
    .ev-date {
        width: 36px; height: 36px; background: #f0f4ff;
        border-radius: 8px; display: flex; flex-direction: column;
        align-items: center; justify-content: center; flex-shrink: 0;
    }
    .ev-day {
        font-family: 'Montserrat', sans-serif; font-weight: 800;
        font-size: 14px; color: #0b1a75; line-height: 1;
    }
    .ev-mon {
        font-size: 9px; font-weight: 700; color: #8492b4;
        text-transform: uppercase; letter-spacing: 0.06em;
    }
    .ev-info p { font-size: 13px; font-weight: 700; color: #0b1a75; margin-bottom: 2px; }
    .ev-info span { font-size: 11px; color: #8492b4; }

    .profile-row {
        display: flex; gap: 12px; padding: 8px 0;
        border-bottom: 0.5px solid #f0f2f8;
        align-items: center; min-width: 0;
    }
    .profile-row:last-child { border-bottom: none; }
    .pr-label { font-size: 12px; color: #8492b4; width: 70px; flex-shrink: 0; }
    .pr-val {
        font-size: 13px; color: #0b1a75; font-weight: 700;
        min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .badge {
        font-size: 11px; font-weight: 700;
        padding: 3px 10px; border-radius: 20px; display: inline-block;
    }
    .badge-active   { background: #e1fdf4; color: #0f6e56; }
    .badge-pending  { background: #fffbeb; color: #92400e; }
    .badge-financial{ background: #e6f1fb; color: #185fa5; }
    .badge-general  { background: #f0f4ff; color: #2025b1; }
    .badge-2fa-on   { color: #1d9e75; font-weight: 700; font-size: 13px; }
    .badge-2fa-off  { color: #e07b00; font-weight: 700; font-size: 13px; }
</style>

{{-- Welcome banner --}}
<div class="welcome-bar">
    <div>
        <h2>Welcome back, {{ explode(' ', auth()->user()->name)[0] }}</h2>
        <p>
            {{ auth()->user()->membershipLevel?->name ?? 'Member' }} member
            @if(auth()->user()->joined_at)
                since {{ auth()->user()->joined_at->format('F Y') }}
            @endif
        </p>
    </div>
    <div class="wb-badge">{{ auth()->user()->membershipLevel?->name ?? 'Member' }}</div>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Membership</div>
        <div class="stat-val" style="font-size:15px;margin-top:4px">
            <span class="stat-dot" style="background:{{ auth()->user()->membership_status === 'active' ? '#1d9e75' : '#e07b00' }}"></span>
            {{ auth()->user()->statusLabel() }}
        </div>
        @if(auth()->user()->membership_expires_at)
            <div class="stat-sub">Renews {{ auth()->user()->membership_expires_at->format('M Y') }}</div>
        @endif
    </div>
    <div class="stat-card">
        <div class="stat-label">Donations</div>
        <div class="stat-val">—</div>
        <div class="stat-sub">Coming in Phase 3</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Events</div>
        <div class="stat-val">—</div>
        <div class="stat-sub">Coming in Phase 4</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Documents</div>
        <div class="stat-val">—</div>
        <div class="stat-sub">Coming in Phase 5</div>
    </div>
</div>

{{-- Cards --}}
<div class="cards-grid">
    {{-- Upcoming events placeholder --}}
    <div class="dash-card">
        <div class="card-hdr">
            <div class="card-title">Upcoming events</div>
            <a href="#" class="card-link">View all</a>
        </div>
        <p style="font-size:13px;color:#8492b4;padding:8px 0">
            Events will appear here once the Events module is enabled.
        </p>
    </div>

    {{-- Profile summary --}}
    <div class="dash-card">
        <div class="card-hdr">
            <div class="card-title">My profile</div>
            <a href="{{ route('portal.profile') }}" class="card-link">Edit</a>
        </div>
        <div class="profile-row">
            <div class="pr-label">Name</div>
            <div class="pr-val">{{ auth()->user()->name }}</div>
        </div>
        <div class="profile-row">
            <div class="pr-label">Email</div>
            <div class="pr-val">{{ auth()->user()->email }}</div>
        </div>
        <div class="profile-row">
            <div class="pr-label">Level</div>
            <div class="pr-val">
                <span class="badge badge-{{ auth()->user()->membershipLevel?->slug ?? 'general' }}">
                    {{ auth()->user()->membershipLevel?->name ?? 'General' }}
                </span>
            </div>
        </div>
        <div class="profile-row">
            <div class="pr-label">Status</div>
            <div class="pr-val">
                <span class="badge badge-{{ auth()->user()->membership_status }}">
                    {{ auth()->user()->statusLabel() }}
                </span>
            </div>
        </div>
        <div class="profile-row">
            <div class="pr-label">2FA</div>
            <div class="pr-val">
                @if(auth()->user()->hasTwoFactorEnabled())
                    <span class="badge-2fa-on">Enabled</span>
                @else
                    <span class="badge-2fa-off">Not set up</span>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection