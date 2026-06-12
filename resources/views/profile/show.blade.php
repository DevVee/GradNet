@extends('layouts.app')

@section('title', $user->full_name . ' — ICCBI Alumni')

@section('content')

    {{-- ── Profile Card ─────────────────────────────────────────── --}}
    <div class="card" style="overflow:visible;">

        {{-- Cover banner --}}
        <div style="height:120px;background:linear-gradient(135deg,var(--primary) 0%,#1e4db7 100%);border-radius:var(--radius) var(--radius) 0 0;position:relative;">
        </div>

        {{-- Avatar + actions row --}}
        <div class="d-flex align-items-flex-end justify-content-between px-4 pb-0"
             style="margin-top:-44px;position:relative;z-index:1;">
            <img src="{{ $user->avatar_url }}"
                 alt="{{ $user->full_name }}"
                 class="avatar avatar-2xl avatar-ring-white"
                 onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}'">

            <div class="d-flex gap-2 align-items-center" style="padding-top:52px;">
                @if ($authUser->id !== $user->id)
                    <a href="{{ route('messages.index') }}?to={{ $user->id }}" class="btn btn-surface btn-sm">
                        <i class="fas fa-comment-dots me-1"></i> Message
                    </a>
                    @if ($connectionStatus === 'accepted')
                        <button class="btn btn-surface btn-sm" disabled>
                            <i class="fas fa-check me-1" style="color:var(--success)"></i> Connected
                        </button>
                    @elseif ($connectionStatus === 'pending_sent')
                        <button class="btn btn-surface btn-sm" disabled>
                            <i class="fas fa-clock me-1"></i> Requested
                        </button>
                    @elseif ($connectionStatus === 'pending_received')
                        <form method="POST" action="{{ route('connections.accept', /* filled dynamically */ 0) }}" style="display:inline;">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-user-check me-1"></i> Accept
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('connections.store') }}" style="display:inline;">
                            @csrf
                            <input type="hidden" name="recipient_id" value="{{ $user->id }}">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-user-plus me-1"></i> Connect
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('profile.edit', $user->id) }}" class="btn btn-outline btn-sm">
                        <i class="fas fa-edit me-1"></i> Edit Profile
                    </a>
                @endif
            </div>
        </div>

        {{-- Name, meta, badges --}}
        <div class="card-body" style="padding-top:var(--sp-3);">
            <h1 class="fw-700 text-dark mb-1" style="font-size:var(--text-xl);">{{ $user->full_name }}</h1>

            @if ($user->present_occupation || $user->workplace)
                <p class="text-secondary mb-1" style="font-size:var(--text-sm);">
                    <i class="fas fa-briefcase me-1"></i>
                    {{ $user->present_occupation }}@if($user->workplace) at {{ $user->workplace }}@endif
                </p>
            @endif

            @if ($user->program || $user->graduation_year)
                <p class="text-muted mb-2" style="font-size:var(--text-sm);">
                    <i class="fas fa-graduation-cap me-1"></i>
                    {{ $user->program }}{{ $user->graduation_year ? ' · Class of ' . $user->graduation_year : '' }}
                </p>
            @endif

            <div class="d-flex flex-wrap gap-2 mt-2">
                <span class="badge badge-primary">
                    <i class="fas fa-users me-1"></i>{{ $connectionCount }} connections
                </span>
                @if ($user->home_municipality)
                    <span class="badge badge-muted">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ $user->home_municipality }}
                    </span>
                @endif
                @if ($user->highest_degree)
                    <span class="badge badge-muted">
                        <i class="fas fa-award me-1"></i>{{ $user->highest_degree }}
                    </span>
                @endif
            </div>

            {{-- Social links --}}
            @if ($user->facebook_link || $user->instagram_link || $user->linkedin_link || $user->facebook_account)
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @if ($user->facebook_link)
                        <a href="{{ $user->facebook_link }}" target="_blank" rel="noopener"
                           class="btn btn-surface btn-sm" style="color:#1877f2;">
                            <i class="fab fa-facebook me-1"></i> Facebook
                        </a>
                    @elseif ($user->facebook_account)
                        <span class="btn btn-surface btn-sm" style="color:#1877f2;cursor:default;">
                            <i class="fab fa-facebook me-1"></i> {{ $user->facebook_account }}
                        </span>
                    @endif
                    @if ($user->instagram_link)
                        <a href="{{ $user->instagram_link }}" target="_blank" rel="noopener"
                           class="btn btn-surface btn-sm" style="color:#c13584;">
                            <i class="fab fa-instagram me-1"></i> Instagram
                        </a>
                    @endif
                    @if ($user->linkedin_link)
                        <a href="{{ $user->linkedin_link }}" target="_blank" rel="noopener"
                           class="btn btn-surface btn-sm" style="color:#0a66c2;">
                            <i class="fab fa-linkedin me-1"></i> LinkedIn
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ── About / Details ─────────────────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <h2 class="section-title-sm"><i class="fas fa-info-circle me-1 text-primary"></i> About</h2>
        </div>
        <div class="card-body">
            <div class="row row-cols-1 row-cols-md-2 g-3">
                @foreach ([
                    ['fas fa-envelope',        'Email',             $user->email],
                    ['fas fa-phone',            'Phone',             $user->phone],
                    ['fas fa-heart',            'Civil Status',      $user->civil_status],
                    ['fas fa-church',           'Religion',          $user->religion],
                    ['fas fa-birthday-cake',    'Birthday',          $user->birthday?->format('F j, Y')],
                    ['fas fa-map-marker-alt',   'Municipality',      $user->home_municipality],
                    ['fas fa-graduation-cap',   'Highest Degree',    $user->highest_degree],
                    ['fas fa-briefcase',        'Employment Status', $user->employment_status],
                ] as [$icon, $label, $value])
                    @if ($value)
                        <div class="col">
                            <div class="d-flex align-items-start gap-3">
                                <i class="{{ $icon }} text-primary mt-1" style="width:16px;font-size:0.9rem;flex-shrink:0;"></i>
                                <div>
                                    <div class="text-muted" style="font-size:var(--text-xs);">{{ $label }}</div>
                                    <div class="fw-500 text-dark" style="font-size:var(--text-sm);">{{ $value }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Posts ────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <h2 class="section-title-sm">
                <i class="fas fa-stream me-1 text-primary"></i> Posts
            </h2>
        </div>
        <div style="padding: var(--sp-3);">
            @if ($posts->isEmpty())
                <div class="empty-state" style="padding: var(--sp-8) var(--sp-4);">
                    <i class="fas fa-file-alt icon"></i>
                    <h3>No Posts Yet</h3>
                    <p>{{ $authUser->id === $user->id ? 'Share something with the network.' : 'This user hasn\'t posted yet.' }}</p>
                </div>
            @else
                @foreach ($posts as $post)
                    @include('feed._post-card', ['post' => $post->load('user','media','reactions','comments'), 'authUser' => $authUser])
                @endforeach
            @endif
        </div>
    </div>

@endsection

@section('right-sidebar')

    {{-- Connections --}}
    @if ($connectionCount > 0 || $authUser->id === $user->id)
    <div class="widget">
        <div class="widget-header">
            <span class="widget-title">Connections</span>
            <span class="badge badge-primary">{{ $connectionCount }}</span>
        </div>
        <div class="widget-body">
            <a href="{{ route('connections.index') }}" class="btn btn-outline btn-wide btn-sm">
                <i class="fas fa-users me-1"></i> View All Connections
            </a>
        </div>
    </div>
    @endif

    {{-- If own profile: quick edit prompt --}}
    @if ($authUser->id === $user->id)
    <div class="widget">
        <div class="widget-header">
            <span class="widget-title">Complete Your Profile</span>
        </div>
        <div class="widget-body d-flex flex-column gap-2">
            @php $complete = collect([$user->profile_picture, $user->program, $user->graduation_year, $user->employment_status, $user->present_occupation])->filter()->count(); @endphp
            <div style="font-size:var(--text-xs);color:var(--text-muted);">{{ $complete }}/5 fields filled</div>
            <div style="height:6px;background:var(--border-light);border-radius:var(--radius-full);">
                <div style="height:100%;width:{{ ($complete/5)*100 }}%;background:var(--primary);border-radius:var(--radius-full);transition:width 0.5s ease;"></div>
            </div>
            <a href="{{ route('profile.edit', $user->id) }}" class="btn btn-primary btn-wide btn-sm mt-1">
                <i class="fas fa-edit me-1"></i> Edit Profile
            </a>
        </div>
    </div>
    @endif

@endsection
