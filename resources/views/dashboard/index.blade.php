@extends('layouts.app')

@section('title', 'Home — GradNet')

@section('content')

@php
    $svgFallback = asset('images/default-avatar.svg');
    $authUser    = auth()->user();

    /* Time-aware greeting */
    $hour = (int) now()->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
@endphp

    {{-- ── Hero Banner ──────────────────────────────────────────────── --}}
    <div class="hero-banner">
        <h1>{{ $greeting }}, {{ $authUser->first_name }}! 👋</h1>
        <p>Stay connected with your GradNet batchmates, explore opportunities,<br class="d-none d-md-inline"> and celebrate our alumni community.</p>
        <div class="hero-banner-actions">
            <a href="{{ route('feed.index') }}" class="btn hero-btn-light btn-sm">
                <i class="fas fa-rss me-1"></i> Browse Feed
            </a>
            <a href="{{ route('connections.index') }}" class="btn hero-btn-light btn-sm">
                <i class="fas fa-user-group me-1"></i> Find Alumni
            </a>
            <a href="{{ route('events.index') }}" class="btn hero-btn-light btn-sm">
                <i class="fas fa-calendar-days me-1"></i> Events
            </a>
        </div>
    </div>

    {{-- ── Batchmates ───────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <h2 class="section-title-sm">
                <i class="fas fa-user-group text-primary me-1"></i>
                Your Batchmates
                {{-- Only show the program+year badge, NOT the "set your program" hint --}}
                @if($canFetchBatchmates && $batchmatesTitle)
                    <span class="badge badge-primary ms-2" style="font-size:0.65rem;">{{ $batchmatesTitle }}</span>
                @endif
            </h2>
            <a href="{{ route('connections.index') }}" class="section-action">View All</a>
        </div>

        <div class="card-body" style="padding-top:var(--sp-3);padding-bottom:var(--sp-3);">
            @if (!$canFetchBatchmates)
                <div class="empty-state" style="padding:var(--sp-6) var(--sp-4);">
                    <i class="fas fa-user-group icon"></i>
                    <h3>No Program Set</h3>
                    <p>Update your profile with your program and graduation year to find batchmates.</p>
                    <a href="{{ route('profile.edit', $authUser->id) }}" class="btn btn-primary btn-sm mt-2">
                        <i class="fas fa-user-pen me-1"></i> Update Profile
                    </a>
                </div>
            @elseif ($batchmates->isEmpty())
                <div class="empty-state" style="padding:var(--sp-6) var(--sp-4);">
                    <i class="fas fa-users icon"></i>
                    <h3>No Batchmates Yet</h3>
                    <p>No alumni found for your program and year. Check back later!</p>
                </div>
            @else
                <div class="scroll-fade-right" style="--surface:var(--surface);">
                    <div class="scroll-x d-flex gap-3 pb-2" id="batchmateScroll">
                        @foreach ($batchmates as $batchmate)
                            <a href="{{ route('profile.show', $batchmate->id) }}"
                               class="d-flex flex-column align-items-center text-decoration-none flex-shrink-0"
                               style="width:80px;">
                                <div style="position:relative;">
                                    <img src="{{ $batchmate->avatar_url }}"
                                         alt="{{ $batchmate->first_name }}"
                                         class="avatar avatar-lg avatar-ring-white mb-2"
                                         style="transition:var(--ease);"
                                         onerror="this.onerror=null;this.src=this.dataset.fallback"
                                         data-fallback="{{ $svgFallback }}">
                                </div>
                                <span class="fw-600 truncate text-center text-dark"
                                      style="font-size:var(--text-xs);width:100%;">
                                    {{ $batchmate->first_name }}
                                </span>
                                <span class="text-muted text-center"
                                      style="font-size:0.625rem;">
                                    {{ $batchmate->graduation_year }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ── News & Updates ───────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <h2 class="section-title-sm">
                <i class="fas fa-bullhorn text-primary me-1"></i> News &amp; Updates
            </h2>
            <a href="{{ route('news.index') }}" class="section-action">View All</a>
        </div>
        <div class="card-body">
            @if ($news->isEmpty())
                <div class="empty-state" style="padding:var(--sp-6) var(--sp-4);">
                    <i class="fas fa-newspaper icon"></i>
                    <h3>No News Yet</h3>
                    <p>Check back soon for announcements and updates.</p>
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-2 g-3">
                    @foreach ($news as $item)
                        <div class="col">
                            <a href="{{ route('news.show', $item->id) }}"
                               class="card card-hover text-decoration-none d-block h-100">
                                <div style="height:140px;overflow:hidden;background:var(--surface-3);">
                                    <img src="{{ $item->image_url ?? asset('images/ICCLOGO.png') }}"
                                         alt="{{ $item->title }}"
                                         style="width:100%;height:100%;object-fit:cover;">
                                </div>
                                <div class="card-body" style="padding:var(--sp-3);">
                                    <h3 class="fw-700 line-clamp-2 text-dark mb-1"
                                        style="font-size:var(--text-sm);">{{ $item->title }}</h3>
                                    <p class="text-muted line-clamp-2 mb-2"
                                       style="font-size:var(--text-xs);">{{ $item->description }}</p>
                                    <span class="text-muted" style="font-size:var(--text-xs);">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ $item->created_at->format('F j, Y') }}
                                    </span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Upcoming Events ─────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <h2 class="section-title-sm">
                <i class="fas fa-calendar-days text-primary me-1"></i> Upcoming Events
            </h2>
            <a href="{{ route('events.index') }}" class="section-action">View All</a>
        </div>
        <div class="card-body" style="padding:0;">
            @if ($upcomingEvents->isEmpty())
                <div class="empty-state" style="padding:var(--sp-6) var(--sp-4);">
                    <i class="fas fa-calendar-days icon"></i>
                    <h3>No Upcoming Events</h3>
                    <p>Nothing scheduled yet — check back soon!</p>
                </div>
            @else
                @foreach ($upcomingEvents as $event)
                    <a href="{{ route('events.show', $event->id) }}"
                       class="d-flex gap-3 text-decoration-none p-3 border-bottom"
                       style="transition:background 0.15s ease;"
                       onmouseover="this.style.background='var(--surface-2)'"
                       onmouseout="this.style.background='transparent'">
                        <div class="flex-shrink-0" style="width:68px;height:68px;border-radius:var(--radius-sm);overflow:hidden;background:var(--surface-3);">
                            <img src="{{ $event->image_url ?? asset('images/ICCLOGO.png') }}"
                                 alt="{{ $event->title }}"
                                 style="width:100%;height:100%;object-fit:cover;">
                        </div>
                        <div class="flex-grow-1" style="min-width:0;">
                            <div class="fw-700 line-clamp-2 text-dark" style="font-size:var(--text-sm);">
                                {{ $event->title }}
                            </div>
                            <div class="text-primary mt-1" style="font-size:var(--text-xs);">
                                <i class="far fa-clock me-1"></i>
                                {{ $event->event_datetime->format('D, M d · h:i A') }}
                            </div>
                            @if($event->location)
                                <div class="text-muted" style="font-size:var(--text-xs);">
                                    <i class="fas fa-location-dot me-1"></i>{{ $event->location }}
                                </div>
                            @endif
                        </div>
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    {{-- ── Past Events ─────────────────────────────────────────────── --}}
    @if ($previousEvents->isNotEmpty())
    <div class="card">
        <div class="card-header">
            <h2 class="section-title-sm text-secondary">
                <i class="fas fa-clock-rotate-left me-1"></i> Past Events
            </h2>
            <a href="{{ route('events.index', ['past' => 1]) }}" class="section-action">See All</a>
        </div>
        <div class="card-body" style="padding:0;">
            @foreach ($previousEvents as $event)
                <a href="{{ route('events.show', $event->id) }}"
                   class="d-flex gap-3 text-decoration-none p-3 border-bottom"
                   style="transition:background 0.15s ease;"
                   onmouseover="this.style.background='var(--surface-2)'"
                   onmouseout="this.style.background='transparent'">
                    <div class="flex-shrink-0 position-relative" style="width:68px;height:68px;border-radius:var(--radius-sm);overflow:hidden;background:var(--surface-3);">
                        <img src="{{ $event->image_url ?? asset('images/ICCLOGO.png') }}"
                             alt="{{ $event->title }}"
                             style="width:100%;height:100%;object-fit:cover;opacity:0.75;">
                        <span class="badge badge-muted position-absolute"
                              style="bottom:4px;left:4px;font-size:0.55rem;padding:1px 5px;">Ended</span>
                    </div>
                    <div class="flex-grow-1" style="min-width:0;">
                        <div class="fw-600 line-clamp-2 text-secondary" style="font-size:var(--text-sm);">
                            {{ $event->title }}
                        </div>
                        <div class="text-muted mt-1" style="font-size:var(--text-xs);">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ $event->event_datetime->format('M d, Y') }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Connect Confirmation Modal ──────────────────────────────── --}}
    <div class="modal-overlay" id="connectModal">
        <div class="modal-box modal-sm">
            <div class="modal-header">
                <h5 class="modal-title">Send Connection Request</h5>
                <button class="modal-close" onclick="closeConnectModal()" aria-label="Close">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body">
                <p style="font-size:var(--text-sm);">
                    Send a connection request to <strong id="modalBatchmateName"></strong>?
                </p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-surface btn-sm" onclick="closeConnectModal()">Cancel</button>
                <button class="btn btn-primary btn-sm" id="confirmConnectBtn">
                    <i class="fas fa-user-plus me-1"></i> Send Request
                </button>
            </div>
        </div>
    </div>

@endsection

{{-- ── Right sidebar widgets ──────────────────────────────────────── --}}
@section('right-sidebar')

@php
    $svgFallback = asset('images/default-avatar.svg');
    $authUser    = auth()->user();
@endphp

    {{-- ── People You May Know (batchmates not yet connected to) ────── --}}
    @if ($canFetchBatchmates && $batchmates->isNotEmpty())
    @php
        /* Get IDs already connected to, so we can show "Connect" only for new ones */
        $myConnectedIds = $authUser->connections()
            ->accepted()
            ->with(['requester:id', 'recipient:id'])
            ->get()
            ->map(fn($c) => $c->requester_id === $authUser->id ? $c->recipient_id : $c->requester_id)
            ->toArray();
        $suggestions = $batchmates->whereNotIn('id', $myConnectedIds)->take(5);
    @endphp
    @if ($suggestions->isNotEmpty())
    <div class="widget">
        <div class="widget-header">
            <span class="widget-title"><i class="fas fa-user-group me-1 text-primary"></i> People You May Know</span>
            <a href="{{ route('connections.index') }}" class="section-action">See all</a>
        </div>
        <div class="widget-body" style="padding-top:0;">
            @foreach ($suggestions as $person)
                <div style="display:flex;align-items:center;gap:var(--sp-3);padding:var(--sp-3) 0;border-bottom:1px solid var(--border-light);">
                    <a href="{{ route('profile.show', $person->id) }}" style="flex-shrink:0;">
                        <img src="{{ $person->avatar_url }}"
                             alt="{{ $person->first_name }}"
                             class="avatar avatar-sm"
                             style="border:2px solid var(--border);"
                             onerror="this.onerror=null;this.src=this.dataset.fallback"
                             data-fallback="{{ $svgFallback }}">
                    </a>
                    <div style="flex:1;min-width:0;">
                        <a href="{{ route('profile.show', $person->id) }}"
                           class="fw-600 text-dark text-decoration-none truncate d-block"
                           style="font-size:var(--text-xs);">
                            {{ $person->first_name }} {{ $person->last_name }}
                        </a>
                        <div class="text-muted" style="font-size:0.65rem;">{{ $person->graduation_year }}</div>
                    </div>
                    <form method="POST" action="{{ route('connections.store') }}" style="flex-shrink:0;">
                        @csrf
                        <input type="hidden" name="recipient_id" value="{{ $person->id }}">
                        <button type="submit"
                                class="btn btn-outline btn-sm"
                                style="padding:3px 10px;font-size:0.65rem;border-radius:var(--radius-full);">
                            + Connect
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    @endif

    {{-- ── Upcoming events ────────────────────────────────────────────── --}}
    @if ($upcomingEvents->isNotEmpty())
    <div class="widget">
        <div class="widget-header">
            <span class="widget-title"><i class="fas fa-calendar-days me-1 text-primary"></i> Upcoming Events</span>
            <a href="{{ route('events.index') }}" class="section-action">See all</a>
        </div>
        <div class="widget-body" style="padding-top:var(--sp-1);">
            @foreach ($upcomingEvents->take(3) as $event)
                <a href="{{ route('events.show', $event->id) }}"
                   class="people-row text-decoration-none">
                    <div style="flex-shrink:0;width:40px;height:40px;
                                background:var(--primary-light);
                                border-radius:var(--radius-sm);
                                display:flex;flex-direction:column;
                                align-items:center;justify-content:center;
                                font-size:0.6rem;font-weight:700;line-height:1.2;
                                color:var(--primary);">
                        <span style="font-size:0.85rem;line-height:1;">{{ $event->event_datetime->format('d') }}</span>
                        <span>{{ $event->event_datetime->format('M') }}</span>
                    </div>
                    <div class="info">
                        <div class="name line-clamp-2">{{ $event->title }}</div>
                        <div class="meta">
                            <i class="far fa-clock me-1"></i>{{ $event->event_datetime->format('h:i A') }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @else
    {{-- No events: show a quick-links widget instead --}}
    <div class="widget">
        <div class="widget-header">
            <span class="widget-title">Explore</span>
        </div>
        <div class="widget-body" style="padding:0;">
            @foreach ([
                ['icon'=>'fas fa-rss','label'=>'Browse Feed','route'=>'feed.index'],
                ['icon'=>'fas fa-user-group','label'=>'Find Alumni','route'=>'connections.index'],
                ['icon'=>'fas fa-newspaper','label'=>'Latest News','route'=>'news.index'],
                ['icon'=>'fas fa-people-group','label'=>'Groups','route'=>'groups.index'],
            ] as $link)
            <a href="{{ route($link['route']) }}"
               class="sidebar-link" style="border-radius:0;">
                <i class="{{ $link['icon'] }}"></i>
                <span>{{ $link['label'] }}</span>
                <i class="fas fa-chevron-right ms-auto" style="font-size:0.6rem;color:var(--text-muted);"></i>
            </a>
            @endforeach
        </div>
    </div>
    @endif

@endsection

@push('scripts')
<script>
const connectModal = document.getElementById('connectModal');
let pendingBatchmateId = null;

function closeConnectModal() {
    connectModal.classList.remove('open');
    pendingBatchmateId = null;
}
connectModal.addEventListener('click', function (e) {
    if (e.target === connectModal) closeConnectModal();
});

const confirmBtn = document.getElementById('confirmConnectBtn');
if (confirmBtn) {
    confirmBtn.addEventListener('click', function () {
        if (!pendingBatchmateId) return;
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        fetch('{{ route("connections.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ recipient_id: pendingBatchmateId }),
        })
        .then(r => r.json())
        .then(() => { closeConnectModal(); showToast('Connection request sent!', 'success'); })
        .catch(() => closeConnectModal());
    });
}

// Horizontal scroll with mouse wheel on batchmate row
const scrollEl = document.getElementById('batchmateScroll');
if (scrollEl) {
    scrollEl.addEventListener('wheel', function (e) {
        if (e.deltaY !== 0) { e.preventDefault(); this.scrollLeft += e.deltaY; }
    }, { passive: false });
}
</script>
@endpush
