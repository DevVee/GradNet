<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\News;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /** GET /dashboard */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        // ── Batchmates (same program + graduation_year, up to 7) ────────
        $canFetchBatchmates = $user->program && $user->graduation_year;
        $batchmates         = collect();
        $batchmatesTitle    = '(Set your program and year to find batchmates)';

        if ($canFetchBatchmates) {
            $batchmates = User::where('program', $user->program)
                ->where('graduation_year', $user->graduation_year)
                ->where('id', '!=', $user->id)
                ->where('status', 'approved')
                ->select('id', 'first_name', 'last_name', 'profile_picture', 'program', 'graduation_year')
                ->limit(7)
                ->get();

            $batchmatesTitle = '(' . $user->program . ' ' . $user->graduation_year . ')';
        }

        // ── News (latest 3) ─────────────────────────────────────────────
        $news = News::orderByDesc('created_at')->limit(3)->get();

        // ── Events ──────────────────────────────────────────────────────
        $upcomingEvents = Event::where('event_datetime', '>=', now())
            ->orderBy('event_datetime')
            ->limit(3)
            ->get();

        $previousEvents = Event::where('event_datetime', '<', now())
            ->orderByDesc('event_datetime')
            ->limit(3)
            ->get();

        return view('dashboard.index', compact(
            'user',
            'canFetchBatchmates',
            'batchmates',
            'batchmatesTitle',
            'news',
            'upcomingEvents',
            'previousEvents'
        ));
    }
}
