<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\User;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    /**
     * Block a user
     */
    public function store(User $user)
    {
        // Check if user is trying to block themselves
        if ($user->id === auth()->id()) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'You cannot block yourself.',
                    'success' => false,
                ], 400);
            }
            return back()->with('error', 'You cannot block yourself.');
        }

        // Check if already blocked
        if (auth()->user()->hasBlocked($user->id)) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'User is already blocked.',
                    'success' => false,
                ], 400);
            }
            return back()->with('error', 'User is already blocked.');
        }

        Block::create([
            'blocker_id' => auth()->id(),
            'blocked_id' => $user->id,
        ]);

        // Also unfollow if following
        if (auth()->user()->isFollowing($user->id)) {
            auth()->user()->following()->detach($user->id);
        }

        // Remove follower if they follow you
        if ($user->isFollowing(auth()->id())) {
            $user->following()->detach(auth()->id());
        }

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'User blocked successfully.',
                'success' => true,
                'is_blocked' => true,
            ]);
        }

        return back()->with('success', 'User blocked successfully.');
    }

    /**
     * Unblock a user
     */
    public function destroy(User $user)
    {
        $block = Block::where('blocker_id', auth()->id())
            ->where('blocked_id', $user->id)
            ->first();

        if (!$block) {
            if (request()->wantsJson()) {
                return response()->json([
                    'message' => 'User is not blocked.',
                    'success' => false,
                ], 400);
            }
            return back()->with('error', 'User is not blocked.');
        }

        $block->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'User unblocked successfully.',
                'success' => true,
                'is_blocked' => false,
            ]);
        }

        return back()->with('success', 'User unblocked successfully.');
    }
}
