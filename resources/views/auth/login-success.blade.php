@extends('layouts.auth')

@section('title', 'Welcome — GradNet')

@section('content')
<div class="auth-page" style="overflow:hidden;">

    {{-- Blurred backdrop --}}
    <div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(5px);z-index:1;"></div>

    {{-- Welcome popup --}}
    <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
                width:90%;max-width:320px;z-index:2;
                background:#fff;border-radius:var(--radius-lg);
                box-shadow:var(--shadow-xl);padding:2rem 1.5rem;text-align:center;
                animation:scaleIn 0.45s ease forwards;">

        <img src="{{ asset('images/gradnet-logo.png') }}"
             alt="GradNet Logo"
             style="width:100px;height:auto;margin-bottom:var(--sp-4);
                    border-radius:var(--radius-sm);animation:bounce 1.5s infinite;"
             onerror="this.style.display='none'">

        <p style="font-size:var(--text-lg);font-weight:700;
                  background:linear-gradient(90deg,var(--primary),var(--accent));
                  -webkit-background-clip:text;background-clip:text;color:transparent;
                  margin-bottom:var(--sp-5);">
            Welcome, {{ $user->first_name }}!
        </p>

        <button class="btn btn-primary btn-wide"
                onclick="window.location.href='{{ route('dashboard') }}'">
            <i class="fas fa-arrow-right me-1"></i> Continue
        </button>
    </div>
</div>
@endsection
