@extends('layouts.auth')

@section('title', 'Forgot Password — GradNet')

@section('content')

<div class="auth-card">

    {{-- Banner --}}
    <div class="auth-banner">
        <img src="{{ asset('images/icc-background.jpg') }}" alt="GradNet">
        <div class="auth-banner-overlay">
            <img src="{{ asset('images/logo.png') }}" class="auth-banner-logo" alt="GradNet Logo"
                 onerror="this.style.display='none'">
            <span class="auth-banner-title">GradNet</span>
        </div>
    </div>

    {{-- Form --}}
    <div class="auth-body">
        <h2 class="auth-title">Forgot Password</h2>
        <p class="auth-subtitle">Enter your email to receive a password reset link.</p>

        @if(session('status'))
            <div class="flash-alert success mb-3">
                <i class="fas fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif
        @if($errors->any())
            <div class="flash-alert danger mb-3">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-4">
                <label class="form-label required">Email Address</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control"
                           placeholder="you@example.com"
                           required autocomplete="email"
                           value="{{ old('email') }}">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-wide btn-lg">
                <i class="fas fa-paper-plane me-1"></i> Send Reset Link
            </button>
        </form>

        <div class="auth-footer-text">
            <a href="{{ route('login') }}"><i class="fas fa-arrow-left me-1"></i>Back to Sign In</a>
        </div>
    </div>
</div>

@endsection
