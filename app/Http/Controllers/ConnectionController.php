<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConnectionController extends Controller
{
    /** GET /connections — list accepted connections + browse alumni with filters */
    public function index(Request $request)
    {
        $user   = Auth::user();
        $search = $request->query('search', '');

        // Advanced filter params
        $filterProgram    = $request->query('program', '');
        $filterYear       = $request->query('year', '');
        $filterEmployment = $request->query('employment_status', '');
        $filterLocation   = $request->query('location', '');

        // ── Accepted connections ─────────────────────────────────────
        // .unique('id') removes duplicates that arise from bidirectional rows
        $connections = $user->connections()
            ->accepted()
            ->with(['requester:id,first_name,last_name,profile_picture,program,graduation_year',
                    'recipient:id,first_name,last_name,profile_picture,program,graduation_year'])
            ->get()
            ->map(fn($c) => $c->requester_id === $user->id ? $c->recipient : $c->requester)
            ->unique('id')
            ->values();

        // ── Pending received requests ────────────────────────────────
        $pendingReceived = Connection::where('followed_id', $user->id)
            ->where('status', 'pending')
            ->with('requester:id,first_name,last_name,profile_picture,program,graduation_year')
            ->get();

        // ── Browse alumni — with advanced filters ────────────────────
        $alumni = User::where('id', '!=', $user->id)
            ->where('role', 'user')
            ->where('status', 'approved')
            ->when($search, fn($q) => $q->where(function ($qq) use ($search) {
                $qq->whereRaw("LOWER(first_name) LIKE ?", ['%' . strtolower($search) . '%'])
                   ->orWhereRaw("LOWER(last_name) LIKE ?",  ['%' . strtolower($search) . '%'])
                   ->orWhereRaw("LOWER(program) LIKE ?",    ['%' . strtolower($search) . '%']);
            }))
            ->when($filterProgram,    fn($q) => $q->where('program', $filterProgram))
            ->when($filterYear,       fn($q) => $q->where('graduation_year', (int) $filterYear))
            ->when($filterEmployment, fn($q) => $q->where('employment_status', $filterEmployment))
            ->when($filterLocation,   fn($q) => $q->whereRaw(
                "LOWER(home_municipality) LIKE ?", ['%' . strtolower($filterLocation) . '%']
            ))
            ->select('id', 'first_name', 'last_name', 'profile_picture',
                     'program', 'graduation_year', 'employment_status', 'home_municipality')
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        // ── Dropdown option lists ────────────────────────────────────
        $programs = User::where('role', 'user')->where('status', 'approved')
            ->whereNotNull('program')
            ->distinct()->orderBy('program')->pluck('program');

        $years = User::where('role', 'user')->where('status', 'approved')
            ->whereNotNull('graduation_year')
            ->distinct()->orderByDesc('graduation_year')->pluck('graduation_year');

        // ── Connection status lookup maps ────────────────────────────
        $connectedIds = $connections->pluck('id')->toArray();

        $sentIds = Connection::where('follower_id', $user->id)
            ->where('status', 'pending')
            ->pluck('followed_id')
            ->toArray();

        $receivedIds = $pendingReceived->pluck('follower_id')->toArray();

        return view('connections.index', compact(
            'connections', 'pendingReceived', 'alumni',
            'connectedIds', 'sentIds', 'receivedIds',
            'search', 'programs', 'years',
            'filterProgram', 'filterYear', 'filterEmployment', 'filterLocation'
        ));
    }

    /** POST /connections — send a request */
    public function store(Request $request)
    {
        $request->validate(['recipient_id' => 'required|exists:users,id']);
        $user = Auth::user();

        abort_if($request->recipient_id == $user->id, 422, 'Cannot connect with yourself.');

        $exists = Connection::where(function ($q) use ($user, $request) {
            $q->where('follower_id', $user->id)->where('followed_id', $request->recipient_id);
        })->orWhere(function ($q) use ($user, $request) {
            $q->where('follower_id', $request->recipient_id)->where('followed_id', $user->id);
        })->exists();

        abort_if($exists, 422, 'Connection already exists or is pending.');

        Connection::create([
            'follower_id' => $user->id,
            'followed_id' => $request->recipient_id,
            'status'      => 'pending',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Connection request sent!');
    }

    /** PATCH /connections/{connection}/accept */
    public function accept(Connection $connection)
    {
        abort_if($connection->followed_id !== Auth::id(), 403);
        $connection->update(['status' => 'accepted']);
        return back()->with('success', 'Connection accepted!');
    }

    /** DELETE /connections/{connection} — reject or remove */
    public function destroy(Connection $connection)
    {
        $user = Auth::user();
        abort_if(
            $connection->follower_id !== $user->id &&
            $connection->followed_id !== $user->id,
            403
        );
        $connection->delete();
        return back()->with('success', 'Connection removed.');
    }
}
