<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Connection;
use App\Models\Event;
use App\Models\News;
use App\Models\Post;
use App\Models\User;

class AdminController extends Controller
{
    /** GET /admin */
    public function index()
    {
        // ── Core stat counts ──────────────────────────────────────────
        $stats = [
            'total_users'       => User::where('role', 'user')->count(),
            'pending_users'     => User::where('status', 'pending')->count(),
            'approved_users'    => User::where('role', 'user')->where('status', 'approved')->count(),
            'rejected_users'    => User::where('role', 'user')->where('status', 'rejected')->count(),
            'total_news'        => News::count(),
            'total_events'      => Event::count(),
            'total_posts'       => Post::count(),
            'total_connections' => Connection::where('status', 'accepted')->count(),
        ];

        // ── Pending approvals list (latest 10) ────────────────────────
        $pendingUsers = User::where('status', 'pending')
            ->select('id', 'first_name', 'last_name', 'email', 'program', 'graduation_year', 'created_at')
            ->latest()
            ->limit(10)
            ->get();

        // ── Monthly registrations — last 6 months ─────────────────────
        // Loop approach avoids SQLite ↔ PostgreSQL date-format differences.
        $monthlyRegistrations = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyRegistrations[] = [
                'label' => $month->format('M Y'),
                'count' => User::where('role', 'user')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }

        // ── Employment breakdown ──────────────────────────────────────
        $employmentBreakdown = User::where('role', 'user')
            ->where('status', 'approved')
            ->whereNotNull('employment_status')
            ->selectRaw('employment_status, count(*) as total')
            ->groupBy('employment_status')
            ->orderByDesc('total')
            ->get();

        // ── Top 5 programs ────────────────────────────────────────────
        $topPrograms = User::where('role', 'user')
            ->whereNotNull('program')
            ->selectRaw('program, count(*) as total')
            ->groupBy('program')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ── Recent activity ───────────────────────────────────────────
        $postsThisWeek   = Post::where('created_at', '>=', now()->subWeek())->count();
        $eventsThisMonth = Event::where('created_at', '>=', now()->startOfMonth())->count();

        return view('admin.dashboard', compact(
            'stats',
            'pendingUsers',
            'monthlyRegistrations',
            'employmentBreakdown',
            'topPrograms',
            'postsThisWeek',
            'eventsThisMonth'
        ));
    }
}
