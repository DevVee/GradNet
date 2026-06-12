@extends('layouts.app')

@section('title', 'Messages — ICCBI Alumni')

@section('content')
@php
    $svgFallback = asset('images/default-avatar.svg');
    $totalUnread = 0; // placeholder — extend later with unread counts per convo
@endphp

<div class="msg-inbox">

    {{-- ── Header ──────────────────────────────────────────────────── --}}
    <div class="msg-inbox-header">
        <div class="msg-inbox-title">
            <i class="fas fa-message"></i>
            Messages
            @if($totalUnread > 0)
                <span class="badge badge-primary ms-1" style="font-size:0.6rem;">{{ $totalUnread }}</span>
            @endif
        </div>
        <button class="msg-compose-fab" onclick="openNewMsg()" title="New Message" aria-label="Compose">
            <i class="fas fa-pen-to-square"></i>
        </button>
    </div>

    {{-- ── Group Chats ──────────────────────────────────────────────── --}}
    @if ($groups->isNotEmpty())
        <div class="msg-section-label">
            <i class="fas fa-users me-1"></i> Group Chats
        </div>
        @foreach ($groups as $group)
            @php
                $gTime = $group->latestMessage?->sent_at?->diffForHumans(null, true);
                $gPreview = $group->latestMessage?->content ?? 'No messages yet';
            @endphp
            <a href="{{ route('messages.group.show', $group->id) }}" class="msg-thread">
                <div class="msg-thread-avatar">
                    <div class="avatar avatar-md d-flex align-items-center justify-content-center"
                         style="background:var(--gradient-primary);color:#fff;font-size:1rem;flex-shrink:0;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="msg-thread-body">
                    <div class="msg-thread-name">
                        {{ $group->group_name }}
                        <span class="badge badge-primary ms-1" style="font-size:0.55rem;vertical-align:middle;">Group</span>
                    </div>
                    <div class="msg-thread-preview">{{ Str::limit($gPreview, 55) }}</div>
                </div>
                @if($gTime)
                <div class="msg-thread-meta">
                    <span class="msg-thread-time">{{ $gTime }}</span>
                </div>
                @endif
            </a>
        @endforeach
    @endif

    {{-- ── Direct Messages ─────────────────────────────────────────── --}}
    @if ($conversations->isNotEmpty())
        <div class="msg-section-label">
            <i class="fas fa-comment-dots me-1"></i> Direct Messages
        </div>

        @foreach ($conversations as $convo)
            @php
                $other     = $convo->otherUser($user->id);
                $preview   = $convo->latestMessage?->content ?? 'No messages yet';
                $msgTime   = $convo->latestMessage?->sent_at;
                $timeLabel = $msgTime
                    ? ($msgTime->isToday()
                        ? $msgTime->format('g:i A')
                        : ($msgTime->isYesterday() ? 'Yesterday' : $msgTime->format('M j')))
                    : '';
                $oAvatar   = $other->profile_picture
                    ? asset('storage/' . $other->profile_picture)
                    : $svgFallback;
            @endphp
            <a href="{{ route('messages.show', $convo->id) }}" class="msg-thread">
                <div class="msg-thread-avatar">
                    <img src="{{ $oAvatar }}"
                         alt="{{ $other->first_name }}"
                         class="avatar avatar-md"
                         onerror="this.onerror=null;this.src=this.dataset.fallback"
                         data-fallback="{{ $svgFallback }}">
                    {{-- Online dot placeholder — extend later with presence --}}
                    {{-- <div class="msg-online-dot"></div> --}}
                </div>
                <div class="msg-thread-body">
                    <div class="msg-thread-name">{{ $other->full_name }}</div>
                    <div class="msg-thread-preview">{{ Str::limit($preview, 55) }}</div>
                </div>
                @if($timeLabel)
                <div class="msg-thread-meta">
                    <span class="msg-thread-time">{{ $timeLabel }}</span>
                </div>
                @endif
            </a>
        @endforeach

    @elseif ($groups->isEmpty())
        {{-- Empty state --}}
        <div class="msg-empty">
            <div class="icon">💬</div>
            <h3>Your inbox is empty</h3>
            <p>Start a conversation with a fellow ICCBI alumnus.</p>
            <button class="btn btn-primary btn-sm mt-4" onclick="openNewMsg()">
                <i class="fas fa-pen me-1"></i> New Message
            </button>
        </div>
    @endif

</div>

{{-- ── New Message Modal ─────────────────────────────────────────── --}}
<div class="modal-overlay" id="newMsgModal">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h5 class="modal-title">New Message</h5>
            <button class="modal-close" onclick="closeNewMsg()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="input-wrap mb-3">
                <i class="fas fa-search input-icon"></i>
                <input type="text" id="searchUser"
                       class="form-control"
                       placeholder="Search alumni by name…"
                       oninput="searchUsers(this.value)"
                       autocomplete="off">
            </div>
            <div id="searchResults"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function openNewMsg()  { document.getElementById('newMsgModal').classList.add('open'); setTimeout(() => document.getElementById('searchUser').focus(), 100); }
function closeNewMsg() { document.getElementById('newMsgModal').classList.remove('open'); }

document.getElementById('newMsgModal').addEventListener('click', function (e) {
    if (e.target === this) closeNewMsg();
});

function searchUsers(q) {
    const box = document.getElementById('searchResults');
    if (q.length < 2) { box.innerHTML = ''; return; }

    fetch(`/messages/search?q=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(users => {
            if (!users.length) {
                box.innerHTML = '<p class="text-muted text-center" style="font-size:var(--text-sm);padding:var(--sp-4);">No results found.</p>';
                return;
            }
            const svgFallback = '{{ asset("images/default-avatar.svg") }}';
            box.innerHTML = users.map(u => `
                <div class="msg-thread" style="border-radius:var(--radius-sm);cursor:pointer;"
                     onclick="startConversation(${u.id})">
                    <div class="msg-thread-avatar">
                        <img src="${u.avatar}" alt="${u.name}"
                             class="avatar avatar-md"
                             onerror="this.onerror=null;this.src='${svgFallback}'">
                    </div>
                    <div class="msg-thread-body">
                        <div class="msg-thread-name">${u.name}</div>
                        <div class="msg-thread-preview">${u.program ?? 'ICCBI Alumni'}</div>
                    </div>
                    <i class="fas fa-chevron-right text-muted" style="font-size:0.7rem;"></i>
                </div>`).join('');
        });
}

function startConversation(userId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("messages.new") }}';
    form.innerHTML = `<input type="hidden" name="_token" value="${CSRF}"><input type="hidden" name="user_id" value="${userId}">`;
    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush
