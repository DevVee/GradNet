{{--
    Sticky topbar — 60px, full-width, dark gradient background.
    Brand: GradNet logo + name (graduation cap icon).
    Contains: brand, global search, messages, notifications, user avatar dropdown.
--}}
@php
    $me          = auth()->user();
    $svgAvatar   = asset('images/default-avatar.svg');
    $avatarSrc   = $me?->avatar_url ?? $svgAvatar;
    $isMessages  = request()->routeIs('messages.*');
    $isNotifs    = request()->routeIs('notifications.*');
@endphp
<header class="topbar" role="banner">
    <div class="topbar-inner">

        {{-- Brand --}}
        <a href="{{ route('dashboard') }}" class="topbar-brand" title="GradNet Home">
            <div class="topbar-brand-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <span class="topbar-brand-name">
                Grad<span>Net</span>
            </span>
        </a>

        {{-- Global search --}}
        <div class="topbar-search">
            <i class="fas fa-search topbar-search-icon"></i>
            <input type="search"
                   id="globalSearch"
                   placeholder="Search alumni, events, news…"
                   autocomplete="off"
                   aria-label="Global search"
                   onkeydown="handleSearch(event)">
        </div>

        {{-- Right actions --}}
        <div class="topbar-actions">

            {{-- Messages --}}
            <a href="{{ route('messages.index') }}"
               class="topbar-btn {{ $isMessages ? 'active' : '' }}"
               title="Messages"
               aria-label="Messages">
                <i class="fas fa-message"></i>
                <span class="topbar-badge" id="msgBadgeTopbar" aria-live="polite"></span>
            </a>

            {{-- Notifications --}}
            <a href="{{ route('notifications.index') }}"
               class="topbar-btn {{ $isNotifs ? 'active' : '' }}"
               title="Notifications"
               aria-label="Notifications"
               id="notifTopbarBtn">
                <i class="fas fa-bell"></i>
                <span class="topbar-badge" id="notifBadgeTopbar" aria-live="polite"></span>
            </a>

            {{-- User avatar + dropdown --}}
            <div class="topbar-user">
                <button id="topbarUserBtn"
                        class="topbar-user-btn"
                        aria-expanded="false"
                        aria-haspopup="true"
                        aria-label="User menu">
                    <img src="{{ $avatarSrc }}"
                         alt="{{ $me?->first_name }}"
                         class="avatar avatar-sm"
                         onerror="this.onerror=null;this.src=this.dataset.fallback"
                         data-fallback="{{ $svgAvatar }}">
                    <span class="topbar-user-name">{{ $me?->first_name }}</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </button>

                <div class="topbar-dropdown" id="topbarDropdown" role="menu">
                    <div class="topbar-dropdown-header">
                        <div class="name">{{ $me?->first_name }} {{ $me?->last_name }}</div>
                        <div class="email">{{ $me?->email }}</div>
                    </div>

                    <a href="{{ route('profile.show', ['user' => auth()->id()]) }}" role="menuitem">
                        <i class="fas fa-user"></i> My Profile
                    </a>
                    <a href="{{ route('profile.edit', ['user' => auth()->id()]) }}" role="menuitem">
                        <i class="fas fa-gear"></i> Account Settings
                    </a>

                    @if($me?->isAdmin())
                        <div class="divider"></div>
                        <a href="{{ route('admin.dashboard') }}" role="menuitem">
                            <i class="fas fa-shield-halved"></i> Admin Panel
                        </a>
                    @endif

                    <div class="divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" role="menuitem">
                            <i class="fas fa-arrow-right-from-bracket"></i> Log Out
                        </button>
                    </form>
                </div>
            </div>

        </div>{{-- /topbar-actions --}}

    </div>{{-- /topbar-inner --}}
</header>
