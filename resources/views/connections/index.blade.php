@extends('layouts.app')

@section('title', 'Connections — ICCBI Alumni')

@section('content')

@php
    $svgFallback   = asset('images/default-avatar.svg');
    $activeFilters = collect([$filterProgram, $filterYear, $filterEmployment, $filterLocation])
                     ->filter()->count();
    $banners = ['banner-1','banner-2','banner-3','banner-4','banner-5','banner-6'];
@endphp

{{-- ── Search + Filter Hero (always visible at top) ──────────────── --}}
<div class="alumni-search-hero">
    <form method="GET" action="{{ route('connections.index') }}" id="filterForm">

        {{-- Big search bar --}}
        <div class="input-wrap mb-3">
            <i class="fas fa-magnifying-glass input-icon" style="color:var(--primary);"></i>
            <input type="text"
                   name="search"
                   id="filterSearch"
                   value="{{ $search }}"
                   class="form-control"
                   placeholder="Search alumni by name, program…"
                   autocomplete="off">
            @if($search)
            <a href="{{ route('connections.index', array_filter(['program'=>$filterProgram,'year'=>$filterYear,'employment_status'=>$filterEmployment,'location'=>$filterLocation])) }}"
               style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);text-decoration:none;font-size:0.8rem;padding:4px;">
                <i class="fas fa-xmark"></i>
            </a>
            @endif
        </div>

        {{-- Filter pill bar — programs + years + employment --}}
        <div class="filter-pill-bar">
            {{-- All --}}
            <a href="{{ route('connections.index', array_filter(['search'=>$search])) }}"
               class="filter-pill {{ (!$filterProgram && !$filterYear && !$filterEmployment) ? 'active' : '' }}">
                All Alumni
            </a>

            {{-- Programs --}}
            @foreach($programs as $prog)
            <a href="{{ route('connections.index', array_filter(['search'=>$search, 'program'=>$prog, 'year'=>$filterYear, 'employment_status'=>$filterEmployment])) }}"
               class="filter-pill {{ $filterProgram === $prog ? 'active' : '' }}">
                {{ $prog }}
            </a>
            @endforeach

            {{-- Recent years (last 8) --}}
            @foreach(array_slice($years->toArray(), 0, 8) as $yr)
            <a href="{{ route('connections.index', array_filter(['search'=>$search, 'program'=>$filterProgram, 'year'=>$yr, 'employment_status'=>$filterEmployment])) }}"
               class="filter-pill {{ (string)$filterYear === (string)$yr ? 'active' : '' }}">
                {{ $yr }}
            </a>
            @endforeach

            {{-- Employment --}}
            @foreach(['Employed','Self-Employed','Student','Unemployed'] as $emp)
            <a href="{{ route('connections.index', array_filter(['search'=>$search, 'program'=>$filterProgram, 'year'=>$filterYear, 'employment_status'=>$emp])) }}"
               class="filter-pill {{ $filterEmployment === $emp ? 'active' : '' }}">
                {{ $emp }}
            </a>
            @endforeach
        </div>

        {{-- Location (hidden, for JS use only) --}}
        <input type="hidden" name="location" value="{{ $filterLocation }}">

    </form>
</div>

{{-- ── Pending Connection Requests ────────────────────────────────── --}}
@if ($pendingReceived->isNotEmpty())
<div class="card">
    <div class="card-header">
        <h2 class="section-title-sm">
            <i class="fas fa-user-clock me-1" style="color:#f97316;"></i> Connection Requests
        </h2>
        <span class="badge" style="background:#f97316;color:#fff;">{{ $pendingReceived->count() }} new</span>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 g-0">
        @foreach ($pendingReceived as $pending)
        <div class="col" style="border-bottom:1px solid var(--border-light);">
            <div class="d-flex align-items-center gap-3 px-4 py-3">
                <a href="{{ route('profile.show', $pending->requester->id) }}">
                    <img src="{{ $pending->requester->avatar_url }}"
                         alt="{{ $pending->requester->first_name }}"
                         class="avatar avatar-lg"
                         onerror="this.onerror=null;this.src=this.dataset.fallback"
                         data-fallback="{{ $svgFallback }}">
                </a>
                <div style="flex:1;min-width:0;">
                    <a href="{{ route('profile.show', $pending->requester->id) }}"
                       class="fw-700 text-dark text-decoration-none d-block truncate"
                       style="font-size:var(--text-sm);">
                        {{ $pending->requester->full_name }}
                    </a>
                    <div class="text-muted" style="font-size:var(--text-xs);margin-top:1px;">
                        {{ $pending->requester->program ?? 'ICCBI Alumni' }}
                        @if($pending->requester->graduation_year) · {{ $pending->requester->graduation_year }}@endif
                    </div>
                    @if($pending->requester->employment_status)
                    <div style="font-size:var(--text-xs);color:var(--success);margin-top:2px;">
                        <i class="fas fa-circle" style="font-size:6px;"></i>
                        {{ $pending->requester->employment_status }}
                    </div>
                    @endif
                </div>
                <div class="d-flex flex-column gap-2 flex-shrink-0">
                    <form method="POST" action="{{ route('connections.accept', $pending->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-primary btn-sm" style="min-width:86px;">
                            <i class="fas fa-check me-1"></i> Accept
                        </button>
                    </form>
                    <form method="POST" action="{{ route('connections.destroy', $pending->id) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-surface btn-sm" style="min-width:86px;">Decline</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ── My Friends ────────────────────────────────────────────────── --}}
@if ($connections->isNotEmpty())
<div class="card">
    <div class="card-header">
        <h2 class="section-title-sm">
            <i class="fas fa-heart me-1" style="color:#ef4444;font-size:0.85rem;"></i> My Friends
        </h2>
        <span class="badge badge-primary">{{ $connections->count() }}</span>
    </div>
    <div class="card-body">
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
            @foreach ($connections as $loop_i => $conn)
            <div class="col">
                <div class="people-card">
                    <div class="people-card-banner {{ $banners[$conn->id % 6] }}"></div>
                    <div class="people-card-body">
                        <a href="{{ route('profile.show', $conn->id) }}">
                            <img src="{{ $conn->avatar_url }}"
                                 alt="{{ $conn->first_name }}"
                                 class="avatar avatar-lg avatar-ring-white"
                                 onerror="this.onerror=null;this.src=this.dataset.fallback"
                                 data-fallback="{{ $svgFallback }}">
                        </a>
                        <div class="people-name mt-1">{{ $conn->first_name }}<br>{{ $conn->last_name }}</div>
                        <div class="people-meta">
                            {{ $conn->program ?? 'ICCBI Alumni' }}
                            @if($conn->graduation_year) · {{ $conn->graduation_year }}@endif
                            @if($conn->employment_status)
                            <br><span style="color:var(--success);font-size:0.65rem;">
                                <i class="fas fa-circle" style="font-size:6px;"></i> {{ $conn->employment_status }}
                            </span>
                            @endif
                            @if($conn->home_municipality)
                            <br><span class="text-muted" style="font-size:0.65rem;">
                                <i class="fas fa-location-dot" style="font-size:0.6rem;"></i> {{ $conn->home_municipality }}
                            </span>
                            @endif
                        </div>
                        <div class="people-card-badge connected"><i class="fas fa-check"></i> Friends</div>
                        <a href="{{ route('messages.index') }}" class="btn btn-surface btn-sm btn-wide">
                            <i class="fas fa-message me-1" style="color:#3b82f6;"></i> Message
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ── Discover Alumni ──────────────────────────────────────────────── --}}
<div class="card">
    <div class="card-header">
        <h2 class="section-title-sm">
            <i class="fas fa-compass me-1" style="color:#6366f1;"></i>
            Discover Alumni
            @if ($filterProgram || $filterYear || $filterEmployment || $search)
                <span class="badge badge-primary ms-2">Filtered</span>
            @endif
        </h2>
        <span class="text-muted" style="font-size:var(--text-xs);">{{ $alumni->total() }} found</span>
    </div>

    @if ($alumni->isEmpty())
        <div class="empty-state" style="padding:var(--sp-8) var(--sp-4);">
            <div style="font-size:2.5rem;margin-bottom:var(--sp-3);">🔍</div>
            <h3>No results found</h3>
            <p>
                @if ($search || $activeFilters > 0)
                    Try a different search or
                    <a href="{{ route('connections.index') }}" class="text-primary fw-600">clear all filters</a>.
                @else
                    No approved alumni yet.
                @endif
            </p>
        </div>
    @else
        <div class="card-body">
            <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3">
                @foreach ($alumni as $person)
                @php
                    $isConnected = in_array($person->id, $connectedIds);
                    $isSent      = in_array($person->id, $sentIds);
                    $isReceived  = in_array($person->id, $receivedIds);
                    $bClass      = $banners[$person->id % 6];
                @endphp
                <div class="col">
                    <div class="people-card">
                        <div class="people-card-banner {{ $bClass }}"></div>
                        <div class="people-card-body">
                            <a href="{{ route('profile.show', $person->id) }}">
                                <img src="{{ $person->avatar_url }}"
                                     alt="{{ $person->first_name }}"
                                     class="avatar avatar-lg avatar-ring-white"
                                     onerror="this.onerror=null;this.src=this.dataset.fallback"
                                     data-fallback="{{ $svgFallback }}">
                            </a>
                            <div class="people-name mt-1">
                                {{ $person->first_name }}<br>{{ $person->last_name }}
                            </div>
                            <div class="people-meta">
                                {{ $person->program ?? 'ICCBI Alumni' }}
                                @if($person->graduation_year) · {{ $person->graduation_year }}@endif
                                @if($person->employment_status)
                                <br><span style="color:var(--success);font-size:0.65rem;">
                                    <i class="fas fa-circle" style="font-size:6px;"></i> {{ $person->employment_status }}
                                </span>
                                @endif
                                @if($person->home_municipality)
                                <br><span class="text-muted" style="font-size:0.65rem;">
                                    <i class="fas fa-location-dot" style="font-size:0.6rem;"></i> {{ $person->home_municipality }}
                                </span>
                                @endif
                            </div>

                            @if($isConnected)
                                <div class="people-card-badge connected"><i class="fas fa-check"></i> Friends</div>
                                <a href="{{ route('messages.index') }}" class="btn btn-surface btn-sm btn-wide">
                                    <i class="fas fa-message me-1" style="color:#3b82f6;"></i> Message
                                </a>
                            @elseif($isSent)
                                <div class="people-card-badge pending"><i class="fas fa-clock"></i> Requested</div>
                                <span class="btn btn-surface btn-sm btn-wide" style="cursor:default;opacity:0.6;">Pending…</span>
                            @elseif($isReceived)
                                <div class="people-card-badge pending"><i class="fas fa-bell"></i> Wants to connect</div>
                                <a href="{{ route('connections.index') }}" class="btn btn-primary btn-sm btn-wide">
                                    <i class="fas fa-check me-1"></i> Respond
                                </a>
                            @else
                                <form method="POST" action="{{ route('connections.store') }}">
                                    @csrf
                                    <input type="hidden" name="recipient_id" value="{{ $person->id }}">
                                    <button type="submit" class="btn btn-primary btn-sm btn-wide">
                                        <i class="fas fa-user-plus me-1"></i> Connect
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="card-footer">
            {{ $alumni->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
// Auto-submit on search (debounced 420ms)
const searchInput = document.getElementById('filterSearch');
if (searchInput) {
    searchInput.addEventListener('input', function () {
        clearTimeout(window._st);
        window._st = setTimeout(() => document.getElementById('filterForm').submit(), 420);
    });
}
</script>
@endpush
