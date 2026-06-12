@extends('layouts.admin')

@section('title', 'New Event')

@section('content')

    <div class="d-flex align-items-center gap-2 mb-3">
        <a href="{{ route('admin.events.index') }}" style="color:var(--primary);font-size:var(--text-sm);text-decoration:none;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h5 class="fw-700 mb-0" style="font-size:var(--text-md);">New Event</h5>
    </div>

    <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
        @csrf
        @include('admin.events._form')
    </form>

@endsection
