@extends('layouts.app')

@section('title', 'Notifications — ICCBI Alumni')

@section('content')

    <div class="card">
        <div class="card-header">
            <h2 class="section-title-sm">
                <i class="fas fa-bell text-primary me-1"></i> Notifications
            </h2>
            <button class="btn btn-surface btn-sm" onclick="markAllRead()">
                <i class="fas fa-check-double me-1"></i> Mark all read
            </button>
        </div>

        @if ($notifications->isEmpty())
            <div class="empty-state">
                <i class="fas fa-bell-slash icon"></i>
                <h3>You're all caught up!</h3>
                <p>No new notifications right now.</p>
            </div>
        @else
            <div style="padding: var(--sp-2) 0;">
                @foreach ($notifications as $notif)
                    @php
                        $link = match ($notif->type) {
                            'reaction', 'comment' => $notif->post_id ? route('posts.show', $notif->post_id) : '#',
                            'connection'          => route('connections.index'),
                            'news'                => route('news.index'),
                            'event'               => route('events.index'),
                            default               => '#',
                        };

                        [$iconClass, $iconBg] = match ($notif->type) {
                            'reaction'   => ['fas fa-heart',       '#e0245e'],
                            'comment'    => ['fas fa-comment',     'var(--primary)'],
                            'connection' => ['fas fa-user-plus',   'var(--accent)'],
                            'news'       => ['fas fa-newspaper',   '#f59e0b'],
                            'event'      => ['fas fa-calendar-alt','#10b981'],
                            default      => ['fas fa-bell',        'var(--primary)'],
                        };
                    @endphp

                    <a href="{{ $link }}" class="notif-item {{ !$notif->is_read ? 'unread' : '' }}">
                        <div class="notif-avatar-wrap">
                            <img src="{{ $notif->actor->avatar_url }}"
                                 alt="{{ $notif->actor->first_name }}"
                                 class="avatar avatar-md"
                                 onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}'">
                            <span class="notif-type-icon" style="background:{{ $iconBg }}">
                                <i class="{{ $iconClass }}"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="notif-msg">{!! $notif->message !!}</div>
                            <div class="notif-time">
                                <i class="far fa-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                            </div>
                        </div>
                        @if (!$notif->is_read)
                            <span class="flex-shrink-0" style="width:8px;height:8px;border-radius:50%;background:var(--primary);"></span>
                        @endif
                    </a>
                    <div class="divider" style="margin:0;"></div>
                @endforeach
            </div>

            <div class="card-footer">
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

@endsection

@push('scripts')
<script>
function markAllRead() {
    fetch('/notifications/read-all', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    }).then(() => {
        document.querySelectorAll('.notif-item.unread').forEach(el => el.classList.remove('unread'));
        // Remove all blue dots
        document.querySelectorAll('.notif-item span[style*="border-radius:50%"]').forEach(el => el.remove());
        showToast('All notifications marked as read.', 'success');
    });
}
</script>
@endpush
