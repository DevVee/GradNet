@extends('layouts.app')

@section('title', 'Feed — ICCBI Alumni')

@section('content')

    {{-- ── Stories Bar ──────────────────────────────────────────────── --}}
    @php
        $svgDefault = asset('images/default-avatar.svg');
        $myAvatar   = $user->profile_picture
            ? asset('storage/' . $user->profile_picture)
            : $svgDefault;
    @endphp
    <div class="stories-bar">
        <div class="stories-scroll">

            {{-- "Your story" / Add post shortcut --}}
            <a href="javascript:void(0)"
               onclick="expandComposer(); document.getElementById('postContent').focus();"
               class="story-item story-item-you"
               title="Share something">
                <div class="story-avatar-wrap">
                    <div class="story-avatar-ring">
                        <img src="{{ $myAvatar }}"
                             alt="You"
                             onerror="this.onerror=null;this.src=this.dataset.fallback"
                             data-fallback="{{ $svgDefault }}">
                    </div>
                    <div class="story-add-icon"><i class="fas fa-plus"></i></div>
                </div>
                <span class="story-name">You</span>
            </a>

            {{-- Connections --}}
            @forelse($storyUsers as $su)
            @php
                $suAvatar = $su->profile_picture
                    ? asset('storage/' . $su->profile_picture)
                    : $svgDefault;
            @endphp
            <a href="{{ route('profile.show', $su->id) }}"
               class="story-item"
               title="{{ $su->first_name }} {{ $su->last_name }}">
                <div class="story-avatar-wrap">
                    <div class="story-avatar-ring">
                        <img src="{{ $suAvatar }}"
                             alt="{{ $su->first_name }}"
                             onerror="this.onerror=null;this.src=this.dataset.fallback"
                             data-fallback="{{ $svgDefault }}">
                    </div>
                </div>
                <span class="story-name">{{ $su->first_name }}</span>
            </a>
            @empty
            {{-- No connections yet — show placeholder alumni to browse --}}
            <a href="{{ route('connections.index') }}" class="story-item" title="Find alumni to connect with">
                <div class="story-avatar-wrap">
                    <div class="story-avatar-ring" style="background:var(--surface-2);border:2px dashed var(--border);">
                        <img src="{{ $svgDefault }}" alt="Alumni">
                    </div>
                </div>
                <span class="story-name" style="color:var(--primary);">Find Alumni</span>
            </a>
            @endforelse

        </div>
    </div>

    {{-- ── Post Composer ─────────────────────────────────────────── --}}
    <div class="post-composer">
        <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" id="postForm">
            @csrf
            <div class="post-composer-row">
                <img src="{{ $user->avatar_url }}"
                     alt="{{ $user->first_name }}"
                     class="avatar avatar-md"
                     onerror="this.onerror=null;this.src=this.dataset.fallback"
                     data-fallback="{{ asset('images/default-avatar.svg') }}">
                <div class="post-composer-input"
                     id="composerTrigger"
                     onclick="document.getElementById('postContent').focus()">
                    What's on your mind, {{ $user->first_name }}?
                </div>
            </div>

            {{-- Full composer (shows on focus / when textarea has content) --}}
            <div id="composerExpanded" style="display:none; margin-top:var(--sp-3);">
                <textarea name="content" id="postContent"
                          class="form-control mb-3"
                          placeholder="What's on your mind, {{ $user->first_name }}?"
                          rows="3"
                          style="border-radius:var(--radius-sm);"></textarea>
                <div id="mediaPreviewsCreate" class="d-flex flex-wrap gap-2 mb-2"></div>

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <label class="btn btn-ghost btn-sm gap-2" for="mediaUpload" style="cursor:pointer;">
                            <i class="fas fa-image" style="color:var(--success);"></i>
                            <span>Photo/Video</span>
                        </label>
                        <input type="file" id="mediaUpload" name="media[]"
                               accept="image/*,video/mp4,video/quicktime" multiple style="display:none;">
                        <select name="is_public" class="form-control" style="width:auto;padding:5px 10px;font-size:var(--text-xs);">
                            <option value="1">🌐 Public</option>
                            <option value="0">🔒 Only Me</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-ghost btn-sm" id="cancelComposer">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-paper-plane me-1"></i> Post
                        </button>
                    </div>
                </div>
            </div>

            <div class="post-composer-actions" id="composerActions">
                <button type="button" class="composer-action-btn" onclick="expandComposer(); document.getElementById('mediaUpload').click()">
                    <i class="fas fa-image" style="color:var(--success);"></i> Photo/Video
                </button>
                <button type="button" class="composer-action-btn" onclick="expandComposer()">
                    <i class="fas fa-pen" style="color:var(--primary);"></i> Write
                </button>
            </div>
        </form>
    </div>

    {{-- ── Posts ─────────────────────────────────────────────────── --}}
    @if ($posts->isEmpty())
        <div class="card" style="text-align:center;padding:var(--sp-10) var(--sp-6);">
            <div style="font-size:3rem;margin-bottom:var(--sp-4);">✍️</div>
            <h3 style="font-size:var(--text-xl);font-weight:700;color:var(--text-dark);margin-bottom:var(--sp-2);">
                Your feed is quiet right now
            </h3>
            <p style="font-size:var(--text-sm);color:var(--text-muted);max-width:320px;margin:0 auto var(--sp-5);line-height:1.7;">
                Be the first to share something! Connect with fellow alumni, post an update, or share a milestone.
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <button class="btn btn-primary btn-sm" onclick="expandComposer(); document.getElementById('postContent').focus(); window.scrollTo({top:0,behavior:'smooth'})">
                    <i class="fas fa-pen me-1"></i> Write a Post
                </button>
                <a href="{{ route('connections.index') }}" class="btn btn-surface btn-sm">
                    <i class="fas fa-user-group me-1"></i> Find Alumni
                </a>
            </div>
        </div>
    @else
        @foreach ($posts as $post)
            @include('feed._post-card', ['post' => $post, 'authUser' => $user])
        @endforeach

        <div class="d-flex justify-content-center mt-3">
            {{ $posts->links('pagination::bootstrap-5') }}
        </div>
    @endif

    {{-- ── Edit Post Modal ─────────────────────────────────────── --}}
    <div class="modal-overlay" id="editModal">
        <div class="modal-box">
            <div class="modal-header">
                <h5 class="modal-title">Edit Post</h5>
                <button class="modal-close" onclick="closeEditModal()" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <input type="hidden" id="editPostId">
                    <textarea id="editContent" name="content" class="form-control mb-3" rows="4" required></textarea>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <label class="btn btn-ghost btn-sm" for="editMediaUpload" style="cursor:pointer;">
                            <i class="fas fa-image me-1" style="color:var(--success);"></i> Add Media
                        </label>
                        <input type="file" id="editMediaUpload" name="media[]" accept="image/*,video/mp4" multiple style="display:none;">
                        <select name="is_public" id="editIsPublic" class="form-control" style="width:auto;padding:5px 10px;font-size:var(--text-xs);">
                            <option value="1">🌐 Public</option>
                            <option value="0">🔒 Only Me</option>
                        </select>
                    </div>
                    <div id="mediaPreviewsEdit" class="d-flex flex-wrap gap-2 mt-2"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-surface btn-sm" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Media Preview Modal ──────────────────────────────────── --}}
    <div class="modal-overlay" id="mediaPreviewModal" style="background:rgba(0,0,0,0.85);">
        <div style="max-width:800px;width:100%;position:relative;">
            <button onclick="closeMediaModal()"
                    style="position:absolute;top:-36px;right:0;background:none;border:none;color:#fff;font-size:1.5rem;cursor:pointer;">
                <i class="fas fa-times"></i>
            </button>
            <div id="mediaCarousel" class="carousel slide">
                <div class="carousel-inner" id="carouselInner"></div>
                <button class="carousel-control-prev" type="button" data-bs-target="#mediaCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#mediaCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
    </div>

@endsection

{{-- ── Right sidebar ─────────────────────────────────────────────── --}}
@section('right-sidebar')

@php
    $svgFallback = asset('images/default-avatar.svg');
@endphp

{{-- People You May Know --}}
<div class="widget-card">
    <div class="widget-header">
        <span class="widget-title">People You May Know</span>
        <a href="{{ route('connections.index') }}" class="widget-link">See all</a>
    </div>

    @forelse($suggestedAlumni as $alumni)
    @php
        $aAvatar = $alumni->profile_picture
            ? asset('storage/' . $alumni->profile_picture)
            : $svgFallback;
    @endphp
    <div class="suggested-person">
        <a href="{{ route('profile.show', $alumni->id) }}" class="suggested-avatar-link">
            <img src="{{ $aAvatar }}"
                 alt="{{ $alumni->first_name }}"
                 class="avatar avatar-sm"
                 onerror="this.onerror=null;this.src=this.dataset.fallback"
                 data-fallback="{{ $svgFallback }}">
        </a>
        <div class="suggested-info">
            <a href="{{ route('profile.show', $alumni->id) }}" class="suggested-name">
                {{ $alumni->first_name }} {{ $alumni->last_name }}
            </a>
            <div class="suggested-meta">
                {{ $alumni->program ?? 'ICCBI Alumni' }}@if($alumni->graduation_year) · {{ $alumni->graduation_year }}@endif
            </div>
        </div>
        <form method="POST" action="{{ route('connections.store') }}" class="ms-auto">
            @csrf
            <input type="hidden" name="recipient_id" value="{{ $alumni->id }}">
            <button type="submit" class="btn btn-surface btn-xs" title="Connect">
                <i class="fas fa-user-plus"></i>
            </button>
        </form>
    </div>
    @empty
    <p class="text-muted" style="font-size:var(--text-xs);padding:var(--sp-3) 0;">
        No suggestions right now. <a href="{{ route('connections.index') }}">Browse alumni</a>
    </p>
    @endforelse
</div>

{{-- Upcoming Events --}}
@if($upcomingEvents->isNotEmpty())
<div class="widget-card">
    <div class="widget-header">
        <span class="widget-title">Upcoming Events</span>
        <a href="{{ route('events.index') }}" class="widget-link">See all</a>
    </div>

    @foreach($upcomingEvents as $ev)
    @php
        $evMonth = $ev->event_datetime->format('M');
        $evDay   = $ev->event_datetime->format('j');
    @endphp
    <a href="{{ route('events.show', $ev->id) }}" class="widget-event-row">
        <div class="widget-event-date">
            <div class="widget-event-month">{{ $evMonth }}</div>
            <div class="widget-event-day">{{ $evDay }}</div>
        </div>
        <div class="widget-event-info">
            <div class="widget-event-title">{{ Str::limit($ev->title, 38) }}</div>
            @if($ev->location)
            <div class="widget-event-loc">
                <i class="fas fa-location-dot"></i> {{ Str::limit($ev->location, 28) }}
            </div>
            @endif
        </div>
    </a>
    @endforeach
</div>
@endif

{{-- Quick Links --}}
<div class="widget-card">
    <div class="widget-header">
        <span class="widget-title">Explore</span>
    </div>
    <div class="widget-explore-grid">
        <a href="{{ route('connections.index') }}" class="widget-explore-item">
            <i class="fas fa-user-group"></i>
            Alumni
        </a>
        <a href="{{ route('events.index') }}" class="widget-explore-item">
            <i class="fas fa-calendar-days"></i>
            Events
        </a>
        <a href="{{ route('news.index') }}" class="widget-explore-item">
            <i class="fas fa-newspaper"></i>
            News
        </a>
        <a href="{{ route('groups.index') }}" class="widget-explore-item">
            <i class="fas fa-people-group"></i>
            Groups
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── Composer expand/collapse ─────────────────────────────────────────
function expandComposer() {
    document.getElementById('composerTrigger').style.display  = 'none';
    document.getElementById('composerExpanded').style.display = 'block';
    document.getElementById('composerActions').style.display  = 'none';
    document.getElementById('postContent').focus();
}
function collapseComposer() {
    document.getElementById('composerTrigger').style.display  = '';
    document.getElementById('composerExpanded').style.display = 'none';
    document.getElementById('composerActions').style.display  = '';
    document.getElementById('postContent').value = '';
    document.getElementById('mediaPreviewsCreate').innerHTML  = '';
}

document.getElementById('composerTrigger').addEventListener('click', expandComposer);
document.getElementById('cancelComposer')?.addEventListener('click', collapseComposer);

// ── Media file preview helper ────────────────────────────────────────
function setupMediaPreview(inputId, previewId) {
    const input   = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    let files = [];

    input.addEventListener('change', function () {
        const newFiles = Array.from(this.files);
        if (files.length + newFiles.length > 20) { showToast('Max 20 files allowed.', 'warning'); return; }
        files = [...files, ...newFiles];
        render(); sync();
    });

    function render() {
        preview.innerHTML = '';
        files.forEach((f, i) => {
            const reader = new FileReader();
            reader.onload = e => {
                const wrap = document.createElement('div');
                wrap.style.cssText = 'position:relative;width:60px;height:60px;border-radius:var(--radius-sm);overflow:hidden;';
                wrap.innerHTML = f.type.startsWith('image/')
                    ? `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`
                    : `<video src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;" muted></video>`;
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.style.cssText = 'position:absolute;top:-2px;right:-2px;width:18px;height:18px;background:var(--danger);color:#fff;border:none;border-radius:50%;font-size:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;';
                btn.innerHTML = '&times;';
                btn.addEventListener('click', () => { files.splice(i, 1); render(); sync(); });
                wrap.appendChild(btn);
                preview.appendChild(wrap);
            };
            reader.readAsDataURL(f);
        });
    }

    function sync() {
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        input.files = dt.files;
    }
}

setupMediaPreview('mediaUpload', 'mediaPreviewsCreate');
setupMediaPreview('editMediaUpload', 'mediaPreviewsEdit');

// ── Edit modal ───────────────────────────────────────────────────────
function openEditModal(postId, content, isPublic) {
    document.getElementById('editPostId').value = postId;
    document.getElementById('editContent').value = content;
    document.getElementById('editIsPublic').value = isPublic ? '1' : '0';
    document.getElementById('editForm').action = `/posts/${postId}`;
    document.getElementById('mediaPreviewsEdit').innerHTML = '';
    document.getElementById('editModal').classList.add('open');
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}
document.getElementById('editModal').addEventListener('click', function (e) {
    if (e.target === this) closeEditModal();
});

// ── Delete post ──────────────────────────────────────────────────────
function deletePost(postId) {
    if (!confirm('Delete this post? This cannot be undone.')) return;
    fetch(`/posts/${postId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ _method: 'DELETE' }),
    }).then(r => {
        if (r.ok) {
            document.getElementById(`post-${postId}`)?.remove();
            showToast('Post deleted.', 'success');
        }
    });
}

// ── React (love) ─────────────────────────────────────────────────────
function reactPost(postId, btn) {
    fetch(`/posts/${postId}/react`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        btn.classList.toggle('loved', data.reacted);
        const icon = btn.querySelector('i');
        icon.className = data.reacted ? 'fas fa-heart' : 'far fa-heart';
        const statEl = document.querySelector(`.reactions-stat[data-post-id="${postId}"]`);
        if (statEl) statEl.textContent = data.count > 0 ? `❤️ ${data.count}` : '';
    });
}

// ── Media modal ──────────────────────────────────────────────────────
function openCarousel(el) {
    const items = JSON.parse(el.dataset.media);
    const inner = document.getElementById('carouselInner');
    inner.innerHTML = items.map((item, i) => `
        <div class="carousel-item ${i === 0 ? 'active' : ''}">
            ${item.type === 'video'
                ? `<video controls style="max-height:80vh;width:100%;object-fit:contain;"><source src="${item.url}" type="video/mp4"></video>`
                : `<img src="${item.url}" style="max-height:80vh;width:100%;object-fit:contain;" alt="">`}
        </div>`).join('');
    document.getElementById('mediaPreviewModal').classList.add('open');
}
function previewSingleMedia(url) {
    document.getElementById('carouselInner').innerHTML =
        `<div class="carousel-item active"><img src="${url}" style="max-height:80vh;width:100%;object-fit:contain;" alt=""></div>`;
    document.getElementById('mediaPreviewModal').classList.add('open');
}
function closeMediaModal() {
    document.getElementById('mediaPreviewModal').classList.remove('open');
}
</script>
@endpush
