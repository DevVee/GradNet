<?php

namespace App\Console\Commands;

use App\Mail\WeeklyDigest;
use App\Models\Event;
use App\Models\News;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklyDigest extends Command
{
    protected $signature   = 'alumni:weekly-digest';
    protected $description = 'Send the weekly digest email to all approved alumni';

    public function handle(): int
    {
        $this->info('Building weekly digest...');

        $upcomingEvents = Event::where('event_datetime', '>=', now())
            ->where('event_datetime', '<=', now()->addDays(7))
            ->orderBy('event_datetime')
            ->get(['id', 'title', 'event_datetime', 'location']);

        $latestNews = News::where('created_at', '>=', now()->subDays(7))
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'description']);

        $newAlumni = User::where('role', 'user')
            ->where('status', 'approved')
            ->where('created_at', '>=', now()->subDays(7))
            ->get(['id', 'first_name', 'last_name', 'program', 'graduation_year']);

        $this->info("  Events: {$upcomingEvents->count()} | News: {$latestNews->count()} | New alumni: {$newAlumni->count()}");

        $mailable = new WeeklyDigest($upcomingEvents, $latestNews, $newAlumni);

        $sent   = 0;
        $failed = 0;

        User::where('role', 'user')
            ->where('status', 'approved')
            ->select('id', 'email', 'first_name')
            ->chunk(100, function ($alumni) use ($mailable, &$sent, &$failed) {
                foreach ($alumni as $alumnus) {
                    try {
                        Mail::to($alumnus->email)->send($mailable);
                        $sent++;
                    } catch (\Throwable $e) {
                        $failed++;
                        $this->warn("  Failed → {$alumnus->email}: {$e->getMessage()}");
                    }
                }
            });

        $this->info("Weekly digest sent to {$sent} alumni. ({$failed} failed)");

        return self::SUCCESS;
    }
}
