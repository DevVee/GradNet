@extends('layouts.app')

@section('title', 'Events — GradNet')

@section('content')

@php
    /* Precompute so no function calls inside HTML attribute contexts */
    $svgFallback = asset('images/gradnet-logo.png');
@endphp

    {{-- ── Upcoming Events ─────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <h2 class="section-title-sm">
                <i class="fas fa-calendar-days text-primary me-1"></i> Upcoming Events
            </h2>
        </div>

        @if ($upcomingEvents->isEmpty())
            <div class="empty-state">
                <i class="fas fa-calendar-days icon"></i>
                <h3>No Upcoming Events</h3>
                <p>Nothing scheduled yet — check back soon!</p>
            </div>
        @else
            <div style="padding:0;">
                @foreach ($upcomingEvents as $event)
                    @php
                        $eventImg  = $event->image_url ?? asset('images/gradnet-logo.png');
                        $goingCount = $goingCounts[$event->id] ?? 0;
                    @endphp
                    <a href="{{ route('events.show', $event->id) }}"
                       class="d-flex gap-3 text-decoration-none p-4 border-bottom"
                       style="transition:background 0.15s ease;color:inherit;align-items:flex-start;"
                       onmouseover="this.style.background='var(--surface-2)'"
                       onmouseout="this.style.background='transparent'">

                        {{-- Thumbnail --}}
                        <div class="flex-shrink-0 position-relative"
                             style="width:90px;height:90px;border-radius:var(--radius-sm);overflow:hidden;background:var(--surface-3);">
                            <img src="{{ $eventImg }}"
                                 alt="{{ $event->title }}"
                                 style="width:100%;height:100%;object-fit:cover;">
                            <div style="position:absolute;bottom:0;left:0;right:0;
                                        background:rgba(0,48,135,0.82);color:#fff;
                                        font-size:0.6rem;font-weight:700;
                                        padding:3px 6px;text-align:center;">
                                {{ $event->event_datetime->format('D, M d') }}
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="flex-grow-1" style="min-width:0;">
                            <div class="fw-700 text-dark line-clamp-2 mb-1"
                                 style="font-size:var(--text-sm);">{{ $event->title }}</div>

                            <div class="d-flex flex-wrap gap-3 mb-1" style="font-size:var(--text-xs);color:var(--primary);">
                                <span><i class="far fa-clock me-1"></i>{{ $event->event_datetime->format('h:i A') }}</span>
                                @if ($event->location)
                                    <span><i class="fas fa-location-dot me-1"></i>{{ $event->location }}</span>
                                @endif
                                {{-- Going count badge --}}
                                @if ($goingCount > 0)
                                    <span class="badge badge-primary" style="font-size:0.68rem;">
                                        <i class="fas fa-user-check me-1"></i>{{ $goingCount }} going
                                    </span>
                                @endif
                            </div>

                            <p class="text-muted line-clamp-2 mb-0"
                               style="font-size:var(--text-xs);">{{ $event->description }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── Past Events ─────────────────────────────────────────────── --}}
    @if ($previousEvents->isNotEmpty())
    <div class="card">
        <div class="card-header">
            <h2 class="section-title-sm text-secondary">
                <i class="fas fa-clock-rotate-left me-1"></i> Past Events
            </h2>
        </div>
        <div style="padding:0;">
            @foreach ($previousEvents as $event)
                @php
                    $eventImg = $event->image_url ?? asset('images/gradnet-logo.png');
                @endphp
                <a href="{{ route('events.show', $event->id) }}"
                   class="d-flex gap-3 text-decoration-none p-4 border-bottom"
                   style="transition:background 0.15s ease;color:inherit;align-items:flex-start;"
                   onmouseover="this.style.background='var(--surface-2)'"
                   onmouseout="this.style.background='transparent'">

                    <div class="flex-shrink-0 position-relative"
                         style="width:90px;height:90px;border-radius:var(--radius-sm);overflow:hidden;background:var(--surface-3);">
                        <img src="{{ $eventImg }}"
                             alt="{{ $event->title }}"
                             style="width:100%;height:100%;object-fit:cover;opacity:0.72;">
                        <span class="badge badge-muted position-absolute"
                              style="top:4px;right:4px;font-size:0.55rem;padding:1px 5px;">Ended</span>
                        <div style="position:absolute;bottom:0;left:0;right:0;
                                    background:rgba(0,0,0,0.5);color:#fff;
                                    font-size:0.6rem;font-weight:700;
                                    padding:3px 6px;text-align:center;">
                            {{ $event->event_datetime->format('M d, Y') }}
                        </div>
                    </div>

                    <div class="flex-grow-1" style="min-width:0;">
                        <div class="fw-600 text-secondary line-clamp-2 mb-1"
                             style="font-size:var(--text-sm);">{{ $event->title }}</div>
                        <div class="d-flex flex-wrap gap-3 text-muted mb-1" style="font-size:var(--text-xs);">
                            <span><i class="far fa-clock me-1"></i>{{ $event->event_datetime->format('h:i A') }}</span>
                            @if ($event->location)
                                <span><i class="fas fa-location-dot me-1"></i>{{ $event->location }}</span>
                            @endif
                        </div>
                        <p class="text-muted line-clamp-2 mb-0" style="font-size:var(--text-xs);">
                            {{ $event->description }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

@endsection
