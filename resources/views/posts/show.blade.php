@extends('layouts.app')

@section('title', 'Post — ICCBI Alumni')

@section('content')

    <div class="page-header">
        <a href="{{ route('feed.index') }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <h1 class="page-title">Post</h1>
    </div>

    {{-- ── Post Card ────────────────────────────────────────────── --}}
    @php
        $loveCount = $post->reactions->where('reaction_type','love')->count();
        $userLoved = $post->reactions->where('reaction_type','love')->where('user_id', $authUser->id)->isNotEmpty();
    @endphp

    <div class="post-card" id="post-{{ $post->id }}">
        <div class="post-card-header">
            <a href="{{ route('profile.show', $post->user_id) }}">
                <img src="{{ $post->user->avatar_url ?? asset('images/default-avatar.svg') }}"
                     alt="{{ $post->user->first_name }}"
                     class="avatar avatar-md"
                     onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}'">
            </a>
            <div class="flex-grow-1 min-w-0">
                <a href="{{ route('profile.show', $post->user_id) }}" class="post-author-name text-decoration-none">
                    {{ $post->user->first_name }} {{ $post->user->last_name }}
                </a>
                <div class="post-author-meta">
                    {{ $post->created_at->format('M d, Y') }}
                    &bull; <i class="{{ $post->is_public ? 'fas fa-globe-asia' : 'fas fa-lock' }}" style="font-size:0.7rem;"></i>
                </div>
            </div>
        </div>

        <div class="post-card-body">
            <p style="white-space:pre-wrap;word-break:break-word;font-size:var(--text-base);line-height:1.65;">{{ $post->content }}</p>
        </div>

        @if ($post->media->isNotEmpty())
            <div class="post-media-grid count-{{ min($post->media->count(), 4) }}"
                 style="@if($post->media->count()===1) max-height:400px; @else max-height:280px; @endif">
                @foreach ($post->media->take(4) as $item)
                    <div style="overflow:hidden;">
                        @if ($item->is_video)
                            <video controls style="width:100%;height:100%;object-fit:cover;">
                                <source src="{{ $item->url }}" type="video/mp4">
                            </video>
                        @else
                            <img src="{{ $item->url }}" alt="Post image" style="width:100%;height:100%;object-fit:cover;">
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="post-stats">
            <span id="loveStats">{{ $loveCount > 0 ? "❤️ $loveCount" : '' }}</span>
            <span>{{ $post->comments->count() }} {{ Str::plural('comment', $post->comments->count()) }}</span>
        </div>

        <div class="post-actions">
            <button class="post-action-btn {{ $userLoved ? 'loved' : '' }}"
                    id="loveBtn" onclick="reactPost()">
                <i class="{{ $userLoved ? 'fas' : 'far' }} fa-heart"></i>
                <span>Like</span>
            </button>
            @if ($authUser->id === $post->user_id)
                <button class="post-action-btn text-danger" onclick="deletePost()">
                    <i class="fas fa-trash"></i> <span>Delete</span>
                </button>
            @endif
        </div>
    </div>

    {{-- ── Comments ─────────────────────────────────────────────── --}}
    <div class="card" id="comments">
        <div class="card-header">
            <h2 class="section-title-sm"><i class="fas fa-comments me-1 text-primary"></i> Comments</h2>
        </div>

        <div class="comment-thread" id="commentList">
            @foreach ($post->comments as $comment)
                <div class="comment-item" id="comment-{{ $comment->id }}">
                    <img src="{{ $comment->user->avatar_url ?? asset('images/default-avatar.svg') }}"
                         alt="{{ $comment->user->first_name }}"
                         class="avatar avatar-sm"
                         onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}'">
                    <div class="comment-bubble">
                        <a href="{{ route('profile.show', $comment->user_id) }}" class="comment-author">
                            {{ $comment->user->first_name }} {{ $comment->user->last_name }}
                        </a>
                        <p class="comment-text">{{ $comment->content }}</p>
                        <div class="comment-meta">
                            <span>{{ $comment->created_at->diffForHumans() }}</span>
                            @if ($authUser->id === $comment->user_id || $authUser->id === $post->user_id)
                                <button class="comment-del" onclick="deleteComment({{ $comment->id }})">Delete</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="comment-composer">
            <img src="{{ $authUser->avatar_url }}"
                 alt="{{ $authUser->first_name }}"
                 class="avatar avatar-sm"
                 onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}'">
            <div class="comment-input-wrap">
                <textarea id="commentInput" class="comment-input" placeholder="Write a comment…" rows="1"></textarea>
                <button class="comment-send" onclick="submitComment()"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const postId = {{ $post->id }};

function reactPost() {
    fetch(`/posts/${postId}/react`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        const btn = document.getElementById('loveBtn');
        btn.classList.toggle('loved', data.reacted);
        btn.querySelector('i').className = data.reacted ? 'fas fa-heart' : 'far fa-heart';
        document.getElementById('loveStats').textContent = data.count > 0 ? `❤️ ${data.count}` : '';
    });
}

function deletePost() {
    if (!confirm('Delete this post?')) return;
    fetch(`/posts/${postId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ _method: 'DELETE' }),
    }).then(r => { if (r.ok) window.location.href = '{{ route('feed.index') }}'; });
}

function submitComment() {
    const input   = document.getElementById('commentInput');
    const content = input.value.trim();
    if (!content) return;

    fetch(`/posts/${postId}/comments`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ content }),
    })
    .then(r => r.json())
    .then(data => {
        input.value = '';
        const item = document.createElement('div');
        item.className = 'comment-item animated';
        item.id = `comment-${data.id}`;
        item.innerHTML = `
            <img src="${data.user.avatar}" alt="" class="avatar avatar-sm">
            <div class="comment-bubble">
                <a href="${data.user.profile_url}" class="comment-author">${data.user.name}</a>
                <p class="comment-text">${data.content.replace(/\n/g,'<br>')}</p>
                <div class="comment-meta">
                    <span>${data.created_at}</span>
                    <button class="comment-del" onclick="deleteComment(${data.id})">Delete</button>
                </div>
            </div>`;
        document.getElementById('commentList').appendChild(item);
    });
}

function deleteComment(commentId) {
    if (!confirm('Remove this comment?')) return;
    fetch(`/posts/${postId}/comments/${commentId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ _method: 'DELETE' }),
    }).then(r => { if (r.ok) document.getElementById(`comment-${commentId}`)?.remove(); });
}

const ci = document.getElementById('commentInput');
ci.addEventListener('input',   function () { this.style.height = 'auto'; this.style.height = Math.min(this.scrollHeight, 120) + 'px'; });
ci.addEventListener('keydown', function (e) { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); submitComment(); } });
</script>
@endpush
