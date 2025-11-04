<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    /**
     * Follow a user
     */
    public function store(Request $request, User $user)
    {
        $currentUser = auth()->user();

        // Prevent following yourself
        if ($currentUser->id === $user->id) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'You cannot follow yourself.'
                ], 400);
            }
            return back()->with('error', 'You cannot follow yourself.');
        }

        // Check if already following
        if ($currentUser->isFollowing($user->id)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'You are already following this user.',
                    'is_following' => true
                ], 400);
            }
            return back()->with('error', 'You are already following this user.');
        }

        $currentUser->following()->attach($user->id);

        // Create notification
        Notification::create([
            'user_id' => $user->id,
            'actor_id' => $currentUser->id,
            'type' => 'follow',
        ]);

        if ($request->wantsJson()) {
            // Get updated follower count
            $followerCount = $user->followers()->count();

            return response()->json([
                'message' => 'You are now following ' . $user->name,
                'is_following' => true,
                'follower_count' => $followerCount
            ]);
        }

        return back()->with('success', 'You are now following ' . $user->name);
    }

    /**
     * Unfollow a user
     */
    public function destroy(Request $request, User $user)
    {
        $currentUser = auth()->user();

        $currentUser->following()->detach($user->id);

        if ($request->wantsJson()) {
            // Get updated follower count
            $followerCount = $user->followers()->count();

            return response()->json([
                'message' => 'You have unfollowed ' . $user->name,
                'is_following' => false,
                'follower_count' => $followerCount
            ]);
        }

        return back()->with('success', 'You have unfollowed ' . $user->name);
    }
}
