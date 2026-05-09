@extends('auth.layout')
@section('title', 'Sign In')

@section('content')
    <h2>Sign in</h2>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-error">
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                required autofocus autocomplete="email" placeholder="you@example.com">
            @error('email') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <div class="label-row">
                <label class="form-label" for="password">Password</label>
                <a href="{{ route('password.request') }}" class="auth-link">Forgot password?</a>
            </div>
            <input id="password" type="password" name="password"
                class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                required autocomplete="current-password">
            @error('password') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group form-check">
            <input id="remember" type="checkbox" name="remember">
            <label for="remember">Keep me signed in</label>
        </div>
        <button type="submit" class="btn-primary">Sign in</button>
    </form>
@endsection

@section('footer')
    Not a member yet? <a href="{{ route('register') }}" class="auth-link">Create an account</a>
@endsection
