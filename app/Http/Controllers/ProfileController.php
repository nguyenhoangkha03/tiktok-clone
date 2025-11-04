<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display public user profile by username
     */
    public function show($username): View
    {
        // Load user with follower/following counts for better performance
        $user = User::where('username', $username)
            ->withCount(['followers', 'following', 'videos'])
            ->firstOrFail();

        // Eager load relationships to avoid N+1
        $videos = $user->videos()
            ->with('user')
            ->withCount(['likes', 'comments', 'favorites'])
            ->latest()
            ->paginate(12, ['*'], 'videos_page');

        // Get liked videos with user info
        $likedVideos = $user->likedVideos()
            ->with('user')
            ->withCount(['likes', 'comments', 'favorites'])
            ->latest('likes.created_at')
            ->paginate(12, ['*'], 'liked_page');

        $followersCount = $user->followers_count;
        $followingCount = $user->following_count;

        // Calculate total likes from all user's videos
        $totalLikes = $user->videos()->withCount('likes')->get()->sum('likes_count');

        // Get actual followers and following lists for modal
        $followers = $user->followers()->get();
        $following = $user->following()->get();

        // Check if current user is following (only if authenticated)
        $isFollowing = auth()->check() ? auth()->user()->isFollowing($user->id) : false;

        return view('profile.show', compact('user', 'videos', 'likedVideos', 'followersCount', 'followingCount', 'totalLikes', 'followers', 'following', 'isFollowing'));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Update other fields first (excluding avatar)
        $validated = $request->validated();

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            $avatar = $request->file('avatar');
            $filename = time() . '_' . $user->id . '.' . $avatar->getClientOriginalExtension();

            // Create avatars directory if not exists
            if (!file_exists(public_path('avatars'))) {
                mkdir(public_path('avatars'), 0777, true);
            }

            $avatar->move(public_path('avatars'), $filename);

            // Set avatar path and remove from validated array
            $validated['avatar'] = 'avatars/' . $filename;
        } else {
            // Remove avatar from validated if no file uploaded
            unset($validated['avatar']);
        }

        // Fill user with validated data
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Redirect back to profile show page with success message
        return Redirect::route('profile.show', ['username' => $user->username])
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
