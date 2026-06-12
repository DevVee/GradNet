<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PushController extends Controller
{
    /**
     * POST /push/subscribe
     * Saves (or updates) the browser's push subscription for the current user.
     *
     * Expected JSON body:
     * {
     *   "endpoint": "https://...",
     *   "keys": { "p256dh": "...", "auth": "..." }
     * }
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint'       => 'required|string|url',
            'keys.p256dh'    => 'required|string',
            'keys.auth'      => 'required|string',
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint' => $request->endpoint],
            [
                'user_id'    => Auth::id(),
                'public_key' => $request->input('keys.p256dh'),
                'auth_token' => $request->input('keys.auth'),
            ]
        );

        return response()->json(['subscribed' => true]);
    }

    /**
     * POST /push/unsubscribe
     * Removes the push subscription by endpoint.
     */
    public function unsubscribe(Request $request)
    {
        $request->validate(['endpoint' => 'required|string']);

        PushSubscription::where('endpoint', $request->endpoint)
            ->where('user_id', Auth::id())
            ->delete();

        return response()->json(['unsubscribed' => true]);
    }
}
