@extends('auth.layout')
@section('title', 'Confirm Password')

@section('content')
    <h2>Confirm your password</h2>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="info-card">This is a secure area. Please confirm your password before continuing.</div>

    <form method="POST" action="{{ route('password.confirm.store') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input id="password" type="password" name="password"
                class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                required autofocus autocomplete="current-password">
            @error('password') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn-primary">Confirm</button>
    </form>
@endsection
