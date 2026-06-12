<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /** GET /profile/{user}  — view any profile */
    public function show(User $user)
    {
        $authUser = Auth::user();

        $posts = $user->posts()
            ->with(['media', 'reactions', 'comments'])
            ->orderByDesc('created_at')
            ->get()
            ->filter(fn($p) =>
                $p->user_id === $authUser->id ||  // own posts (if viewing self)
                $p->is_public                      // public
            )
            ->values();

        // Connection status between authUser and profileUser
        $connectionStatus = 'none'; // none | pending_sent | pending_received | accepted
        if ($authUser->id !== $user->id) {
            $conn = \App\Models\Connection::where(function ($q) use ($authUser, $user) {
                $q->where('follower_id', $authUser->id)->where('followed_id', $user->id);
            })->orWhere(function ($q) use ($authUser, $user) {
                $q->where('follower_id', $user->id)->where('followed_id', $authUser->id);
            })->first();

            if ($conn) {
                if ($conn->status === 'accepted') {
                    $connectionStatus = 'accepted';
                } elseif ($conn->follower_id === $authUser->id) {
                    $connectionStatus = 'pending_sent';
                } else {
                    $connectionStatus = 'pending_received';
                }
            }
        }

        $connectionCount = $user->connections()->accepted()->count();

        return view('profile.show', compact(
            'user', 'authUser', 'posts', 'connectionStatus', 'connectionCount'
        ));
    }

    /** GET /profile/edit */
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    /** PATCH /profile — update profile fields */
    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'middle_name'      => 'nullable|string|max:100',
            'suffix'           => 'nullable|string|max:20',
            'email'            => "required|email|max:255|unique:users,email,{$user->id}",
            'phone'            => 'nullable|string|max:20',
            'location'         => 'nullable|string|max:255',
            'workplace'        => 'nullable|string|max:255',
            'present_occupation'=> 'nullable|string|max:255',
            'program'          => 'nullable|string|max:100',
            'graduation_year'  => 'nullable|integer|min:1941|max:' . (date('Y') + 5),
            'facebook_account' => 'nullable|string|max:255',
            'facebook_link'    => 'nullable|url|max:255',
            'instagram_link'   => 'nullable|url|max:255',
            'linkedin_link'    => 'nullable|url|max:255',
            'comments'         => 'nullable|string|max:1000',
        ]);

        $user->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }

    /** POST /profile/picture — upload avatar */
    public function updatePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|max:4096|mimes:jpeg,jpg,png,webp,gif',
        ]);

        $user = Auth::user();

        // Delete old picture from storage (not the default logo)
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $path = $request->file('profile_picture')->store('avatars', 'public');
        $user->update(['profile_picture' => $path]);

        return back()->with('success', 'Profile picture updated!');
    }

    /** POST /profile/password — change password */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'  => 'required',
            'password'          => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully!');
    }
}
