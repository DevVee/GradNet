@extends('layouts.app')

@section('title', $other->full_name . ' — Messages')

@section('content')

<div class="chat-wrap">

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="chat-header">
        <a href="{{ route('messages.index') }}" class="back-btn" title="Back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <img src="{{ $other->avatar_url }}"
             alt="{{ $other->first_name }}"
             class="avatar avatar-md"
             onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}'">
        <div class="flex-grow-1 min-w-0">
            <div class="fw-700 text-dark" style="font-size:var(--text-sm);">{{ $other->full_name }}</div>
            <div class="text-muted" style="font-size:var(--text-xs);">{{ $other->program ?? 'ICCBI Alumni' }}</div>
        </div>
        <div class="d-flex gap-1 ms-auto">
            <a href="{{ route('profile.show', $other->id) }}" class="topbar-btn" title="View profile">
                <i class="fas fa-user"></i>
            </a>
            <button class="topbar-btn" onclick="confirmDeleteConvo()" title="Delete conversation">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </div>

    {{-- ── Attachment preview row ─────────────────────────────── --}}
    <div id="attachPreview" style="display:flex;gap:6px;flex-wrap:wrap;padding:0 var(--sp-4) 0;"></div>

    {{-- ── Messages ─────────────────────────────────────────────── --}}
    <div class="chat-body" id="chatBody">
        @if ($messages->isEmpty())
            <div class="empty-state" style="flex:1;">
                <i class="fas fa-comment-dots icon"></i>
                <h3>Say hello to {{ $other->first_name }}!</h3>
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
                            @foreach ($msg->attachments as $att)
                                @if ($att->file_type === 'video')
                                    <video style="max-width:220px;border-radius:var(--radius-sm);display:block;margin-bottom:4px;" controls>
                                        <source src="{{ $att->url }}" type="video/mp4">
                                    </video>
                                @else
                                    <img src="{{ $att->url }}"
                                         onclick="openImg('{{ $att->url }}')"
                                         alt="{{ $att->file_name }}"
                                         style="max-width:220px;border-radius:var(--radius-sm);display:block;margin-bottom:4px;cursor:pointer;">
                                @endif
                            @endforeach
                            @if ($msg->content){{ $msg->content }}@endif
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
        <button class="topbar-btn" onclick="document.getElementById('fileInput').click()" title="Attach file" style="flex-shrink:0;">
            <i class="fas fa-paperclip"></i>
        </button>
        <input type="file" id="fileInput" accept="image/*,video/*" style="display:none" onchange="previewFile(this)">
        <div class="comment-input-wrap flex-grow-1">
            <textarea id="msgInput" class="comment-input" placeholder="Type a message…" rows="1"></textarea>
            <button class="comment-send" id="sendBtn" onclick="sendMessage()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

</div>

{{-- ── Delete confirmation modal ──────────────────────────────── --}}
<div class="modal-overlay" id="deleteModal">
    <div class="modal-box modal-sm" style="text-align:center;">
        <div class="modal-body">
            <i class="fas fa-trash-alt text-danger" style="font-size:2rem;display:block;margin-bottom:var(--sp-3);"></i>
            <h3 style="font-size:var(--text-md);margin-bottom:var(--sp-2);">Delete conversation?</h3>
            <p class="text-muted" style="font-size:var(--text-sm);margin-bottom:var(--sp-4);">
                This removes the conversation from your view only.
            </p>
            <form method="POST" action="{{ route('messages.delete', $conversation->id) }}" class="d-flex gap-2 justify-content-center">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-surface btn-sm" onclick="document.getElementById('deleteModal').classList.remove('open')">Cancel</button>
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
        </div>
    </div>
</div>

{{-- ── Image lightbox ──────────────────────────────────────────── --}}
<div id="imgModal" onclick="this.classList.remove('open')"
     class="modal-overlay" style="background:rgba(0,0,0,0.88);">
    <img id="lightboxImg" src="" alt="attachment"
         style="max-width:90vw;max-height:90vh;border-radius:var(--radius);object-fit:contain;">
</div>

@endsection

@push('scripts')
<script>
const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
const CONVO_ID = {{ $conversation->id }};
const MY_ID    = {{ auth()->id() }};
const POLL_URL = `{{ route('messages.poll', $conversation->id) }}`;
const SEND_URL = `{{ route('messages.send', $conversation->id) }}`;

let lastMsgId  = {{ $messages->isNotEmpty() ? $messages->last()->id : 0 }};
let pollTimer  = null;
let pendingFile = null;

// ── Auto-scroll ──────────────────────────────────────────────────────
function scrollBottom(smooth = true) {
    const body = document.getElementById('chatBody');
    body.scrollTo({ top: body.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
}
scrollBottom(false);

// ── Resize textarea ──────────────────────────────────────────────────
const msgInput = document.getElementById('msgInput');
msgInput.addEventListener('input', () => {
    msgInput.style.height = 'auto';
    msgInput.style.height = Math.min(msgInput.scrollHeight, 120) + 'px';
});
msgInput.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});

// ── Send message ─────────────────────────────────────────────────────
function sendMessage() {
    const text = msgInput.value.trim();
    if (!text && !pendingFile) return;

    const sendBtn = document.getElementById('sendBtn');
    sendBtn.disabled = true;

    const formData = new FormData();
    formData.append('content', text);
    if (pendingFile) formData.append('attachment', pendingFile);

    fetch(SEND_URL, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: formData,
    })
    .then(r => r.json())
    .then(msg => {
        msgInput.value = '';
        msgInput.style.height = 'auto';
        pendingFile = null;
        document.getElementById('attachPreview').innerHTML = '';
        appendMessage(msg);
        lastMsgId = msg.id;
    })
    .finally(() => { sendBtn.disabled = false; msgInput.focus(); });
}

// ── Append a bubble ──────────────────────────────────────────────────
function appendMessage(msg) {
    const empty = document.querySelector('.empty-state');
    if (empty) empty.remove();

    const body = document.getElementById('chatBody');
    const div  = document.createElement('div');
    div.className = `msg-row ${msg.is_mine ? 'mine' : ''}`;
    div.id = `msg-${msg.id}`;
    div.dataset.id = msg.id;

    const avatarHtml = msg.is_mine ? '' :
        `<img class="avatar avatar-sm" src="${msg.sender.avatar}" alt="${msg.sender.name}" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}'">`;

    const attachHtml = (msg.attachments || []).map(a => {
        if (a.file_type === 'video') {
            return `<video style="max-width:220px;border-radius:var(--radius-sm);display:block;margin-bottom:4px;" controls><source src="${a.url}" type="video/mp4"></video>`;
        }
        return `<img src="${a.url}" onclick="openImg('${a.url}')" alt="${a.file_name}" style="max-width:220px;border-radius:var(--radius-sm);display:block;margin-bottom:4px;cursor:pointer;">`;
    }).join('');

    div.innerHTML = `${avatarHtml}
        <div>
            <div class="bubble">
                ${attachHtml}
                ${msg.content ? msg.content : ''}
            </div>
            <div class="bubble-time ${msg.is_mine ? 'text-end' : ''}">${msg.sent_at}</div>
        </div>`;
    body.appendChild(div);
    scrollBottom();
}

// ── AJAX Poll ────────────────────────────────────────────────────────
function pollMessages() {
    fetch(`${POLL_URL}?last_message_id=${lastMsgId}`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(data => {
        data.messages.forEach(msg => {
            if (!document.getElementById(`msg-${msg.id}`)) {
                appendMessage(msg);
                lastMsgId = msg.id;
            }
        });
    })
    .catch(() => {});
}

pollTimer = setInterval(pollMessages, 3000);
document.addEventListener('visibilitychange', () => {
    if (document.hidden) clearInterval(pollTimer);
    else { pollMessages(); pollTimer = setInterval(pollMessages, 3000); }
});

// ── File attachment ──────────────────────────────────────────────────
function previewFile(input) {
    const file = input.files[0];
    if (!file) return;
    pendingFile = file;
    const url = URL.createObjectURL(file);
    const preview = document.getElementById('attachPreview');
    preview.style.paddingBottom = 'var(--sp-2)';
    preview.innerHTML = `
        <div style="position:relative;">
            <img src="${file.type.startsWith('video') ? '{{ asset("images/ICCLOGO.png") }}' : url}"
                 style="width:52px;height:52px;object-fit:cover;border-radius:var(--radius-sm);border:1px solid var(--border);">
            <button onclick="clearFile()"
                    style="position:absolute;top:-4px;right:-4px;background:var(--danger);color:#fff;border:none;border-radius:50%;width:16px;height:16px;font-size:0.6rem;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                ×
            </button>
        </div>`;
}

function clearFile() {
    pendingFile = null;
    const preview = document.getElementById('attachPreview');
    preview.innerHTML = '';
    preview.style.paddingBottom = '0';
    document.getElementById('fileInput').value = '';
}

// ── Lightbox ─────────────────────────────────────────────────────────
function openImg(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('imgModal').classList.add('open');
}

// ── Delete convo ─────────────────────────────────────────────────────
function confirmDeleteConvo() {
    document.getElementById('deleteModal').classList.add('open');
}
document.getElementById('deleteModal').addEventListener('click', function (e) {
    if (e.target === this) this.classList.remove('open');
});
</script>
@endpush
