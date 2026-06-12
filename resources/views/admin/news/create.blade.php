@extends('layouts.admin')

@section('title', 'New Article')

@section('content')

    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('admin.news.index') }}" style="color:var(--primary);font-size:var(--text-sm);text-decoration:none;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h5 class="fw-700 mb-0" style="font-size:var(--text-md);">New Article</h5>
    </div>

    <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.news._form')
    </form>

@endsection
