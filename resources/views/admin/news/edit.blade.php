@extends('layouts.admin')

@section('title', 'Edit Article')

@section('content')

    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('admin.news.index') }}" style="color:var(--primary);font-size:var(--text-sm);text-decoration:none;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h5 class="fw-700 mb-0" style="font-size:var(--text-md);">Edit Article</h5>
    </div>

    <form method="POST" action="{{ route('admin.news.update', $news->id) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.news._form', ['news' => $news])
    </form>

@endsection
