{{--
    Left sidebar — 240px desktop | 64px tablet (icon-only) | hidden mobile.
    Contains: mini profile card + navigation links.
    Active states via request()->routeIs().
    Icons: FA6 — Feed uses fa-rss (not fa-newspaper which is for News).
--}}
@php
    $authUser    = auth()->user();
    $svgAvatar   = asset('images/default-avatar.svg');
    $avatarUrl   = $authUser?->profile_picture
        ? asset('storage/' . $authUser->profile_picture)
        : $svgAvatar;
    $connections = $authUser?->connections()->count() ?? 0;
    $posts       = $authUser?->posts()->count() ?? 0;
@endphp

<div class="left-sidebar">

    {{-- ── Mini profile card ─────────────────────────────────────── --}}
    <a href="{{ route('profile.show', ['user' => auth()->id()]) }}"
       class="sidebar-profile-card"
       title="View My Profile">

        {{-- Banner strip --}}
        <div class="sidebar-profile-banner"></div>

        {{-- Avatar + name pulled up 20px to overlap banner --}}
        <div class="sidebar-profile-content">
            <img src="{{ $avatarUrl }}"
                 alt="{{ $authUser?->first_name }}"
                 class="sidebar-profile-avatar"
                 onerror="this.onerror=null;this.src=this.dataset.fallback"
                 data-fallback="{{ $svgAvatar }}">
            <div class="sidebar-profile-info">
                <div class="name">{{ $authUser?->first_name }} {{ $authUser?->last_name }}</div>
                <div class="meta">
                    {{ $authUser?->program ?? 'ICCBI Alumni' }}@if($authUser?->graduation_year) · {{ $authUser->graduation_year }}@endif
                </div>
            </div>
        </div>

        {{-- Stats row --}}
        <div class="sidebar-profile-stats">
            <div class="sidebar-stat">
                <div class="value">{{ $connections }}</div>
                <div class="label">Connections</div>
            </div>
            <div class="sidebar-stat">
                <div class="value">{{ $posts }}</div>
                <div class="label">Posts</div>
            </div>
        </div>

    </a>

    {{-- ── Navigation ─────────────────────────────────────────────── --}}
    <nav class="sidebar-nav" aria-label="Main navigation">

        <div class="sidebar-section-label">Main</div>

        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-house sidebar-icon-home"></i>
            <span>Home</span>
        </a>

        <a href="{{ route('feed.index') }}"
           class="sidebar-link {{ request()->routeIs('feed.*') ? 'active' : '' }}">
            <i class="fas fa-rss sidebar-icon-feed"></i>
            <span>Feed</span>
        </a>

        <a href="{{ route('connections.index') }}"
           class="sidebar-link {{ request()->routeIs('connections.*') ? 'active' : '' }}">
            <i class="fas fa-user-group sidebar-icon-connect"></i>
            <span>Connections</span>
        </a>

        <a href="{{ route('messages.index') }}"
           class="sidebar-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
            <i class="fas fa-message sidebar-icon-msg"></i>
            <span>Messages</span>
        </a>

        <a href="{{ route('notifications.index') }}"
           class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"
           id="notifSideLink">
            <i class="fas fa-bell sidebar-icon-notif"></i>
            <span>Notifications</span>
            <span class="sidebar-badge" id="notifBadgeSide" aria-live="polite"></span>
        </a>

        <div class="sidebar-section-label">Discover</div>

        <a href="{{ route('events.index') }}"
           class="sidebar-link {{ request()->routeIs('events.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-days sidebar-icon-events"></i>
            <span>Events</span>
        </a>

        <a href="{{ route('news.index') }}"
           class="sidebar-link {{ request()->routeIs('news.*') ? 'active' : '' }}">
            <i class="fas fa-newspaper sidebar-icon-news"></i>
            <span>News</span>
        </a>

        <a href="{{ route('groups.index') }}"
           class="sidebar-link {{ request()->routeIs('groups.*') ? 'active' : '' }}">
            <i class="fas fa-people-group sidebar-icon-groups"></i>
            <span>Groups</span>
        </a>

        @if($authUser?->isAdmin())
            <div class="sidebar-section-label">Admin</div>
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                <i class="fas fa-shield-halved sidebar-icon-admin"></i>
                <span>Admin Panel</span>
            </a>
        @endif

    </nav>

</div>{{-- /left-sidebar --}}
