{{--
    Bottom navigation — mobile only (≤600px).
    58px fixed. Icons match the left sidebar icon set (FA6).
--}}
<nav class="bottom-nav" aria-label="Bottom navigation">

    <a href="{{ route('dashboard') }}"
       class="bottom-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-house"></i>
        <span>Home</span>
    </a>

    <a href="{{ route('feed.index') }}"
       class="bottom-nav-link {{ request()->routeIs('feed.*') ? 'active' : '' }}">
        <i class="fas fa-rss"></i>
        <span>Feed</span>
    </a>

    <a href="{{ route('connections.index') }}"
       class="bottom-nav-link {{ request()->routeIs('connections.*') ? 'active' : '' }}">
        <i class="fas fa-user-group"></i>
        <span>Connect</span>
    </a>

    <a href="{{ route('notifications.index') }}"
       class="bottom-nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
        <i class="fas fa-bell"></i>
        <span class="bottom-badge" id="notifBadgeBottom" aria-live="polite"></span>
        <span>Alerts</span>
    </a>

    <a href="{{ route('messages.index') }}"
       class="bottom-nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
        <i class="fas fa-message"></i>
        <span>Messages</span>
    </a>

    <a href="{{ route('profile.show', ['user' => auth()->id()]) }}"
       class="bottom-nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <i class="fas fa-user"></i>
        <span>Profile</span>
    </a>

</nav>
