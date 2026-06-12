@extends('layouts.app')

@section('title', 'Create Group — ICCBI Alumni')

@section('content')

    <div class="page-header">
        <a href="{{ route('groups.index') }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <h1 class="page-title">Create Group</h1>
    </div>

    <div class="card" style="max-width:480px;">
        <div class="card-header">
            <h2 class="section-title-sm">
                <i class="fas fa-users me-1 text-primary"></i> New Community Group
            </h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('groups.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label required" for="group_name">Group Name</label>
                    <input type="text"
                           id="group_name"
                           name="group_name"
                           class="form-control @error('group_name') is-invalid @enderror"
                           value="{{ old('group_name') }}"
                           placeholder="e.g. BSIT Alumni 2022, CS Batch 2020…"
                           maxlength="100"
                           required
                           autofocus>
                    <div class="form-hint">Must be unique. Max 100 characters.</div>
                    @error('group_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('groups.index') }}" class="btn btn-surface">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Create Group
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
