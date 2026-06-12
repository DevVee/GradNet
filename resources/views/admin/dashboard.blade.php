@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

    {{-- ── Row 1: Primary stats ────────────────────────────────────── --}}
    <div class="stat-cards">
        <div class="stat-card primary">
            <div class="number">{{ $stats['total_users'] }}</div>
            <div class="label"><i class="fas fa-users me-1"></i>Total Users</div>
        </div>
        <div class="stat-card pending">
            <div class="number">{{ $stats['pending_users'] }}</div>
            <div class="label"><i class="fas fa-clock me-1"></i>Pending Approval</div>
        </div>
        <div class="stat-card news">
            <div class="number">{{ $stats['total_news'] }}</div>
            <div class="label"><i class="fas fa-newspaper me-1"></i>News Articles</div>
        </div>
        <div class="stat-card events">
            <div class="number">{{ $stats['total_events'] }}</div>
            <div class="label"><i class="fas fa-calendar-alt me-1"></i>Events</div>
        </div>
    </div>

    {{-- ── Row 2: Activity stats ────────────────────────────────────── --}}
    <div class="stat-cards mt-2">
        <div class="stat-card" style="border-top:3px solid var(--success);">
            <div class="number" style="color:var(--success);">{{ $stats['approved_users'] }}</div>
            <div class="label"><i class="fas fa-user-check me-1"></i>Approved Alumni</div>
        </div>
        <div class="stat-card" style="border-top:3px solid var(--accent);">
            <div class="number" style="color:var(--accent);">{{ $stats['total_posts'] }}</div>
            <div class="label"><i class="fas fa-file-alt me-1"></i>Total Posts</div>
        </div>
        <div class="stat-card" style="border-top:3px solid #6366f1;">
            <div class="number" style="color:#6366f1;">{{ $stats['total_connections'] }}</div>
            <div class="label"><i class="fas fa-user-friends me-1"></i>Connections</div>
        </div>
        <div class="stat-card" style="border-top:3px solid #f59e0b;">
            <div class="number" style="color:#f59e0b;">{{ $postsThisWeek }}</div>
            <div class="label"><i class="fas fa-fire me-1"></i>Posts This Week</div>
        </div>
    </div>

    {{-- ── Charts row ───────────────────────────────────────────────── --}}
    <div class="row mt-3 g-3">

        {{-- Monthly Registrations bar chart --}}
        <div class="col-md-7">
            <div class="table-card" style="padding:16px 20px;">
                <h6 class="fw-700 mb-3" style="font-size:var(--text-sm);">
                    <i class="fas fa-chart-bar me-1 text-primary"></i> Monthly Registrations (Last 6 Months)
                </h6>
                <canvas id="monthlyChart" height="160"></canvas>
            </div>
        </div>

        {{-- User Status doughnut --}}
        <div class="col-md-5">
            <div class="table-card" style="padding:16px 20px;">
                <h6 class="fw-700 mb-3" style="font-size:var(--text-sm);">
                    <i class="fas fa-chart-pie me-1 text-primary"></i> User Status Breakdown
                </h6>
                <canvas id="statusChart" height="160"></canvas>
            </div>
        </div>

        {{-- Top Programs horizontal bar --}}
        <div class="col-md-6">
            <div class="table-card" style="padding:16px 20px;">
                <h6 class="fw-700 mb-3" style="font-size:var(--text-sm);">
                    <i class="fas fa-graduation-cap me-1 text-primary"></i> Top 5 Programs
                </h6>
                <canvas id="programsChart" height="180"></canvas>
            </div>
        </div>

        {{-- Employment breakdown progress bars --}}
        <div class="col-md-6">
            <div class="table-card" style="padding:16px 20px;">
                <h6 class="fw-700 mb-3" style="font-size:var(--text-sm);">
                    <i class="fas fa-briefcase me-1 text-primary"></i> Employment Breakdown
                </h6>
                @php
                    $empTotal = $employmentBreakdown->sum('total');
                    $empColors = [
                        'employed'    => 'var(--success)',
                        'self-employed'=> 'var(--accent)',
                        'unemployed'  => 'var(--danger)',
                        'student'     => '#6366f1',
                        'retired'     => '#64748b',
                    ];
                @endphp
                @if ($empTotal > 0)
                    @foreach ($employmentBreakdown as $row)
                        @php
                            $pct   = round(($row->total / $empTotal) * 100);
                            $label = $row->employment_status ?? 'Unknown';
                            $color = $empColors[strtolower($label)] ?? '#94a3b8';
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1" style="font-size:var(--text-xs);">
                                <span class="fw-600 text-capitalize">{{ $label }}</span>
                                <span class="text-muted">{{ $row->total }} ({{ $pct }}%)</span>
                            </div>
                            <div style="height:8px;background:var(--border);border-radius:var(--radius-full);overflow:hidden;">
                                <div style="height:100%;width:{{ $pct }}%;background:{{ $color }};border-radius:var(--radius-full);transition:width 0.6s ease;"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted" style="font-size:var(--text-sm);">No employment data yet.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Pending Approvals ─────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
        <h5 class="section-title-sm">Pending Approvals</h5>
        <a href="{{ route('admin.users.index', ['status' => 'pending']) }}"
           style="font-size:var(--text-xs);color:var(--primary);">View all →</a>
    </div>

    <div class="table-card">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Program</th>
                    <th>Year</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pendingUsers as $u)
                    <tr>
                        <td>
                            <a href="{{ route('admin.users.show', $u->id) }}"
                               class="fw-600 text-dark text-decoration-none">
                                {{ $u->full_name }}
                            </a>
                        </td>
                        <td class="text-muted">{{ $u->email }}</td>
                        <td>{{ $u->program ?? '—' }}</td>
                        <td>{{ $u->graduation_year ?? '—' }}</td>
                        <td>{{ $u->created_at->format('M j, Y') }}</td>
                        <td style="white-space:nowrap;">
                            <form method="POST" action="{{ route('admin.users.approve', $u->id) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button class="action-btn btn-approve me-1">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.reject', $u->id) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button class="action-btn btn-reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-check-circle me-1" style="color:#10b981;"></i>
                            No pending approvals!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Monthly Registrations Bar Chart ──────────────────────────────
const monthlyLabels = {!! json_encode(array_column($monthlyRegistrations, 'label')) !!};
const monthlyCounts = {!! json_encode(array_column($monthlyRegistrations, 'count')) !!};

new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: monthlyLabels,
        datasets: [{
            label: 'Registrations',
            data: monthlyCounts,
            backgroundColor: 'rgba(0,48,135,0.75)',
            borderRadius: 4,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } } },
            x: { ticks: { font: { size: 11 } } },
        }
    }
});

// ── Status Doughnut Chart ─────────────────────────────────────────
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Approved', 'Pending', 'Rejected'],
        datasets: [{
            data: [
                {{ $stats['approved_users'] }},
                {{ $stats['pending_users'] }},
                {{ $stats['rejected_users'] }}
            ],
            backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } }
        }
    }
});

// ── Top Programs Horizontal Bar Chart ────────────────────────────
const progLabels = {!! json_encode($topPrograms->pluck('program')->map(fn($p) => $p ?? 'Unknown')->values()) !!};
const progCounts  = {!! json_encode($topPrograms->pluck('total')->values()) !!};

new Chart(document.getElementById('programsChart'), {
    type: 'bar',
    data: {
        labels: progLabels,
        datasets: [{
            label: 'Alumni',
            data: progCounts,
            backgroundColor: [
                'rgba(0,48,135,0.80)',
                'rgba(196,151,47,0.85)',
                'rgba(99,102,241,0.80)',
                'rgba(16,185,129,0.80)',
                'rgba(245,158,11,0.80)',
            ],
            borderRadius: 4,
            borderSkipped: false,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } } },
            y: { ticks: { font: { size: 11 } } },
        }
    }
});
</script>
@endpush
