@extends('layouts.app')

@section('title', $event->title . ' — GradNet')

@section('content')

@php
    /*
     * Pre-compute every value that would appear inside an HTML attribute,
     * so {{ }} expressions in attributes contain only simple variables
     * (no -> chains or function calls) which the IDE JS parser can handle.
     *
     * Comments are mapped to plain arrays so the loop uses $cd['key']
     * bracket access (valid JS) instead of $c->prop (invalid JS arrow).
     */
    $rsvpStatus    = $userRsvp ? $userRsvp->status : null;
    $svgFallback   = asset('images/default-avatar.svg');
    $likedClass    = $userLiked ? 'loved' : '';
    $likedIcon     = $userLiked ? 'fas'   : 'far';
    $authAvatarUrl = $authUser->avatar_url;
    $authFirstName = $authUser->first_name;
    $commentCount  = $event->comments->count();
    $eventImageSrc = $event->image_url ?? asset('images/gradnet-logo.png');

    $commentsData = $event->comments->map(function ($c) use ($svgFallback, $authUser) {
        return [
            'id'      => $c->id,
            'author'  => $c->user->first_name . ' ' . $c->user->last_name,
            'avatar'  => $c->user->avatar_url ?? $svgFallback,
            'text'    => $c->comment,
            'time'    => $c->created_at->diffForHumans(),
            'user_id' => $c->user_id,
            'is_own'  => $authUser->id === $c->user_id,
        ];
    })->all();
@endphp

    <div class="page-header">
        <a href="{{ route('events.index') }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <h1 class="page-title">Event</h1>
    </div>

    {{-- ── Event Details Card ───────────────────────────────────── --}}
    <div class="card">
        <div style="height:240px;overflow:hidden;">
            <img src="{{ $eventImageSrc }}"
                 alt="{{ $event->title }}"
                 style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="card-body">
            <h1 class="fw-700 text-dark mb-3" style="font-size:var(--text-2xl);">{{ $event->title }}</h1>

            <div class="d-flex flex-wrap gap-3 mb-3" style="font-size:var(--text-sm);color:var(--primary);">
                <span><i class="far fa-calendar-alt me-1"></i>{{ $event->event_datetime->format('l, F j, Y') }}</span>
                <span><i class="far fa-clock me-1"></i>{{ $event->event_datetime->format('h:i A') }}</span>
                @if ($event->location)
                    <span><i class="fas fa-map-marker-alt me-1"></i>{{ $event->location }}</span>
                @endif
            </div>

            <p class="text-body" style="font-size:var(--text-base);line-height:1.7;">{{ $event->description }}</p>

            <div class="divider"></div>

            <div class="d-flex align-items-center justify-content-between">
                <button class="post-action-btn {{ $likedClass }}"
                        id="likeBtn"
                        onclick="toggleLike()"
                        style="flex:none;padding:8px 16px;border-radius:var(--radius-full);">
                    <i class="{{ $likedIcon }} fa-heart"></i>
                    <span id="likeCount">{{ $likeCount }}</span> {{ Str::plural('Like', $likeCount) }}
                </button>
                <span class="text-muted" style="font-size:var(--text-xs);">
                    {{ $commentCount }} {{ Str::plural('comment', $commentCount) }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── RSVP Card ───────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-body" style="padding:var(--sp-4);">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <span class="fw-700" style="font-size:var(--text-sm);">
                    <i class="fas fa-calendar-check me-1 text-primary"></i> Are you going?
                </span>
                <span class="text-muted" style="font-size:var(--text-xs);">
                    <span id="goingCount">{{ $goingCount }}</span> going ·
                    <span id="maybeCount">{{ $maybeCount }}</span> maybe
                </span>
            </div>

            {{-- @if used for class names instead of {{ $var }} to avoid IDE JS parse errors --}}
            {{-- on buttons that also carry an onclick attribute.                             --}}
            <div class="d-flex gap-2 flex-wrap">
                <button id="rsvp-going" onclick="submitRsvp('going')"
                        class="btn btn-sm @if($rsvpStatus === 'going') btn-primary @else btn-surface @endif"
                        style="border-radius:var(--radius-full);padding:6px 18px;">
                    <i class="fas fa-check-circle me-1"></i>Going
                </button>
                <button id="rsvp-maybe" onclick="submitRsvp('maybe')"
                        class="btn btn-sm @if($rsvpStatus === 'maybe') btn-primary @else btn-surface @endif"
                        style="border-radius:var(--radius-full);padding:6px 18px;">
                    <i class="fas fa-question-circle me-1"></i>Maybe
                </button>
                <button id="rsvp-not_going" onclick="submitRsvp('not_going')"
                        class="btn btn-sm @if($rsvpStatus === 'not_going') btn-danger @else btn-surface @endif"
                        style="border-radius:var(--radius-full);padding:6px 18px;">
                    <i class="fas fa-times-circle me-1"></i>Not Going
                </button>
            </div>
        </div>
    </div>

    {{-- ── Comments ─────────────────────────────────────────────── --}}
    <div class="card" id="comments">
        <div class="card-header">
            <h2 class="section-title-sm"><i class="fas fa-comments me-1 text-primary"></i> Comments</h2>
        </div>

        <div class="comment-thread" id="commentList">
            {{-- $commentsData is a plain array so {{ $cd['key'] }} uses bracket access,
                 which is valid JS. No @php blocks inside the loop. --}}
            @foreach ($commentsData as $cd)
                <div class="comment-item" id="comment-{{ $cd['id'] }}">
                    <img src="{{ $cd['avatar'] }}"
                         alt="{{ $cd['author'] }}"
                         class="avatar avatar-sm"
                         onerror="this.onerror=null;this.src=this.dataset.fallback"
                         data-fallback="{{ $svgFallback }}">
                    <div class="comment-bubble">
                        <a href="{{ route('profile.show', $cd['user_id']) }}" class="comment-author">
                            {{ $cd['author'] }}
                        </a>
                        <p class="comment-text">{{ $cd['text'] }}</p>
                        <div class="comment-meta">
                            <span>{{ $cd['time'] }}</span>
                            @if ($cd['is_own'])
                                <button class="comment-del" onclick="deleteComment({{ $cd['id'] }})">Delete</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="comment-composer">
            <img src="{{ $authAvatarUrl }}"
                 alt="{{ $authFirstName }}"
                 class="avatar avatar-sm"
                 onerror="this.onerror=null;this.src=this.dataset.fallback"
                 data-fallback="{{ $svgFallback }}">
            <div class="comment-input-wrap">
                <textarea id="commentInput" class="comment-input" placeholder="Write a comment…" rows="1"></textarea>
                <button class="comment-send" onclick="submitComment()"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
const eventId = {{ $event->id }};

// ── Like ─────────────────────────────────────────────────────────
function toggleLike() {
    fetch(`/events/${eventId}/like`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const btn = document.getElementById('likeBtn');
        btn.classList.toggle('loved', data.liked);
        btn.querySelector('i').className = data.liked ? 'fas fa-heart' : 'far fa-heart';
        document.getElementById('likeCount').textContent = data.count;
    });
}

// ── RSVP ─────────────────────────────────────────────────────────
const RSVP_STATUSES = ['going', 'maybe', 'not_going'];

function submitRsvp(status) {
    fetch(`/events/${eventId}/rsvp`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ status }),
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('goingCount').textContent = data.going_count;
        document.getElementById('maybeCount').textContent = data.maybe_count;

        RSVP_STATUSES.forEach(s => {
            const btn = document.getElementById('rsvp-' + s);
            if (!btn) return;
            btn.classList.remove('btn-primary', 'btn-danger', 'btn-surface');
            btn.classList.add(
                s === data.status
                    ? (s === 'not_going' ? 'btn-danger' : 'btn-primary')
                    : 'btn-surface'
            );
        });
    });
}

// ── Comments ──────────────────────────────────────────────────────
function submitComment() {
    const input = document.getElementById('commentInput');
    const text  = input.value.trim();
    if (!text) return;

    fetch(`/events/${eventId}/comments`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ content: text }),
    })
    .then(r => r.json())
    .then(data => {
        input.value = '';
        const fallback = document.querySelector('.comment-composer img').dataset.fallback || '';
        const item = document.createElement('div');
        item.className = 'comment-item animated';
        item.id = 'comment-' + data.id;
        item.innerHTML =
            '<img src="' + data.user.avatar + '" alt="" class="avatar avatar-sm"' +
            ' onerror="this.onerror=null;this.src=this.dataset.fallback"' +
            ' data-fallback="' + fallback + '">' +
            '<div class="comment-bubble">' +
            '<a href="' + data.user.profile_url + '" class="comment-author">' + data.user.name + '</a>' +
            '<p class="comment-text">' + data.content + '</p>' +
            '<div class="comment-meta"><span>' + data.created_at + '</span>' +
            '<button class="comment-del" onclick="deleteComment(' + data.id + ')">Delete</button>' +
            '</div></div>';
        document.getElementById('commentList').appendChild(item);
    });
}

function deleteComment(id) {
    if (!confirm('Remove comment?')) return;
    fetch('/events/' + eventId + '/comments/' + id, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ _method: 'DELETE' }),
    }).then(r => { if (r.ok) { const el = document.getElementById('comment-' + id); if (el) el.remove(); } });
}

document.getElementById('commentInput').addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); submitComment(); }
});
</script>
@endpush
