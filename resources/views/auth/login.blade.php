@extends('layouts.app')

@section('content')
    <div class="card" style="max-width: 420px; margin: 40px auto;">
        @if (session('status') === 'login-link-sent')
            <p style="margin-top:0;">Check your inbox — we've sent a link to log in.</p>
            <p style="color: var(--ink-soft); font-size: 14px;">It expires in 15 minutes.</p>
        @else
            <p style="margin-top:0; color: var(--ink-soft); font-size: 14px;">
                Enter your email and we'll send you a link to log in.
            </p>

            <form method="POST" action="{{ route('login.link') }}">
                @csrf

                <div style="position:absolute;left:-9999px;" aria-hidden="true">
                    <label for="website">Leave this field empty</label>
                    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                </div>

                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com"
                    required autofocus autocomplete="username">
                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror

                <button type="submit" class="btn btn-solid" style="margin-top: 12px; width: 100%; justify-content: center;">
                    Continue with email
                </button>
            </form>
        @endif
    </div>
@endsection
