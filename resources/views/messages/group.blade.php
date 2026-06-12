@extends('layouts.app')

@section('title', $group->group_name . ' — Group Chat')

@section('content')

<div class="chat-wrap">

    {{-- ── Header ─────────────────────────────────────────────── --}}
    <div class="chat-header">
        <a href="{{ route('messages.index') }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <div class="avatar-initials avatar-md flex-shrink-0" style="font-size:0.9rem;">
            <i class="fas fa-users"></i>
        </div>
        <div class="flex-grow-1 min-w-0">
            <div class="fw-700 text-dark" style="font-size:var(--text-sm);">{{ $group->group_name }}</div>
            <div class="text-muted" style="font-size:var(--text-xs);">
                {{ $group->members->count() }} {{ Str::plural('member', $group->members->count()) }}
            </div>
        </div>
        <button class="topbar-btn" onclick="openMembers()" title="View members">
            <i class="fas fa-users"></i>
        </button>
    </div>

    {{-- ── Messages ─────────────────────────────────────────────── --}}
    <div class="chat-body" id="chatBody">
        @if ($messages->isEmpty())
            <div class="empty-state" style="flex:1;">
                <i class="fas fa-comments icon"></i>
                <h3>Start the conversation!</h3>
                <p>Be the first to say something in <strong>{{ $group->group_name }}</strong>.</p>
            </div>
        @else
            @php $prevDate = null; @endphp
            @foreach ($messages as $msg)
                @php
                    $msgDate  = $msg->sent_at->format('Y-m-d');
                    $showDate = $msgDate !== $prevDate;
                    $prevDate = $msgDate;
                    $isMine   = $msg->sender_id === auth()->id();
                @endphp

                @if ($showDate)
                    <div class="text-center my-2">
                        <span class="badge badge-muted">
                            {{ $msg->sent_at->isToday() ? 'Today' : $msg->sent_at->format('M j, Y') }}
                        </span>
                    </div>
                @endif

                <div class="msg-row {{ $isMine ? 'mine' : '' }}" id="msg-{{ $msg->id }}" data-id="{{ $msg->id }}">
                    @if (!$isMine)
                        <img class="avatar avatar-sm"
                             src="{{ $msg->sender->avatar_url }}"
                             alt="{{ $msg->sender->first_name }}"
                             onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}'">
                    @endif
                    <div>
                        <div class="bubble">
                            @if (!$isMine)
                                <div style="font-size:0.68rem;font-weight:700;color:var(--accent);margin-bottom:2px;">
                                    {{ $msg->sender->first_name }}
                                </div>
                            @endif
                            {{ $msg->content }}
                        </div>
                        <div class="bubble-time {{ $isMine ? 'text-end' : '' }}">
                            {{ $msg->sent_at->format('h:i A') }}
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- ── Footer ─────────────────────────────────────────────── --}}
    <div class="chat-footer">
        <div class="comment-input-wrap flex-grow-1">
            <textarea id="msgInput" class="comment-input"
                      placeholder="Message {{ $group->group_name }}…" rows="1"></textarea>
            <button class="comment-send" id="sendBtn" onclick="sendMessage()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

</div>

{{-- ── Members Modal ────────────────────────────────────────────── --}}
<div class="modal-overlay" id="membersModal">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h5 class="modal-title">
                <i class="fas fa-users me-1"></i> Members
                <span class="badge badge-primary ms-2">{{ $group->members->count() }}</span>
            </h5>
            <button class="modal-close" onclick="closeMembers()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body" style="padding:var(--sp-3) 0;max-height:60vh;overflow-y:auto;">
            @foreach ($group->members as $member)
                <div class="d-flex align-items-center gap-3 px-4 py-2">
                    <img src="{{ $member->avatar_url }}"
                         alt="{{ $member->first_name }}"
                         class="avatar avatar-sm"
                         onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}'">
                    <span class="fw-600 text-dark" style="font-size:var(--text-sm);">
                        {{ $member->full_name }}
                    </span>
                    @if ($member->id === $group->created_by)
                        <span class="badge badge-primary ms-auto" style="font-size:0.6rem;">Admin</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
const GROUP_ID = {{ $group->id }};
const MY_ID    = {{ auth()->id() }};
const POLL_URL = `{{ route('messages.group.poll', $group->id) }}`;
const SEND_URL = `{{ route('messages.group.send', $group->id) }}`;

let lastMsgId = {{ $messages->isNotEmpty() ? $messages->last()->id : 0 }};
let pollTimer  = null;

function scrollBottom(smooth = true) {
    const body = document.getElementById('chatBody');
    body.scrollTo({ top: body.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
}
scrollBottom(false);

const msgInput = document.getElementById('msgInput');
msgInput.addEventListener('input', () => {
    msgInput.style.height = 'auto';
    msgInput.style.height = Math.min(msgInput.scrollHeight, 120) + 'px';
});
msgInput.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});

function sendMessage() {
    const text = msgInput.value.trim();
    if (!text) return;

    const sendBtn = document.getElementById('sendBtn');
    sendBtn.disabled = true;

    fetch(SEND_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ content: text }),
    })
    .then(r => r.json())
    .then(msg => {
        msgInput.value = '';
        msgInput.style.height = 'auto';
        appendMessage(msg);
        lastMsgId = msg.id;
    })
    .finally(() => { sendBtn.disabled = false; msgInput.focus(); });
}

function appendMessage(msg) {
    document.querySelector('.empty-state')?.remove();
    const body = document.getElementById('chatBody');
    const div  = document.createElement('div');
    div.className = `msg-row ${msg.is_mine ? 'mine' : ''}`;
    div.id = `msg-${msg.id}`;
    div.dataset.id = msg.id;

    const avatarHtml  = msg.is_mine ? '' : `<img class="avatar avatar-sm" src="${msg.sender.avatar}" alt="${msg.sender.name}" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}'">`;
    const senderHtml  = msg.is_mine ? '' : `<div style="font-size:0.68rem;font-weight:700;color:var(--accent);margin-bottom:2px;">${msg.sender.name}</div>`;
    const timeAlign   = msg.is_mine ? 'text-align:right' : '';

    div.innerHTML = `${avatarHtml}
        <div>
            <div class="bubble">${senderHtml}${msg.content}</div>
            <div class="bubble-time" style="${timeAlign}">${msg.sent_at}</div>
        </div>`;
    body.appendChild(div);
    scrollBottom();
}

function pollMessages() {
    fetch(`${POLL_URL}?last_message_id=${lastMsgId}`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(data => {
        data.messages.forEach(msg => {
            if (!document.getElementById(`msg-${msg.id}`)) { appendMessage(msg); lastMsgId = msg.id; }
        });
    })
    .catch(() => {});
}

pollTimer = setInterval(pollMessages, 3000);
document.addEventListener('visibilitychange', () => {
    if (document.hidden) clearInterval(pollTimer);
    else { pollMessages(); pollTimer = setInterval(pollMessages, 3000); }
});

// Members modal
function openMembers()  { document.getElementById('membersModal').classList.add('open'); }
function closeMembers() { document.getElementById('membersModal').classList.remove('open'); }
document.getElementById('membersModal').addEventListener('click', function (e) {
    if (e.target === this) closeMembers();
});
</script>
@endpush
