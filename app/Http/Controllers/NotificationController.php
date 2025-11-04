<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display notifications page
     */
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->with(['actor', 'notifiable'])
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($id);

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        auth()->user()
            ->notifications()
            ->unread()
            ->update(['read_at' => now()]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Get unread notifications count (API)
     */
    public function unreadCount()
    {
        $count = auth()->user()
            ->notifications()
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', auth()->id())
            ->findOrFail($id);

        $notification->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification deleted');
    }
}
