@extends('layouts.app')

@section('title', $group->group_name . ' — GradNet')

@section('content')

    <div class="page-header">
        <a href="{{ route('groups.index') }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <h1 class="page-title">Group</h1>
    </div>

    {{-- ── Group Hero Banner ──────────────────────────────────────── --}}
    <div class="hero-banner mb-3" style="position:relative;overflow:hidden;">
        {{-- Decorative watermark icon --}}
        <i class="fas fa-users"
           style="position:absolute;right:-10px;top:-10px;font-size:8rem;opacity:0.06;color:#fff;pointer-events:none;"></i>

        <div class="fw-700 text-white mb-1" style="font-size:var(--text-xl);">{{ $group->group_name }}</div>
        <div class="d-flex flex-wrap gap-3 mb-3" style="font-size:var(--text-xs);opacity:0.9;color:#fff;">
            <span><i class="fas fa-users me-1"></i>{{ $group->members_count }} {{ Str::plural('member', $group->members_count) }}</span>
            <span><i class="fas fa-user-tie me-1"></i>Created by {{ $group->creator->first_name }}</span>
            <span><i class="far fa-calendar-alt me-1"></i>{{ $group->created_at->format('M j, Y') }}</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if ($isMember)
                <a href="{{ route('messages.group.show', $group->id) }}"
                   class="btn btn-sm"
                   style="background:rgba(255,255,255,0.18);color:#fff;border:1px solid rgba(255,255,255,0.35);">
                    <i class="fas fa-comment-dots me-1"></i> Group Chat
                </a>
                @if (!$isAdmin)
                    <button class="btn btn-sm"
                            style="background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.3);"
                            onclick="openLeave()">
                        <i class="fas fa-sign-out-alt me-1"></i> Leave Group
                    </button>
                @endif
            @else
                <form method="POST" action="{{ route('groups.members.add', $group->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm"
                            style="background:#fff;color:var(--primary);font-weight:700;">
                        <i class="fas fa-user-plus me-1"></i> Join Group
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- ── Members ──────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <h2 class="section-title-sm">Members</h2>
            <span class="badge badge-muted">{{ $group->members_count }} total</span>
        </div>

        @if ($members->isEmpty())
            <div class="empty-state">
                <i class="fas fa-user-slash icon"></i>
                <h3>No Members Yet</h3>
                <p>Be the first to join this group.</p>
            </div>
        @else
            <div class="card-body" style="padding-top:var(--sp-2);">
                <div class="row row-cols-1 row-cols-sm-2 g-2">
                    @foreach ($members as $member)
                        <div class="col">
                            <div class="d-flex align-items-center gap-3 px-2 py-2 rounded"
                                 style="transition:background 0.15s;"
                                 onmouseover="this.style.background='var(--bg-hover)'"
                                 onmouseout="this.style.background=''">
                                <a href="{{ route('profile.show', $member->id) }}"
                                   class="d-flex align-items-center gap-3 text-decoration-none flex-grow-1 min-w-0">
                                    <img src="{{ $member->avatar_url }}"
                                         alt="{{ $member->first_name }}"
                                         class="avatar avatar-md"
                                         onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}'">
                                    <div class="min-w-0">
                                        <div class="fw-600 text-dark" style="font-size:var(--text-sm);">
                                            {{ $member->full_name }}
                                            @if ($member->id === $group->created_by)
                                                <span class="badge badge-primary ms-1" style="font-size:0.6rem;">Admin</span>
                                            @endif
                                        </div>
                                        <div class="text-muted" style="font-size:var(--text-xs);">
                                            {{ $member->program ?? 'Alumni' }}
                                            @if ($member->graduation_year) · {{ $member->graduation_year }} @endif
                                        </div>
                                    </div>
                                </a>
                                @if ($isAdmin && $member->id !== auth()->id())
                                    <form method="POST"
                                          action="{{ route('groups.members.remove', [$group->id, $member->id]) }}"
                                          onsubmit="return confirm('Remove {{ $member->first_name }} from the group?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-ghost btn-sm"
                                                style="color:var(--danger);padding:4px 8px;"
                                                title="Remove member">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

@endsection

{{-- ── Leave Confirmation Modal ─────────────────────────────────── --}}
<div class="modal-overlay" id="leaveModal">
    <div class="modal-box" style="max-width:320px;text-align:center;">
        <div class="modal-body py-4">
            <i class="fas fa-sign-out-alt" style="color:var(--danger);font-size:2.5rem;display:block;margin-bottom:var(--sp-3);"></i>
            <h3 class="fw-700 text-dark mb-2" style="font-size:var(--text-base);">Leave "{{ $group->group_name }}"?</h3>
            <p class="text-muted mb-4" style="font-size:var(--text-sm);">You can rejoin at any time.</p>
            <form method="POST" action="{{ route('groups.members.remove', [$group->id, auth()->id()]) }}">
                @csrf @method('DELETE')
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-surface" onclick="closeLeave()">Cancel</button>
                    <button type="submit" class="btn btn-danger">Leave Group</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openLeave()  { document.getElementById('leaveModal').classList.add('open'); }
function closeLeave() { document.getElementById('leaveModal').classList.remove('open'); }
document.getElementById('leaveModal').addEventListener('click', function(e) {
    if (e.target === this) closeLeave();
});
</script>
@endpush
