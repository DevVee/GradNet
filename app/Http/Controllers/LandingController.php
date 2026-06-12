<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class LandingController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        $stats = [
            'alumni'      => User::where('status', 'approved')->count(),
            'connections' => DB::table('connections')->where('status', 'accepted')->count(),
            'events'      => Event::count(),
            'posts'       => DB::table('posts')->count(),
            'news'        => DB::table('news')->count(),
        ];

        return view('landing', compact('stats'));
    }
}
