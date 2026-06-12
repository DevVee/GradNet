<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ICCBI Alumni')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/alumni.css') }}?v={{ filemtime(public_path('css/alumni.css')) }}">
</head>
<body style="overflow:hidden;">

<div class="auth-split">

    {{-- ── Brand Panel (left, hidden on mobile) ─────────────────────── --}}
    <div class="auth-panel-brand">
        <div class="auth-brand-inner" style="animation:fadeInUp 0.6s ease;">

            {{-- Logo + name --}}
            <div class="auth-brand-logo-wrap">
                <img src="{{ asset('images/logo.png') }}"
                     alt="ICCBI Logo"
                     onerror="this.style.display='none'">
                <div class="brand-name">
                    ICCBI Alumni
                    <small>Immaculate Conception College</small>
                </div>
            </div>

            {{-- Headline --}}
            <h1 class="auth-brand-headline">
                Connect.<br>Grow.<br><span>Inspire.</span>
            </h1>
            <p class="auth-brand-sub">
                Your alumni network — reconnect with batchmates, share your journey,
                and discover opportunities together.
            </p>

            {{-- Feature list --}}
            <ul class="auth-feature-list">
                <li>
                    <i class="fas fa-user-friends"></i>
                    Find and reconnect with batchmates
                </li>
                <li>
                    <i class="fas fa-share-alt"></i>
                    Share updates, milestones and memories
                </li>
                <li>
                    <i class="fas fa-calendar-check"></i>
                    Attend events and reunions
                </li>
                <li>
                    <i class="fas fa-briefcase"></i>
                    Discover career opportunities
                </li>
            </ul>

            {{-- Quote --}}
            <div class="auth-brand-quote">
                <p>"Education is not the filling of a pail, but the lighting of a fire."</p>
                <cite>— William Butler Yeats</cite>
            </div>

        </div>
    </div>

    {{-- ── Form Panel (right) ─────────────────────────────────────────── --}}
    <div class="auth-panel-form">
        <div class="auth-form-inner">

            {{-- Mobile-only logo --}}
            <div class="auth-form-logo">
                <img src="{{ asset('images/logo.png') }}"
                     alt="ICCBI Logo"
                     onerror="this.style.display='none'">
                <span>ICCBI Alumni</span>
            </div>

            @yield('content')

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
