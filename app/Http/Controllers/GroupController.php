<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    // ─────────────────────────────────────────────────────────────────
    //  LIST
    // ─────────────────────────────────────────────────────────────────

    /** GET /groups */
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Group::withCount('members')->with('creator:id,first_name,last_name');

        if ($q = $request->get('q')) {
            $query->whereRaw('LOWER(group_name) LIKE ?', ['%' . strtolower($q) . '%']);
        }

        $groups = $query->latest()->paginate(12)->withQueryString();

        // IDs of groups the current user has joined
        $myGroupIds = $user->groups()->pluck('groups.id')->all();

        return view('groups.index', compact('groups', 'myGroupIds', 'user'));
    }

    // ─────────────────────────────────────────────────────────────────
    //  CREATE / STORE
    // ─────────────────────────────────────────────────────────────────

    /** GET /groups/create */
    public function create()
    {
        return view('groups.create');
    }

    /** POST /groups */
    public function store(Request $request)
    {
        $data = $request->validate([
            'group_name' => 'required|string|max:100|unique:groups,group_name',
        ]);

        $group = Group::create([
            'group_name' => $data['group_name'],
            'created_by' => Auth::id(),
        ]);

        // Auto-join creator
        $group->members()->attach(Auth::id(), ['joined_at' => Carbon::now()]);

        return redirect()->route('groups.show', $group->id)
            ->with('success', 'Group created! You\'ve been added as the first member.');
    }

    // ─────────────────────────────────────────────────────────────────
    //  SHOW
    // ─────────────────────────────────────────────────────────────────

    /** GET /groups/{group} */
    public function show(Group $group)
    {
        $user    = Auth::user();
        $group->loadCount('members')->load('creator:id,first_name,last_name,profile_picture');

        $members = $group->members()
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.profile_picture', 'users.program', 'users.graduation_year')
            ->withPivot('joined_at')
            ->orderByPivot('joined_at')
            ->get();

        $isMember = $members->contains('id', $user->id);
        $isAdmin  = $group->created_by === $user->id;

        return view('groups.show', compact('group', 'members', 'isMember', 'isAdmin', 'user'));
    }

    // ─────────────────────────────────────────────────────────────────
    //  MEMBERS (JSON)
    // ─────────────────────────────────────────────────────────────────

    /** GET /groups/{group}/members */
    public function members(Group $group)
    {
        $members = $group->members()
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.profile_picture')
            ->get()
            ->map(fn($u) => [
                'id'     => $u->id,
                'name'   => $u->full_name,
                'avatar' => $u->avatar_url,
            ]);

        return response()->json($members);
    }

    // ─────────────────────────────────────────────────────────────────
    //  JOIN (addMember)
    // ─────────────────────────────────────────────────────────────────

    /** POST /groups/{group}/members */
    public function addMember(Request $request, Group $group)
    {
        $userId = $request->input('user_id', Auth::id());

        // Only group admin can add others; any member can add themselves
        if ($userId != Auth::id()) {
            abort_if($group->created_by !== Auth::id(), 403, 'Only the group admin can add members.');
            $request->validate(['user_id' => 'required|exists:users,id']);
        }

        if (!$group->members->contains($userId)) {
            $group->members()->attach($userId, ['joined_at' => Carbon::now()]);
        }

        if ($request->wantsJson()) {
            return response()->json(['joined' => true, 'count' => $group->members()->count()]);
        }

        return back()->with('success', 'Joined the group!');
    }

    // ─────────────────────────────────────────────────────────────────
    //  LEAVE / REMOVE (removeMember)
    // ─────────────────────────────────────────────────────────────────

    /** DELETE /groups/{group}/members/{user} */
    public function removeMember(Group $group, User $user)
    {
        $auth = Auth::user();

        // Can only remove yourself, or admin removes anyone (but not themselves)
        $isSelf  = $user->id === $auth->id;
        $isAdmin = $group->created_by === $auth->id;

        abort_if(!$isSelf && !$isAdmin, 403, 'Not authorised.');
        abort_if($isAdmin && $isSelf, 422, 'Creator cannot leave the group. Transfer ownership first.');

        $group->members()->detach($user->id);

        if (request()->wantsJson()) {
            return response()->json(['left' => true]);
        }

        return $isSelf
            ? redirect()->route('groups.index')->with('success', 'You left the group.')
            : back()->with('success', 'Member removed.');
    }
}
