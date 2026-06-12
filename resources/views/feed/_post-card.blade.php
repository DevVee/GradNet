{{-- ── Post Card partial ─────────────────────────────────────── --}}
{{-- Variables: $post (Post model), $authUser (Auth::user())   --}}
@php
    $isOwn        = $authUser->id === $post->user_id;
    $loveCount    = $post->reactions->where('reaction_type', 'love')->count();
    $userLoved    = $post->reactions->where('reaction_type', 'love')->where('user_id', $authUser->id)->isNotEmpty();
    $commentCount = $post->comments->count();
    $reactors     = $post->reactions->where('reaction_type', 'love')->take(3)->pluck('user.first_name')->filter();
@endphp

<div class="post-card" id="post-{{ $post->id }}">

    {{-- Header --}}
    <div class="post-card-header">
        <a href="{{ route('profile.show', $post->user_id) }}" class="d-flex align-items-center gap-3 text-decoration-none">
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
                {{ $post->created_at->diffForHumans() }}
                &bull;
                <i class="{{ $post->is_public ? 'fas fa-globe-asia' : 'fas fa-lock' }}"
                   style="font-size:0.7rem;" title="{{ $post->is_public ? 'Public' : 'Only me' }}"></i>
            </div>
        </div>

        @if ($isOwn)
            <div class="post-menu dropdown">
                <button class="post-menu-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Post options">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow" style="font-size:var(--text-sm);border-radius:var(--radius);border:1px solid var(--border-light);min-width:160px;">
                    <li>
                        <button class="dropdown-item d-flex align-items-center gap-2 py-2"
                                onclick="openEditModal('{{ $post->id }}', {{ json_encode($post->content) }}, {{ $post->is_public ? 'true' : 'false' }})">
                            <i class="fas fa-edit text-secondary" style="width:16px;"></i> Edit
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger"
                                onclick="deletePost('{{ $post->id }}')">
                            <i class="fas fa-trash" style="width:16px;"></i> Delete
                        </button>
                    </li>
                </ul>
            </div>
        @endif
    </div>

    {{-- Body --}}
    <div class="post-card-body">
        @if($post->content)
            <p style="white-space:pre-wrap;word-break:break-word;font-size:var(--text-base);line-height:1.6;color:var(--text-body);">{{ $post->content }}</p>
        @endif
    </div>

    {{-- Media grid --}}
    @if ($post->media->isNotEmpty())
        @php $mediaItems = $post->media; $displayCount = min($mediaItems->count(), 4); @endphp
        <div class="post-media-grid count-{{ min($mediaItems->count(), 4) }}"
             style="@if($mediaItems->count()===1) max-height:400px; @else max-height:280px; @endif">
            @for ($i = 0; $i < $displayCount; $i++)
                @php $item = $mediaItems[$i]; @endphp
                @if ($i === 3 && $mediaItems->count() > 4)
                    <div class="media-item" style="position:relative;cursor:pointer;background:#000;overflow:hidden;"
                         data-media='@json($mediaItems->map(fn($m)=>["url"=>$m->url,"type"=>$m->media_type]))'
                         onclick="openCarousel(this)">
                        <img src="{{ $item->url }}" alt="" style="opacity:0.5;width:100%;height:100%;object-fit:cover;">
                        <span style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#fff;font-size:1.8rem;font-weight:700;">
                            +{{ $mediaItems->count() - 4 }}
                        </span>
                    </div>
                @elseif ($item->is_video)
                    <div class="media-item" style="overflow:hidden;">
                        <video controls style="width:100%;height:100%;object-fit:cover;">
                            <source src="{{ $item->url }}" type="video/mp4">
                        </video>
                    </div>
                @else
                    <div class="media-item" style="overflow:hidden;cursor:pointer;" onclick="previewSingleMedia('{{ $item->url }}')">
                        <img src="{{ $item->url }}" alt="Post image" style="width:100%;height:100%;object-fit:cover;transition:transform 0.3s ease;" onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
                    </div>
                @endif
            @endfor
        </div>
    @endif

    {{-- Stats --}}
    <div class="post-stats">
        <span class="reactions-stat" data-post-id="{{ $post->id }}"
              title="{{ $reactors->implode(', ') }}{{ $reactors->count() && $loveCount > $reactors->count() ? ' and more' : '' }}">
            @if ($loveCount > 0)
                ❤️ {{ $loveCount }}
            @endif
        </span>
        @if ($commentCount > 0)
            <span>{{ $commentCount }} {{ Str::plural('comment', $commentCount) }}</span>
        @endif
    </div>

    {{-- Actions --}}
    <div class="post-actions">
        <button class="post-action-btn {{ $userLoved ? 'loved' : '' }}"
                onclick="reactPost('{{ $post->id }}', this)">
            <i class="{{ $userLoved ? 'fas' : 'far' }} fa-heart"></i>
            <span>Like</span>
        </button>
        <a href="{{ route('posts.show', $post->id) }}#comments" class="post-action-btn">
            <i class="fas fa-comment"></i>
            <span>Comment</span>
        </a>
    </div>

</div>
