<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GradNet')</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Google Fonts: Inter --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- Design system (single file, no per-page @push('styles') needed) --}}
    <link rel="stylesheet" href="{{ asset('css/alumni.css') }}?v={{ filemtime(public_path('css/alumni.css')) }}">
</head>
<body>

{{-- ══════════════════════════════════════════════
     TOPBAR
══════════════════════════════════════════════ --}}
@include('components.topbar')

{{-- ══════════════════════════════════════════════
     3-COLUMN PAGE LAYOUT
     left sidebar  |  main content  |  right widgets
══════════════════════════════════════════════ --}}
<div class="page-layout @unless(View::hasSection('right-sidebar')) no-widgets @endunless">

    {{-- Left sidebar --}}
    <aside class="page-sidebar">
        @include('components.sidebar')
    </aside>

    {{-- Main content area --}}
    <main class="page-main" id="main-content">

        {{-- Flash alerts --}}
        @if(session('success'))
            <div class="flash-alert success" role="alert">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
                <button class="flash-close" onclick="this.parentElement.remove()" aria-label="Dismiss">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif
        @if(session('error'))
            <div class="flash-alert danger" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
                <button class="flash-close" onclick="this.parentElement.remove()" aria-label="Dismiss">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Right sidebar widgets (optional — pages inject via @section('right-sidebar')) --}}
    @hasSection('right-sidebar')
    <aside class="page-widgets right-sidebar">
        @yield('right-sidebar')
    </aside>
    @endif

</div>

{{-- ══════════════════════════════════════════════
     BOTTOM NAV (mobile only — shown via CSS @media)
══════════════════════════════════════════════ --}}
@include('components.bottom-nav')

{{-- ══════════════════════════════════════════════
     TOAST CONTAINER
══════════════════════════════════════════════ --}}
<div id="toast-container" aria-live="polite"></div>

{{-- Web Push service worker --}}
@if(config('app.vapid_public_key'))
    <script>const VAPID_PUBLIC_KEY = "{{ config('app.vapid_public_key') }}";</script>
    <script src="{{ asset('js/push.js') }}"></script>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- ══════════════════════════════════════════════
     GLOBAL JS — topbar dropdown, toast, notif badge
══════════════════════════════════════════════ --}}
<script>
// ── Global broken-avatar guard ───────────────────────────────────────
// Catches any <img class="avatar"> whose src 404s (including dynamically
// injected ones from JS polling). Fires in capture phase so it runs before
// any inline onerror attribute, which prevents the infinite-reload loop.
(function () {
    const FALLBACK = '{{ asset('images/default-avatar.svg') }}';
    document.addEventListener('error', function (e) {
        const img = e.target;
        if (img.tagName !== 'IMG') return;
        if (img.src === FALLBACK || img.dataset.avatarFallback) return;
        img.dataset.avatarFallback = '1'; // guard against re-entry
        img.onerror = null;
        img.src = FALLBACK;
    }, true); // capture phase — runs before inline onerror
})();

// ── Topbar user dropdown ─────────────────────────────────────────────
(function () {
    const btn  = document.getElementById('topbarUserBtn');
    const menu = document.getElementById('topbarDropdown');
    if (!btn || !menu) return;

    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const open = menu.classList.toggle('open');
        btn.setAttribute('aria-expanded', open);
    });

    document.addEventListener('click', function () {
        menu.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
    });
})();

// ── Toast helper ─────────────────────────────────────────────────────
window.showToast = function (message, type = 'info', duration = 3500) {
    const icons = {
        success: '<i class="fas fa-check-circle" style="color:var(--success)"></i>',
        danger:  '<i class="fas fa-exclamation-circle" style="color:var(--danger)"></i>',
        warning: '<i class="fas fa-exclamation-triangle" style="color:var(--warning)"></i>',
        info:    '<i class="fas fa-info-circle" style="color:var(--primary)"></i>',
    };
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <span class="toast-icon">${icons[type] || icons.info}</span>
        <span>${message}</span>
        <button class="toast-close" onclick="this.parentElement.remove()" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), duration);
};

// ── Notification badge polling ───────────────────────────────────────
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    function updateBadge(count) {
        const ids = ['notifBadgeSide', 'notifBadgeBottom', 'notifBadgeTopbar'];
        ids.forEach(function (id) {
            const el = document.getElementById(id);
            if (!el) return;
            if (count > 0) {
                el.textContent = count > 99 ? '99+' : count;
                el.classList.add('visible');
            } else {
                el.classList.remove('visible');
            }
        });

        // Bell shake + pulse ring on topbar
        const bellBtn = document.getElementById('notifTopbarBtn');
        if (bellBtn) {
            if (count > 0) {
                bellBtn.classList.add('has-notif');
                // Re-trigger shake animation each time count changes
                bellBtn.querySelector('i').style.animation = 'none';
                void bellBtn.querySelector('i').offsetWidth; // reflow
                bellBtn.querySelector('i').style.animation = '';
            } else {
                bellBtn.classList.remove('has-notif');
            }
        }

        // Browser tab prefix
        const base = document.title.replace(/^\(\d+\) /, '');
        document.title = count > 0 ? `(${count}) ${base}` : base;
    }

    function checkNotifications() {
        fetch('/notifications/check', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        })
        .then(r => r.ok ? r.json() : null)
        .then(data => { if (data) updateBadge(data.count); })
        .catch(() => {});
    }

    // Reset immediately on the notifications page
    if (window.location.pathname.startsWith('/notifications')) updateBadge(0);

    checkNotifications();
    setInterval(checkNotifications, 30000);
})();
</script>

@stack('scripts')
</body>
</html>
