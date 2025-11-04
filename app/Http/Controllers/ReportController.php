<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Store a new report
     */
    public function store(Request $request, User $user)
    {
        // Check if user is trying to report themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot report yourself.');
        }

        // Check if already reported
        if (auth()->user()->hasReported($user->id)) {
            return back()->with('error', 'You have already reported this user.');
        }

        $validated = $request->validate([
            'reason' => 'required|string|in:spam,harassment,inappropriate,fake_account,other',
            'description' => 'nullable|string|max:500',
        ]);

        Report::create([
            'reporter_id' => auth()->id(),
            'reported_user_id' => $user->id,
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'User reported successfully. We will review this report.',
                'success' => true,
            ]);
        }

        return back()->with('success', 'User reported successfully. We will review this report.');
    }

    /**
     * Get all reports (admin only - optional)
     */
    public function index()
    {
        // This can be used for admin panel later
        $reports = Report::with(['reporter', 'reportedUser'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
    }
}
