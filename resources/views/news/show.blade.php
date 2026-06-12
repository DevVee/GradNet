@extends('layouts.app')

@section('title', $news->title . ' — GradNet')

@section('content')

    <div class="page-header">
        <a href="{{ route('news.index') }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <h1 class="page-title">News</h1>
    </div>

    <div class="card">
        @if ($news->image_path)
            <div style="max-height:280px;overflow:hidden;">
                <img src="{{ $news->image_url }}"
                     alt="{{ $news->title }}"
                     style="width:100%;height:280px;object-fit:cover;">
            </div>
        @endif
        <div class="card-body">
            <h1 class="fw-700 text-dark mb-2" style="font-size:var(--text-2xl);">{{ $news->title }}</h1>
            <div class="text-muted mb-4" style="font-size:var(--text-xs);">
                <i class="far fa-calendar-alt me-1"></i>{{ $news->created_at->format('F j, Y') }}
            </div>
            <div style="font-size:var(--text-base);line-height:1.75;color:var(--text-body);">
                {!! nl2br(e($news->description)) !!}
            </div>

            <div class="divider"></div>

            <div class="d-flex align-items-center justify-content-between">
                <button class="post-action-btn {{ $userLiked ? 'loved' : '' }}"
                        id="likeBtn"
                        onclick="toggleLike()"
                        style="flex:none;padding:8px 16px;border-radius:var(--radius-full);">
                    <i class="{{ $userLiked ? 'fas' : 'far' }} fa-heart"></i>
                    <span id="likeCount">{{ $likeCount }}</span> {{ Str::plural('Like', $likeCount) }}
                </button>
                <span class="text-muted" style="font-size:var(--text-xs);">
                    {{ $news->comments->count() }} {{ Str::plural('comment', $news->comments->count()) }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── Comments ─────────────────────────────────────────────── --}}
    <div class="card" id="comments">
        <div class="card-header">
            <h2 class="section-title-sm"><i class="fas fa-comments me-1 text-primary"></i> Comments</h2>
        </div>

        <div class="comment-thread" id="commentList">
            @foreach ($news->comments as $comment)
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
                            @if ($authUser->id === $comment->user_id)
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
const newsId = {{ $news->id }};

function toggleLike() {
    fetch(`/news/${newsId}/like`, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            const btn = document.getElementById('likeBtn');
            btn.classList.toggle('loved', data.liked);
            btn.querySelector('i').className = data.liked ? 'fas fa-heart' : 'far fa-heart';
            document.getElementById('likeCount').textContent = data.count;
        });
}

function submitComment() {
    const input = document.getElementById('commentInput');
    const text  = input.value.trim();
    if (!text) return;
    fetch(`/news/${newsId}/comments`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ content: text }),
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
                <p class="comment-text">${data.content}</p>
                <div class="comment-meta">
                    <span>${data.created_at}</span>
                    <button class="comment-del" onclick="deleteComment(${data.id})">Delete</button>
                </div>
            </div>`;
        document.getElementById('commentList').appendChild(item);
    });
}

function deleteComment(id) {
    if (!confirm('Remove comment?')) return;
    fetch(`/news/${newsId}/comments/${id}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ _method: 'DELETE' }),
    }).then(r => { if (r.ok) document.getElementById(`comment-${id}`)?.remove(); });
}

document.getElementById('commentInput').addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); submitComment(); }
});
</script>
@endpush
