@extends('layouts.auth')

@section('title', 'Registration Submitted — ICCBI Alumni')

@section('content')
<div class="auth-page" style="overflow:hidden;">

    {{-- Blurred backdrop --}}
    <div style="position:fixed;inset:0;background:rgba(0,0,0,0.5);backdrop-filter:blur(5px);z-index:1;"></div>

    {{-- Popup card --}}
    <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
                width:90%;max-width:360px;z-index:2;
                background:#fff;border-radius:var(--radius-lg);
                box-shadow:var(--shadow-xl);padding:2rem 1.5rem;text-align:center;
                animation:scaleIn 0.45s ease forwards;">

        <i class="fas fa-hourglass-half"
           style="font-size:3.5rem;color:var(--primary);display:block;
                  margin-bottom:var(--sp-3);animation:bounce 1.5s infinite;"></i>

        <h2 style="font-size:var(--text-xl);font-weight:700;
                   background:linear-gradient(90deg,var(--primary),var(--accent));
                   -webkit-background-clip:text;background-clip:text;color:transparent;
                   margin-bottom:var(--sp-2);">Account Under Review</h2>

        <span class="badge" style="background:#fff8e1;color:#f59e0b;border:1px solid #fde68a;
                                   border-radius:var(--radius-full);font-size:var(--text-xs);
                                   font-weight:600;padding:3px 14px;display:inline-block;
                                   margin-bottom:var(--sp-4);">
            <i class="fas fa-clock me-1"></i>Pending Approval
        </span>

        <p style="font-size:var(--text-sm);color:var(--text-muted);line-height:1.6;margin-bottom:var(--sp-5);">
            Your registration has been submitted successfully!<br><br>
            An administrator will review your information and approve your account.
            You will be notified via <strong>{{ $email ?? 'email' }}</strong> once approved.
        </p>

        <button class="btn btn-primary btn-wide"
                onclick="window.location.href='{{ route('login') }}'">
            <i class="fas fa-arrow-left me-1"></i> Back to Sign In
        </button>
    </div>
</div>
@endsection
