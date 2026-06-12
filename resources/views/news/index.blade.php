@extends('layouts.app')

@section('title', 'News & Updates — ICCBI Alumni')

@section('content')

    <div class="card">
        <div class="card-header">
            <h2 class="section-title-sm">
                <i class="fas fa-bullhorn text-primary me-1"></i> News &amp; Updates
            </h2>
        </div>

        @if ($news->isEmpty())
            <div class="empty-state">
                <i class="fas fa-newspaper icon"></i>
                <h3>No News Yet</h3>
                <p>Check back soon for the latest news and announcements.</p>
            </div>
        @else
            <div class="card-body">
                <div class="row row-cols-1 row-cols-sm-2 g-3 mb-3">
                    @foreach ($news as $item)
                        <div class="col">
                            <a href="{{ route('news.show', $item->id) }}"
                               class="card card-hover text-decoration-none d-block h-100">
                                <div style="height:160px;overflow:hidden;">
                                    <img src="{{ $item->image_url ?? asset('images/ICCLOGO.png') }}"
                                         alt="{{ $item->title }}"
                                         style="width:100%;height:100%;object-fit:cover;transition:transform 0.3s ease;"
                                         onmouseover="this.style.transform='scale(1.05)'"
                                         onmouseout="this.style.transform='scale(1)'">
                                </div>
                                <div class="card-body">
                                    <h3 class="fw-700 line-clamp-2 text-dark mb-1" style="font-size:var(--text-sm);">
                                        {{ $item->title }}
                                    </h3>
                                    <p class="text-muted line-clamp-3 mb-2" style="font-size:var(--text-xs);">
                                        {{ $item->description }}
                                    </p>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-muted" style="font-size:0.7rem;">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ $item->created_at->format('M j, Y') }}
                                        </span>
                                        <span class="text-primary fw-600" style="font-size:var(--text-xs);">
                                            Read More →
                                        </span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer">
                {{ $news->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

@endsection
