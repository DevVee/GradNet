<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // ─────────────────────────────────────────────────────────────────
    //  LIST
    // ─────────────────────────────────────────────────────────────────

    /** GET /notifications */
    public function index()
    {
        $user = Auth::user();

        $notifications = AppNotification::where('user_id', $user->id)
            ->with(['actor:id,first_name,last_name,profile_picture', 'post:id'])
            ->orderByDesc('created_at')
            ->paginate(30);

        // Auto-mark all as read on page open
        AppNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('notifications.index', compact('notifications'));
    }

    // ─────────────────────────────────────────────────────────────────
    //  CHECK (AJAX — for badge count)
    // ─────────────────────────────────────────────────────────────────

    /** GET /notifications/check — returns unread count as JSON */
    public function check()
    {
        $count = AppNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    // ─────────────────────────────────────────────────────────────────
    //  MARK READ
    // ─────────────────────────────────────────────────────────────────

    /** POST /notifications/{id}/read */
    public function markRead(int $id)
    {
        AppNotification::where('id', $id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    /** POST /notifications/read-all */
    public function markAllRead()
    {
        AppNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }
}
