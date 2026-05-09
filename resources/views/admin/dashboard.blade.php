@extends('admin.layout')
@section('title', 'Dashboard')
@section('page-title', 'Admin Dashboard')

@section('topbar-actions')
    <a href="{{ route('admin.members.create') }}" class="topbar-btn">+ Add member</a>
@endsection

@section('content')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px; margin-bottom: 16px;
    }
    @media (min-width: 768px) {
        .stats-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .stat-card { background: #fff; border-radius: 12px; padding: 14px 16px; border: 0.5px solid #e4e9f5; }
    .stat-label { font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #8492b4; margin-bottom: 6px; }
    .stat-val { font-family: 'Montserrat', sans-serif; font-weight: 800; font-size: 24px; color: #0b1a75; }
    .stat-sub { font-size: 11px; margin-top: 3px; }
    .stat-up   { color: #1d9e75; }
    .stat-warn { color: #e07b00; }
    .stat-muted{ color: #8492b4; }

    .dash-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 16px; margin-bottom: 16px;
    }
    @media (min-width: 768px) {
        .dash-grid { grid-template-columns: 1.4fr 1fr; }
    }
    .dash-card { background: #fff; border-radius: 12px; border: 0.5px solid #e4e9f5; padding: 16px; min-width: 0; }
    .card-hdr { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
    .card-title { font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 13px; color: #0b1a75; }
    .card-link { font-size: 12px; color: #2683d4; font-weight: 700; text-decoration: none; }

    .member-row { display: flex; align-items: center; gap: 10px; padding: 7px 0; border-bottom: 0.5px solid #f0f2f8; }
    .member-row:last-child { border-bottom: none; }
    .m-avatar {
        width: 30px; height: 30px; border-radius: 50%; overflow: hidden;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700; color: #fff; flex-shrink: 0;
    }
    .m-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .m-name { font-size: 13px; font-weight: 700; color: #0b1a75; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .badge { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 20px; display: inline-block; }
    .badge-active   { background: #e1fdf4; color: #0f6e56; }
    .badge-pending  { background: #fffbeb; color: #92400e; }
    .badge-suspended{ background: #fef2f2; color: #b91c1c; }

    .module-row { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 0.5px solid #f0f2f8; }
    .module-row:last-child { border-bottom: none; }
    .mod-name { font-size: 13px; color: #0b1a75; font-weight: 700; }
    .mod-sub  { font-size: 11px; color: #8492b4; }
    .toggle { width: 36px; height: 20px; border-radius: 10px; position: relative; flex-shrink: 0; }
    .toggle-on  { background: linear-gradient(90deg, #2025b1, #21b7e7); }
    .toggle-off { background: #dde3f0; }
    .toggle-dot { position: absolute; top: 3px; width: 14px; height: 14px; background: #fff; border-radius: 50%; }
    .toggle-on  .toggle-dot { right: 3px; }
    .toggle-off .toggle-dot { left: 3px; }

    .activity-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 0;
    }
    @media (min-width: 768px) {
        .activity-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0 24px; }
    }
    .activity-item { display: flex; gap: 10px; padding: 7px 0; border-bottom: 0.5px solid #f0f2f8; align-items: flex-start; }
    .activity-item:last-child { border-bottom: none; }
    .act-icon { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .act-icon svg { width: 15px; height: 15px; }
    .act-blue   { background: #e6f1fb; color: #185fa5; }
    .act-green  { background: #e1fdf4; color: #0f6e56; }
    .act-purple { background: #eeedfe; color: #534ab7; }
    .act-text p    { font-size: 12px; color: #0b1a75; font-weight: 700; margin-bottom: 1px; }
    .act-text span { font-size: 11px; color: #8492b4; }
</style>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total members</div>
        <div class="stat-val">{{ $stats['total_members'] }}</div>
        <div class="stat-sub stat-up">↑ {{ $stats['new_this_month'] }} this month</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Financial members</div>
        <div class="stat-val">{{ $stats['financial_members'] }}</div>
        <div class="stat-sub stat-muted">{{ $stats['financial_pct'] }}% of total</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending approval</div>
        <div class="stat-val" style="{{ $stats['pending'] > 0 ? 'color:#e07b00' : '' }}">{{ $stats['pending'] }}</div>
        @if($stats['pending'] > 0)
            <div class="stat-sub stat-warn">Awaiting review</div>
        @else
            <div class="stat-sub stat-muted">All clear</div>
        @endif
    </div>
    <div class="stat-card">
        <div class="stat-label">Donations (YTD)</div>
        <div class="stat-val">—</div>
        <div class="stat-sub stat-muted">Phase 3</div>
    </div>
</div>

{{-- Main grid --}}
<div class="dash-grid">
    {{-- Recent members --}}
    <div class="dash-card">
        <div class="card-hdr">
            <div class="card-title">Recent members</div>
            <a href="{{ route('admin.members') }}" class="card-link">View all</a>
        </div>
        @forelse($recentMembers as $member)
            <div class="member-row">
                <div class="m-avatar" style="background: linear-gradient(135deg, #2025b1, #2683d4)">
                    @if($member->profile_photo_path)
                        <img src="{{ Storage::url($member->profile_photo_path) }}" alt="{{ $member->name }}">
                    @else
                        {{ strtoupper(substr($member->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $member->name)[1] ?? '', 0, 1)) }}
                    @endif
                </div>
                <div class="m-name">{{ $member->name }}</div>
                <span class="badge badge-{{ $member->membership_status }}">{{ $member->statusLabel() }}</span>
            </div>
        @empty
            <p style="font-size:13px;color:#8492b4;padding:8px 0">No members yet.</p>
        @endforelse
    </div>

    {{-- Module status --}}
    <div class="dash-card">
        <div class="card-hdr">
            <div class="card-title">Module status</div>
            <a href="{{ route('admin.modules') }}" class="card-link">Manage</a>
        </div>
        @foreach($modules as $module)
            <div class="module-row">
                <div>
                    <div class="mod-name">{{ $module['name'] }}</div>
                    <div class="mod-sub">{{ $module['description'] ?? '' }}</div>
                </div>
                <div class="toggle {{ $module['enabled'] ? 'toggle-on' : 'toggle-off' }}">
                    <div class="toggle-dot"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Activity --}}
<div class="dash-card">
    <div class="card-hdr">
        <div class="card-title">Recent activity</div>
    </div>
    <div class="activity-grid">
        @forelse($activity as $item)
            <div class="activity-item">
                <div class="act-icon act-{{ $item['color'] }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">{!! $item['icon'] !!}</svg>
                </div>
                <div class="act-text">
                    <p>{{ $item['message'] }}</p>
                    <span>{{ $item['time'] }}</span>
                </div>
            </div>
        @empty
            <p style="font-size:13px;color:#8492b4;padding:8px 0">No recent activity.</p>
        @endforelse
    </div>
</div>

@endsection