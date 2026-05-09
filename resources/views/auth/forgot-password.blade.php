@extends('auth.layout')
@section('title', 'Forgot Password')

@section('content')
    <h2>Reset your password</h2>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-error">
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="info-card">Enter your email address and we'll send you a password reset link.</div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                required autofocus autocomplete="email" placeholder="you@example.com">
            @error('email') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn-primary">Send reset link</button>
    </form>
@endsection

@section('footer')
    <a href="{{ route('login') }}" class="auth-link">← Back to sign in</a>
@endsection
