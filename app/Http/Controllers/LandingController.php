<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    public function index()
    {
        // Redirect logged-in users straight to their dashboard
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        // Pull live stats for the landing page hero section
        $stats = [
            'alumni'      => User::where('status', 'approved')->count(),
            'connections' => DB::table('connections')->where('status', 'accepted')->count(),
            'events'      => Event::where('event_datetime', '>=', now())->count(),
        ];

        return view('landing', compact('stats'));
    }
}
