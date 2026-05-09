@extends(request()->is('admin/*') ? 'admin.layout' : 'portal.layout')
@section('title', $title)
@section('page-title', $title)

@section('content')
<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:320px;text-align:center;padding:40px 20px">
    <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#0b1a75,#21b7e7);display:flex;align-items:center;justify-content:center;margin-bottom:20px">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#fff" style="width:30px;height:30px"><path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
    </div>
    <h2 style="font-family:'Montserrat',sans-serif;font-weight:800;font-size:20px;color:#0b1a75;margin-bottom:8px">
        {{ $title }} module
    </h2>
    <p style="font-size:14px;color:#8492b4;max-width:320px;line-height:1.6">
        This module is coming in Phase {{ $phase }} of the Gemana build.
    </p>
</div>
@endsection