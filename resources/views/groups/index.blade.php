@extends('layouts.app')

@section('title', 'Community Groups — ICCBI Alumni')

@section('content')

    <div class="card">
        <div class="card-header">
            <h2 class="section-title-sm">
                <i class="fas fa-users text-primary me-1"></i> Community Groups
            </h2>
            <a href="{{ route('groups.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> New Group
            </a>
        </div>

        <div class="card-body" style="padding-bottom: var(--sp-2);">
            <form method="GET">
                <div class="input-wrap mb-0">
                    <i class="fas fa-search input-icon"></i>
                    <input type="text" name="q" class="form-control"
                           style="border-radius:var(--radius-full);"
                           placeholder="Search groups…" value="{{ request('q') }}">
                </div>
            </form>
        </div>

        @if ($groups->isEmpty())
            <div class="empty-state">
                <i class="fas fa-users icon"></i>
                <h3>No Groups Found</h3>
                <p>
                    @if(request('q'))
                        No groups match "{{ request('q') }}".
                    @else
                        Be the first to create a community group!
                    @endif
                </p>
                <a href="{{ route('groups.create') }}" class="btn btn-primary btn-sm mt-2">
                    <i class="fas fa-plus me-1"></i> Create Group
                </a>
            </div>
        @else
            <div class="card-body">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                    @foreach ($groups as $group)
                        @php $isMember = in_array($group->id, $myGroupIds); @endphp
                        <div class="col">
                            <div class="card card-hover h-100" style="overflow:visible;">
                                <div class="card-body d-flex flex-column gap-2">
                                    <a href="{{ route('groups.show', $group->id) }}" class="d-flex align-items-center gap-3 text-decoration-none">
                                        <div class="avatar-initials avatar-md flex-shrink-0">
                                            <i class="fas fa-users" style="font-size:0.9rem;"></i>
                                        </div>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="fw-700 text-dark" style="font-size:var(--text-sm);">
                                                {{ $group->group_name }}
                                            </div>
                                            <div class="text-muted" style="font-size:var(--text-xs);">
                                                {{ $group->members_count }} {{ Str::plural('member', $group->members_count) }}
                                                · by {{ $group->creator->first_name }}
                                            </div>
                                        </div>
                                        @if ($isMember)
                                            <span class="badge badge-primary flex-shrink-0">
                                                <i class="fas fa-check me-1"></i>Joined
                                            </span>
                                        @endif
                                    </a>

                                    @if (!$isMember)
                                        <form method="POST" action="{{ route('groups.members.add', $group->id) }}" class="mt-auto">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-sm btn-wide">
                                                <i class="fas fa-user-plus me-1"></i> Join Group
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('groups.show', $group->id) }}" class="btn btn-surface btn-sm btn-wide mt-auto">
                                            <i class="fas fa-comments me-1"></i> View Group
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card-footer">
                {{ $groups->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

@endsection
