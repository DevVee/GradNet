<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventComment;
use App\Models\EventLike;
use App\Models\EventRsvp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /** GET /events */
    public function index(Request $request)
    {
        $past = $request->boolean('past');

        $upcomingEvents = Event::where('event_datetime', '>=', now())
            ->orderBy('event_datetime')
            ->get();

        $previousEvents = Event::where('event_datetime', '<', now())
            ->orderByDesc('event_datetime')
            ->get();

        // Pre-load going counts to avoid N+1 on the index page
        $allIds = $upcomingEvents->pluck('id')->merge($previousEvents->pluck('id'));
        $goingCounts = EventRsvp::whereIn('event_id', $allIds)
            ->where('status', 'going')
            ->selectRaw('event_id, count(*) as total')
            ->groupBy('event_id')
            ->pluck('total', 'event_id');

        return view('events.index', compact('upcomingEvents', 'previousEvents', 'past', 'goingCounts'));
    }

    /** GET /events/{event} */
    public function show(Event $event)
    {
        $event->load([
            'likes',
            'comments.user:id,first_name,last_name,profile_picture',
        ]);

        $authUser  = Auth::user();
        $userLiked = $event->likes->where('user_id', $authUser->id)->isNotEmpty();
        $likeCount = $event->likes->count();

        // RSVP data
        $userRsvp   = EventRsvp::where('event_id', $event->id)->where('user_id', $authUser->id)->first();
        $goingCount = EventRsvp::where('event_id', $event->id)->where('status', 'going')->count();
        $maybeCount = EventRsvp::where('event_id', $event->id)->where('status', 'maybe')->count();

        return view('events.show', compact(
            'event', 'authUser', 'userLiked', 'likeCount',
            'userRsvp', 'goingCount', 'maybeCount'
        ));
    }

    /** POST /events/{event}/like */
    public function toggleLike(Event $event)
    {
        $existing = EventLike::where('event_id', $event->id)->where('user_id', Auth::id())->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            EventLike::create(['event_id' => $event->id, 'user_id' => Auth::id()]);
            $liked = true;
        }

        $count = EventLike::where('event_id', $event->id)->count();
        return response()->json(['liked' => $liked, 'count' => $count]);
    }

    /** POST /events/{event}/rsvp */
    public function rsvp(Request $request, Event $event)
    {
        $request->validate([
            'status' => 'required|in:going,maybe,not_going',
        ]);

        EventRsvp::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => Auth::id()],
            ['status'   => $request->status]
        );

        return response()->json([
            'status'      => $request->status,
            'going_count' => EventRsvp::where('event_id', $event->id)->where('status', 'going')->count(),
            'maybe_count' => EventRsvp::where('event_id', $event->id)->where('status', 'maybe')->count(),
        ]);
    }

    /** POST /events/{event}/comments */
    public function storeComment(Request $request, Event $event)
    {
        $request->validate(['content' => 'required|string|max:1000']);

        // DB column is `comment` (EventComment fillable) — map input `content` → `comment`
        $comment = EventComment::create([
            'event_id' => $event->id,
            'user_id'  => Auth::id(),
            'comment'  => $request->content,
        ]);

        $comment->load('user:id,first_name,last_name,profile_picture');

        if ($request->wantsJson()) {
            return response()->json([
                'id'         => $comment->id,
                'content'    => $comment->comment,   // return from `comment` column
                'created_at' => $comment->created_at->diffForHumans(),
                'user'       => [
                    'name'        => $comment->user->full_name,
                    'avatar'      => $comment->user->avatar_url,
                    'profile_url' => route('profile.show', $comment->user->id),
                ],
            ]);
        }

        return back()->with('success', 'Comment added.');
    }

    /** DELETE /events/{event}/comments/{comment} */
    public function destroyComment(Event $event, EventComment $comment)
    {
        abort_if($comment->user_id !== Auth::id(), 403);
        $comment->delete();

        if (request()->wantsJson()) return response()->json(['deleted' => true]);
        return back();
    }
}
