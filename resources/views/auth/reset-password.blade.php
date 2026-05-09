@extends('auth.layout')
@section('title', 'Reset Password')

@section('content')
    <h2>Choose a new password</h2>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ request()->route('token') }}">
        <div class="form-group">
            <label class="form-label" for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email', request()->email) }}"
                class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                required autofocus autocomplete="email">
            @error('email') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="password">New password</label>
            <input id="password" type="password" name="password"
                class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                required autocomplete="new-password" placeholder="Min. 8 characters">
            @error('password') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirm new password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                class="form-input" required autocomplete="new-password">
        </div>
        <button type="submit" class="btn-primary">Reset password</button>
    </form>
@endsection
