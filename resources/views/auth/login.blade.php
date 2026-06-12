@extends('layouts.auth')

@section('title', 'Sign In — GradNet')

@section('content')

{{-- Form header --}}
<div class="auth-form-header">
    <h2 class="auth-form-title">Welcome back 👋</h2>
    <p class="auth-form-subtitle">Sign in to your GradNet account to continue.</p>
</div>

{{-- Flash messages --}}
@if(session('status'))
    <div class="flash-alert success mb-4">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('status') }}</span>
    </div>
@endif
@if($errors->has('email_or_phone'))
    <div class="flash-alert danger mb-4">
        <i class="fas fa-exclamation-circle"></i>
        <span>{{ $errors->first('email_or_phone') }}</span>
    </div>
@endif

<form method="POST" action="{{ route('login') }}" autocomplete="on">
    @csrf

    {{-- Email / Phone --}}
    <div class="mb-4">
        <label class="form-label required">Email or Phone</label>
        <div class="input-wrap">
            <i class="fas fa-envelope input-icon"></i>
            <input type="text"
                   name="email_or_phone"
                   class="form-control @error('email_or_phone') is-invalid @enderror"
                   placeholder="you@example.com"
                   value="{{ old('email_or_phone') }}"
                   required
                   autocomplete="username"
                   autofocus>
        </div>
    </div>

    {{-- Password --}}
    <div class="mb-3">
        <label class="form-label required">Password</label>
        <div class="input-wrap" style="position:relative;">
            <i class="fas fa-lock input-icon"></i>
            <input type="password"
                   name="password"
                   id="loginPassword"
                   class="form-control"
                   placeholder="••••••••"
                   required
                   autocomplete="current-password"
                   style="padding-right:42px;">
            <button type="button"
                    onclick="toggleLoginPwd()"
                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);
                           background:none;border:none;color:var(--text-muted);cursor:pointer;
                           font-size:0.85rem;padding:4px;transition:var(--ease);"
                    title="Show / hide password">
                <i class="fas fa-eye" id="loginPwdIcon"></i>
            </button>
        </div>
    </div>

    {{-- Remember + Forgot --}}
    <div class="d-flex align-items-center justify-content-between mb-5">
        <label class="d-flex align-items-center gap-2"
               style="font-size:var(--text-sm);cursor:pointer;font-weight:500;color:var(--text-body);">
            <input type="checkbox"
                   name="remember_me"
                   {{ old('remember_me') ? 'checked' : '' }}
                   style="width:15px;height:15px;accent-color:var(--primary);cursor:pointer;">
            Remember me
        </label>
        <a href="{{ route('password.request') }}"
           style="font-size:var(--text-sm);color:var(--primary);font-weight:600;">
            Forgot password?
        </a>
    </div>

    <button type="submit" class="btn btn-primary btn-wide btn-lg">
        <i class="fas fa-sign-in-alt me-2"></i> Sign In
    </button>
</form>

<div class="auth-footer-text">
    Don't have an account?
    <a href="{{ route('register') }}">Create account →</a>
</div>

@endsection

@push('scripts')
<script>
function toggleLoginPwd() {
    const input = document.getElementById('loginPassword');
    const icon  = document.getElementById('loginPwdIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
@endpush
