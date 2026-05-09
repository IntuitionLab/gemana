@extends('auth.layout')
@section('title', 'Two-Factor Authentication')

@section('content')
    <h2>Two-factor authentication</h2>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div x-data="{ useRecovery: false }">
        {{-- TOTP --}}
        <div x-show="!useRecovery">
            <div class="info-card">Enter the 6-digit code from your authenticator app.</div>
            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="code">Authentication code</label>
                    <input id="code" type="text" name="code" inputmode="numeric" autofocus
                        autocomplete="one-time-code" class="form-input" placeholder="000000">
                </div>
                <button type="submit" class="btn-primary">Verify</button>
            </form>
            <div style="text-align:center;margin-top:1.25rem;">
                <button class="toggle-link" @click="useRecovery = true">Use a recovery code instead</button>
            </div>
        </div>
        {{-- Recovery --}}
        <div x-show="useRecovery">
            <div class="info-card">Enter one of your emergency recovery codes.</div>
            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="recovery_code">Recovery code</label>
                    <input id="recovery_code" type="text" name="recovery_code"
                        autocomplete="one-time-code" class="form-input mono">
                </div>
                <button type="submit" class="btn-primary">Verify recovery code</button>
            </form>
            <div style="text-align:center;margin-top:1.25rem;">
                <button class="toggle-link" @click="useRecovery = false">Use authenticator app instead</button>
            </div>
        </div>
    </div>
@endsection
