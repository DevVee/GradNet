@extends('layouts.admin')

@section('title', $user->full_name)

@section('content')

    <a href="{{ route('admin.users.index') }}"
       class="d-inline-flex align-items-center gap-2 mb-3 text-decoration-none"
       style="color:var(--primary);font-size:var(--text-sm);">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>

    {{-- Profile banner --}}
    <div class="admin-profile-header">
        <img src="{{ $user->avatar_url }}" alt="{{ $user->first_name }}"
             onerror="this.onerror=null;this.src=this.dataset.fallback"
             data-fallback="{{ asset('images/default-avatar.svg') }}">
        <div>
            <div class="name">{{ $user->full_name }}</div>
            <div class="meta">{{ $user->email }}</div>
            <div class="mt-1">
                <span class="badge-status badge-{{ $user->status }}">{{ ucfirst($user->status) }}</span>
            </div>
        </div>
        <div class="ms-auto d-flex gap-2 flex-wrap">
            @if ($user->status !== 'approved')
                <form method="POST" action="{{ route('admin.users.approve', $user->id) }}">
                    @csrf @method('PATCH')
                    <button class="action-btn btn-approve" style="padding:7px 16px;">
                        <i class="fas fa-check me-1"></i>Approve
                    </button>
                </form>
            @endif
            @if ($user->status !== 'rejected')
                <form method="POST" action="{{ route('admin.users.reject', $user->id) }}">
                    @csrf @method('PATCH')
                    <button class="action-btn btn-reject" style="padding:7px 16px;">
                        <i class="fas fa-times me-1"></i>Reject
                    </button>
                </form>
            @endif
            <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                  onsubmit="return confirm('Permanently delete {{ addslashes($user->full_name) }}?')">
                @csrf @method('DELETE')
                <button class="action-btn btn-del" style="padding:7px 16px;">
                    <i class="fas fa-trash me-1"></i>Delete
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="info-card">
                <h6>Personal Information</h6>
                @foreach ([
                    'First Name'   => $user->first_name,
                    'Last Name'    => $user->last_name,
                    'Middle Name'  => $user->middle_name ?? '—',
                    'Email'        => $user->email,
                    'Phone'        => $user->phone_number ?? '—',
                    'Birthday'     => $user->birthday ? $user->birthday->format('M j, Y') : '—',
                    'Age'          => $user->age ?? '—',
                    'Gender'       => $user->sex ?? '—',
                    'Civil Status' => $user->civil_status ?? '—',
                ] as $lbl => $val)
                    <div class="info-row">
                        <span class="label">{{ $lbl }}</span>
                        <span class="value">{{ $val }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-6">
            <div class="info-card">
                <h6>Academic &amp; Career</h6>
                @foreach ([
                    'Education Level' => $user->education_level ?? '—',
                    'College'         => $user->college ?? '—',
                    'Program'         => $user->program ?? '—',
                    'Graduation Year' => $user->graduation_year ?? '—',
                    'Employment'      => $user->employment_status ?? '—',
                    'Occupation'      => $user->occupation ?? '—',
                    'Company'         => $user->company ?? '—',
                    'Employer Type'   => $user->employer_type ?? '—',
                ] as $lbl => $val)
                    <div class="info-row">
                        <span class="label">{{ $lbl }}</span>
                        <span class="value">{{ $val }}</span>
                    </div>
                @endforeach
            </div>

            <div class="info-card">
                <h6>Account Details</h6>
                @foreach ([
                    'Registered' => $user->created_at->format('M j, Y h:i A'),
                    'Role'       => ucfirst($user->role),
                    'Status'     => ucfirst($user->status),
                ] as $lbl => $val)
                    <div class="info-row">
                        <span class="label">{{ $lbl }}</span>
                        <span class="value">{{ $val }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── Posts Moderation ─────────────────────────────────────────── --}}
    @php $userPosts = $user->posts()->latest()->get(); @endphp
    <div class="table-card mt-3">
        <div class="d-flex justify-content-between align-items-center px-3 pt-3 pb-2 border-bottom">
            <h6 class="fw-700 mb-0" style="font-size:var(--text-sm);">
                <i class="fas fa-file-alt me-1 text-primary"></i>
                Posts by {{ $user->first_name }}
                <span class="text-muted fw-400">({{ $userPosts->count() }})</span>
            </h6>
        </div>

        @if ($userPosts->isEmpty())
            <div class="text-center text-muted py-4" style="font-size:var(--text-sm);">No posts yet.</div>
        @else
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Content</th>
                        <th width="130">Posted</th>
                        <th width="90">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($userPosts as $post)
                        <tr>
                            <td style="max-width:0;">
                                <div class="text-dark" style="font-size:var(--text-sm);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:480px;">
                                    {{ $post->content }}
                                </div>
                                @if ($post->media_paths && count(json_decode($post->media_paths, true)) > 0)
                                    <span class="text-muted" style="font-size:var(--text-xs);">
                                        <i class="fas fa-image me-1"></i>{{ count(json_decode($post->media_paths, true)) }} media
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted" style="font-size:var(--text-xs);white-space:nowrap;">
                                {{ $post->created_at->format('M j, Y') }}
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.moderation.posts.destroy', $post->id) }}"
                                      onsubmit="return confirm('Delete this post?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn btn-del">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

@endsection
