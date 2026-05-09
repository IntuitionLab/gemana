@extends('auth.layout')
@section('title', 'Create Account')

@section('content')
    <h2>Create your account</h2>

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="name">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                class="form-input {{ $errors->has('name') ? 'error' : '' }}"
                required autocomplete="name" placeholder="Jane Smith">
            @error('name') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                required autocomplete="email" placeholder="you@example.com">
            @error('email') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="membership_level">Membership type</label>
            <select id="membership_level" name="membership_level" class="form-input">
                @foreach (\App\Modules\Members\Models\MembershipLevel::active()->get() as $level)
                    @if (!$level->requires_approval)
                        <option value="{{ $level->slug }}"
                            {{ old('membership_level') === $level->slug ? 'selected' : '' }}>
                            {{ $level->name }} — {{ $level->feeLabel() }}
                        </option>
                    @endif
                @endforeach
            </select>
            <div class="form-hint">Life and Honorary memberships are awarded by the committee.</div>
        </div>
        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input id="password" type="password" name="password"
                class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                required autocomplete="new-password" placeholder="Min. 8 characters">
            @error('password') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                class="form-input" required autocomplete="new-password">
        </div>
        <div class="form-group form-check">
            <input id="terms" type="checkbox" name="terms">
            <label for="terms">
                I agree to the <a href="#" class="auth-link">terms and conditions</a>
            </label>
        </div>
        @error('terms') <div class="form-error" style="margin-top:-0.75rem;margin-bottom:0.75rem;">{{ $message }}</div> @enderror
        <button type="submit" class="btn-primary">Create account</button>
    </form>
@endsection

@section('footer')
    Already have an account? <a href="{{ route('login') }}" class="auth-link">Sign in</a>
@endsection
