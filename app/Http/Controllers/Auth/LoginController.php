<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /** GET /login */
    public function showForm()
    {
        return view('auth.login');
    }

    /** POST /login */
    public function login(LoginRequest $request)
    {
        $credential = trim($request->email_or_phone);

        // Find by email OR phone (matches legacy login.php logic)
        $user = User::where('email', $credential)
                    ->orWhere('phone', $credential)
                    ->first();

        // Unknown user
        if (!$user) {
            return back()
                ->withInput($request->only('email_or_phone', 'remember_me'))
                ->withErrors(['email_or_phone' => 'Invalid email/phone or password.']);
        }

        // Account not yet approved
        if ($user->status !== 'approved') {
            return back()
                ->withInput($request->only('email_or_phone', 'remember_me'))
                ->withErrors(['email_or_phone' => 'Your account is not approved yet. Please contact the administrator.']);
        }

        // Wrong password
        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('email_or_phone', 'remember_me'))
                ->withErrors(['email_or_phone' => 'Invalid email/phone or password.']);
        }

        // ✅ Success — log in
        Auth::login($user, $request->boolean('remember_me'));
        $request->session()->regenerate();

        // Redirect directly to dashboard — no welcome modal
        return redirect()->route('dashboard');
    }
}
