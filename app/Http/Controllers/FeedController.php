<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    /** GET /feed — all posts from connections + own posts */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // IDs of accepted connections
        $connectedIds = $user->connections()
            ->accepted()
            ->get()
            ->flatMap(fn($c) => [$c->requester_id, $c->recipient_id])
            ->filter(fn($id) => $id !== $user->id)
            ->unique()
            ->values();

        $visibleIds = $connectedIds->push($user->id);

        $posts = Post::with([
                'user:id,first_name,last_name,profile_picture',
                'media',
                'reactions',
                'comments.user:id,first_name,last_name,profile_picture',
            ])
            ->whereIn('user_id', $visibleIds)
            ->where(function ($q) use ($user) {
                // own posts OR public posts from connections
                $q->where('user_id', $user->id)
                  ->orWhere('is_public', true);
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        // Stories bar: recent connections (up to 12)
        $storyUsers = User::whereIn('id', $connectedIds->all())
            ->inRandomOrder()
            ->limit(12)
            ->get(['id', 'first_name', 'last_name', 'profile_picture']);

        // Right-sidebar: suggested alumni (same program, not yet connected)
        $suggestedAlumni = User::where('id', '!=', $user->id)
            ->where('status', 'approved')
            ->when($user->program, fn($q) => $q->where('program', $user->program))
            ->whereNotIn('id', $connectedIds->all())
            ->inRandomOrder()
            ->limit(5)
            ->get(['id', 'first_name', 'last_name', 'profile_picture', 'program', 'graduation_year']);

        // Fallback: any alumni if same-program returns nothing
        if ($suggestedAlumni->isEmpty()) {
            $suggestedAlumni = User::where('id', '!=', $user->id)
                ->where('status', 'approved')
                ->whereNotIn('id', $connectedIds->all())
                ->inRandomOrder()
                ->limit(5)
                ->get(['id', 'first_name', 'last_name', 'profile_picture', 'program', 'graduation_year']);
        }

        // Right-sidebar: upcoming events (next 30 days)
        $upcomingEvents = Event::where('event_datetime', '>=', now())
            ->where('event_datetime', '<=', now()->addDays(30))
            ->orderBy('event_datetime')
            ->limit(3)
            ->get(['id', 'title', 'event_datetime', 'location']);

        return view('feed.index', compact('posts', 'user', 'suggestedAlumni', 'upcomingEvents', 'storyUsers'));
    }
}
