<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — GradNet</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/alumni.css') }}">
</head>
<body style="background-color: #f5f5f5; font-family: 'Inter', Arial, sans-serif; font-size: 0.85rem; padding-bottom: 60px;">

    {{-- ── Sticky blue gradient header (exact match to admin.php) -- --}}
    <div class="admin-header">
        <h4><i class="fas fa-shield-alt me-2"></i>GradNet Admin</h4>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white-50" style="font-size:0.8rem;">
                {{ auth()->user()->full_name }}
            </span>
            <form method="POST" action="{{ route('logout') }}" class="d-inline m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    {{-- ── Admin tab-style navigation ──────────────────────── --}}
    <div class="admin-main">
        <div class="mb-3 d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.dashboard') }}"
               class="btn btn-sm {{ request()->routeIs('admin.dashboard') ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-chart-line me-1"></i>Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="btn btn-sm {{ request()->routeIs('admin.users.*') ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-users me-1"></i>Users
            </a>
            <a href="{{ route('admin.news.index') }}"
               class="btn btn-sm {{ request()->routeIs('admin.news.*') ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-newspaper me-1"></i>News
            </a>
            <a href="{{ route('admin.events.index') }}"
               class="btn btn-sm {{ request()->routeIs('admin.events.*') ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-calendar-days me-1"></i>Events
            </a>
            <a href="{{ route('admin.users.index', ['status' => 'all']) }}"
               class="btn btn-sm {{ request()->routeIs('admin.moderation.*') ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="fas fa-shield-halved me-1"></i>Moderation
            </a>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
