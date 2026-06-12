<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /** GET /admin/events */
    public function index()
    {
        $events = Event::with(['uploader:id,first_name,last_name', 'rsvps'])
            ->orderByDesc('event_datetime')
            ->paginate(15);
        return view('admin.events.index', compact('events'));
    }

    /** GET /admin/events/create */
    public function create()
    {
        return view('admin.events.create');
    }

    /** POST /admin/events */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'event_datetime' => 'required|date',
            'location'       => 'required|string|max:255',
            'image'          => 'nullable|image|max:4096|mimes:jpeg,jpg,png,gif,webp',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('events', 'public')
            : null;

        Event::create([
            'title'          => $data['title'],
            'description'    => $data['description'],
            'event_datetime' => $data['event_datetime'],
            'location'       => $data['location'],
            'image_path'     => $imagePath ?? 'images/gradnet-logo.png',
            'uploaded_by'    => Auth::id(),
        ]);

        return redirect()->route('admin.events.index')->with('success', 'Event created.');
    }

    /** GET /admin/events/{event}/edit */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /** PUT /admin/events/{event} */
    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'event_datetime' => 'required|date',
            'location'       => 'required|string|max:255',
            'image'          => 'nullable|image|max:4096|mimes:jpeg,jpg,png,gif,webp',
        ]);

        if ($request->hasFile('image')) {
            if ($event->image_path && !str_starts_with($event->image_path, 'images/')) {
                Storage::disk('public')->delete($event->image_path);
            }
            $data['image_path'] = $request->file('image')->store('events', 'public');
        }

        $event->update([
            'title'          => $data['title'],
            'description'    => $data['description'],
            'event_datetime' => $data['event_datetime'],
            'location'       => $data['location'],
            'image_path'     => $data['image_path'] ?? $event->image_path,
        ]);

        return redirect()->route('admin.events.index')->with('success', 'Event updated.');
    }

    /** DELETE /admin/events/{event} */
    public function destroy(Event $event)
    {
        if ($event->image_path && !str_starts_with($event->image_path, 'images/')) {
            Storage::disk('public')->delete($event->image_path);
        }

        $event->delete();

        return back()->with('success', 'Event deleted.');
    }
}
