@extends('layouts.admin')

@section('title', 'News')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-700 mb-0" style="font-size:var(--text-md);">News Articles</h5>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> New Article
        </a>
    </div>

    <div class="table-card">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="60">Image</th>
                    <th>Title</th>
                    <th>Published By</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($news as $item)
                    <tr>
                        <td>
                            <img src="{{ $item->image_url ?? asset('images/gradnet-logo.png') }}"
                                 alt="{{ $item->title }}"
                                 style="width:52px;height:36px;object-fit:cover;border-radius:4px;">
                        </td>
                        <td style="max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                            title="{{ $item->title }}">{{ $item->title }}</td>
                        <td>{{ $item->uploader?->first_name }}</td>
                        <td>{{ $item->created_at->format('M j, Y') }}</td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('admin.news.edit', $item->id) }}"
                               class="action-btn btn-edit-sm me-1">Edit</a>
                            <form method="POST" action="{{ route('admin.news.destroy', $item->id) }}" style="display:inline;"
                                  onsubmit="return confirm('Delete this article?')">
                                @csrf @method('DELETE')
                                <button class="action-btn btn-del">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No news articles yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $news->links('pagination::bootstrap-5') }}</div>

@endsection
