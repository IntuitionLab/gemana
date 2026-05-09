@extends('auth.layout')
@section('title', 'Verify Email')

@section('content')
    <h2>Verify your email</h2>

    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success">A new verification link has been sent to your email address.</div>
    @endif

    <div class="info-card">
        Thanks for registering. Before accessing your portal, please verify your email address by clicking the link we sent you.
    </div>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="btn-primary">Resend verification email</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" style="margin-top:1rem;text-align:center;">
        @csrf
        <button type="submit" class="toggle-link">Sign out</button>
    </form>
@endsection
