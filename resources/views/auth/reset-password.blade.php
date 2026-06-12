@extends('layouts.auth')

@section('title', 'Reset Password — GradNet')

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
        <h2 class="auth-title">Reset Password</h2>
        <p class="auth-subtitle">Enter your new password below.</p>

        @if($errors->any())
            <div class="flash-alert danger mb-3">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label required">Email Address</label>
                <div class="input-wrap">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control"
                           placeholder="you@example.com"
                           required autocomplete="email"
                           value="{{ old('email', request()->get('email')) }}">
                </div>
            </div>

            {{-- New Password --}}
            <div class="mb-3">
                <label class="form-label required">New Password <span class="text-muted">(min. 8 chars)</span></label>
                <div class="input-wrap" style="position:relative;">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" id="pw1" class="form-control"
                           placeholder="••••••••" required minlength="8"
                           autocomplete="new-password"
                           style="padding-right:40px;">
                    <button type="button" id="togglePw1"
                            onclick="togglePwd('pw1','togglePw1Icon')"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);
                                   background:none;border:none;color:var(--text-muted);cursor:pointer;">
                        <i class="fas fa-eye" id="togglePw1Icon"></i>
                    </button>
                </div>
            </div>

            {{-- Confirm Password --}}
            <div class="mb-4">
                <label class="form-label required">Confirm New Password</label>
                <div class="input-wrap" style="position:relative;">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password_confirmation" id="pw2" class="form-control"
                           placeholder="••••••••" required minlength="8"
                           autocomplete="new-password"
                           style="padding-right:40px;">
                    <button type="button" id="togglePw2"
                            onclick="togglePwd('pw2','togglePw2Icon')"
                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);
                                   background:none;border:none;color:var(--text-muted);cursor:pointer;">
                        <i class="fas fa-eye" id="togglePw2Icon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-wide btn-lg">
                <i class="fas fa-key me-1"></i> Reset Password
            </button>
        </form>

        <div class="auth-footer-text">
            <a href="{{ route('login') }}"><i class="fas fa-arrow-left me-1"></i>Back to Sign In</a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.getElementById('pw2')?.addEventListener('input', function () {
    const pw1 = document.getElementById('pw1').value;
    this.setCustomValidity(pw1 && this.value && pw1 !== this.value ? 'Passwords do not match' : '');
});
</script>
@endpush
