@extends('layouts.admin')

@section('title', 'Users')

@section('content')

    <h5 class="fw-700 mb-3" style="font-size:var(--text-md);">Manage Users</h5>

    {{-- Status tabs --}}
    <div class="filter-tabs">
        @foreach (['all' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
            <a href="{{ route('admin.users.index', ['status' => $key, 'q' => request('q')]) }}"
               class="filter-tab {{ $status === $key ? 'active' : '' }}">
                {{ $label }}
                <span class="count">{{ $counts[$key] }}</span>
            </a>
        @endforeach
    </div>

    {{-- Search --}}
    <form method="GET" class="d-flex gap-2 mb-3">
        <input type="hidden" name="status" value="{{ $status }}">
        <input type="text" name="q" value="{{ request('q') }}"
               class="form-control form-control-sm" style="max-width:300px;"
               placeholder="Search by name or email…">
        <button type="submit" class="btn btn-primary btn-sm">Search</button>
    </form>

    {{-- ── Bulk action bar (visible only when rows are checked) ─────── --}}
    <div id="bulkBar"
         style="display:none;align-items:center;gap:12px;padding:10px 16px;
                background:var(--primary-light);border-radius:var(--radius);
                border:1px solid var(--border);margin-bottom:12px;">
        <span id="bulkCount" class="fw-600 text-primary" style="font-size:var(--text-sm);white-space:nowrap;"></span>
        <select id="bulkActionSelect" class="form-select form-select-sm" style="max-width:200px;">
            <option value="">— Bulk Action —</option>
            <option value="approve">Approve Selected</option>
            <option value="reject">Reject Selected</option>
            <option value="delete">Delete Selected</option>
        </select>
        <button type="button" onclick="submitBulk()" class="btn btn-primary btn-sm">Apply</button>
        <button type="button" onclick="clearSelection()" class="btn btn-surface btn-sm">Clear</button>
    </div>

    {{-- Hidden form that bulk JS submits --}}
    <form id="bulkForm" method="POST" action="{{ route('admin.users.bulk') }}">
        @csrf
        <input type="hidden" name="action" id="bulkActionInput">
        {{-- user_ids[] appended dynamically by JS --}}
    </form>

    <div class="table-card">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="36">
                        <input type="checkbox" id="selectAll" onchange="toggleAll(this)"
                               style="width:15px;height:15px;cursor:pointer;" title="Select all">
                    </th>
                    <th width="36"></th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Program</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                    <tr>
                        <td>
                            <input type="checkbox" class="row-check" value="{{ $u->id }}"
                                   onchange="updateBulkBar()"
                                   style="width:15px;height:15px;cursor:pointer;">
                        </td>
                        <td>
                            <img src="{{ $u->avatar_url }}" alt="{{ $u->first_name }}"
                                 class="avatar avatar-sm"
                                 onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}'">
                        </td>
                        <td>
                            <a href="{{ route('admin.users.show', $u->id) }}"
                               class="fw-600 text-dark text-decoration-none">{{ $u->full_name }}</a>
                        </td>
                        <td class="text-muted">{{ $u->email }}</td>
                        <td>{{ $u->program ?? '—' }}</td>
                        <td>{{ $u->graduation_year ?? '—' }}</td>
                        <td>
                            <span class="badge-status badge-{{ $u->status }}">{{ ucfirst($u->status) }}</span>
                        </td>
                        <td>{{ $u->created_at->format('M j, Y') }}</td>
                        <td style="white-space:nowrap;">
                            @if ($u->status === 'pending' || $u->status === 'rejected')
                                <form method="POST" action="{{ route('admin.users.approve', $u->id) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button class="action-btn btn-approve me-1">Approve</button>
                                </form>
                            @endif
                            @if ($u->status === 'pending' || $u->status === 'approved')
                                <form method="POST" action="{{ route('admin.users.reject', $u->id) }}" style="display:inline;">
                                    @csrf @method('PATCH')
                                    <button class="action-btn btn-reject me-1">Reject</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.users.destroy', $u->id) }}" style="display:inline;"
                                  onsubmit="return confirm('Delete {{ addslashes($u->full_name) }}?')">
                                @csrf @method('DELETE')
                                <button class="action-btn btn-del">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $users->links('pagination::bootstrap-5') }}</div>

@endsection

@push('scripts')
<script>
function toggleAll(master) {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = master.checked);
    updateBulkBar();
}

function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked');
    const bar = document.getElementById('bulkBar');
    if (checked.length > 0) {
        bar.style.display = 'flex';
        document.getElementById('bulkCount').textContent = checked.length + ' user(s) selected';
    } else {
        bar.style.display = 'none';
        document.getElementById('selectAll').checked = false;
    }
}

function clearSelection() {
    document.querySelectorAll('.row-check, #selectAll').forEach(cb => cb.checked = false);
    updateBulkBar();
}

function submitBulk() {
    const action = document.getElementById('bulkActionSelect').value;
    if (!action) { alert('Please choose a bulk action first.'); return; }

    const ids = [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
    if (!ids.length) return;

    if (action === 'delete' && !confirm('Permanently delete ' + ids.length + ' user(s)? This cannot be undone.')) return;
    if (action === 'reject' && !confirm('Reject ' + ids.length + ' user(s)?')) return;

    const form = document.getElementById('bulkForm');
    document.getElementById('bulkActionInput').value = action;

    // Remove any previous id inputs
    form.querySelectorAll('input[name="user_ids[]"]').forEach(el => el.remove());
    ids.forEach(id => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'user_ids[]';
        inp.value = id;
        form.appendChild(inp);
    });

    form.submit();
}
</script>
@endpush
