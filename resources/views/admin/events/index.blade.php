@extends('layouts.admin')

@section('title', 'Events')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-700 mb-0" style="font-size:var(--text-md);">Events</h5>
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> New Event
        </a>
    </div>

    <div class="table-card">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="60">Image</th>
                    <th>Title</th>
                    <th>Date &amp; Time</th>
                    <th>Location</th>
                    <th>Attendees</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($events as $event)
                    @php
                        $ended      = $event->event_datetime->isPast();
                        $goingCount = $event->rsvps->where('status', 'going')->count();
                    @endphp
                    <tr>
                        <td>
                            <img src="{{ $event->image_path && !str_starts_with($event->image_path, 'images/') ? Storage::url($event->image_path) : asset($event->image_path ?? 'images/ICCLOGO.png') }}"
                                 alt="{{ $event->title }}"
                                 style="width:52px;height:36px;object-fit:cover;border-radius:4px;">
                        </td>
                        <td style="max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                            title="{{ $event->title }}">{{ $event->title }}</td>
                        <td style="white-space:nowrap;">{{ $event->event_datetime->format('M j, Y h:i A') }}</td>
                        <td style="max-width:130px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $event->location }}</td>
                        <td>
                            @if ($goingCount > 0)
                                <span class="badge" style="background:var(--primary-light);color:var(--primary);font-size:0.72rem;padding:3px 8px;border-radius:var(--radius-full);">
                                    <i class="fas fa-user-check me-1"></i>{{ $goingCount }} going
                                </span>
                            @else
                                <span class="text-muted" style="font-size:var(--text-xs);">—</span>
                            @endif
                        </td>
                        <td>
                            @if ($ended)
                                <span class="badge-ended">Ended</span>
                            @else
                                <span class="badge-upcoming">Upcoming</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('admin.events.edit', $event->id) }}"
                               class="action-btn btn-edit-sm me-1">Edit</a>
                            <form method="POST" action="{{ route('admin.events.destroy', $event->id) }}" style="display:inline;"
                                  onsubmit="return confirm('Delete this event?')">
                                @csrf @method('DELETE')
                                <button class="action-btn btn-del">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No events yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $events->links('pagination::bootstrap-5') }}</div>

@endsection
