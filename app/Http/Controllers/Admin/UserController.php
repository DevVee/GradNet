<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountApproved;
use App\Mail\AccountRejected;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /** GET /admin/users */
    public function index(Request $request)
    {
        $q      = $request->query('q');
        $status = $request->query('status', 'all');

        $query = User::where('role', 'user')->latest();

        if ($q) {
            $query->where(function ($sq) use ($q) {
                $sq->whereRaw("LOWER(first_name) LIKE ?", ['%' . strtolower($q) . '%'])
                   ->orWhereRaw("LOWER(last_name) LIKE ?", ['%' . strtolower($q) . '%'])
                   ->orWhereRaw("LOWER(email) LIKE ?", ['%' . strtolower($q) . '%']);
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $users = $query->paginate(20)->withQueryString();

        $counts = [
            'all'      => User::where('role', 'user')->count(),
            'pending'  => User::where('role', 'user')->where('status', 'pending')->count(),
            'approved' => User::where('role', 'user')->where('status', 'approved')->count(),
            'rejected' => User::where('role', 'user')->where('status', 'rejected')->count(),
        ];

        return view('admin.users.index', compact('users', 'counts', 'status'));
    }

    /** GET /admin/users/{user} */
    public function show(User $user)
    {
        $user->load(['posts' => fn($q) => $q->with('media')->latest()->limit(10)]);
        return view('admin.users.show', compact('user'));
    }

    /** PATCH /admin/users/{user}/approve */
    public function approve(User $user)
    {
        $this->approveUser($user);
        return back()->with('success', "{$user->full_name} has been approved.");
    }

    /** PATCH /admin/users/{user}/reject */
    public function reject(User $user)
    {
        $this->rejectUser($user);
        return back()->with('success', "{$user->full_name} has been rejected.");
    }

    /** DELETE /admin/users/{user} */
    public function destroy(User $user)
    {
        abort_if($user->role === 'admin', 403, 'Cannot delete admin accounts.');
        $name = $user->full_name;
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', "{$name} has been deleted.");
    }

    /** POST /admin/users/bulk-action */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action'     => 'required|in:approve,reject,delete',
            'user_ids'   => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $users = User::where('role', 'user')
                     ->whereIn('id', $request->user_ids)
                     ->get();

        $count = 0;
        foreach ($users as $user) {
            match ($request->action) {
                'approve' => $this->approveUser($user),
                'reject'  => $this->rejectUser($user),
                'delete'  => $user->delete(),
            };
            $count++;
        }

        $label = ['approve' => 'approved', 'reject' => 'rejected', 'delete' => 'deleted'][$request->action];

        return redirect()->route('admin.users.index')
            ->with('success', "{$count} user(s) {$label} successfully.");
    }

    // ── Private helpers ───────────────────────────────────────────────

    private function approveUser(User $user): void
    {
        $user->update(['status' => 'approved']);
        try {
            Mail::to($user->email)->send(new AccountApproved($user));
        } catch (\Throwable) {
            // Mail failure must never block the approval action
        }
    }

    private function rejectUser(User $user): void
    {
        $user->update(['status' => 'rejected']);
        try {
            Mail::to($user->email)->send(new AccountRejected($user));
        } catch (\Throwable) {
            // Mail failure must never block the rejection action
        }
    }
}
