<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Display inbox with list of conversations
     */
    public function index()
    {
        $userId = auth()->id();

        // Get all users that the current user has conversations with
        $conversations = Message::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender', 'receiver'])
            ->get()
            ->map(function ($message) use ($userId) {
                // Determine the other user in the conversation
                return $message->sender_id === $userId ? $message->receiver : $message->sender;
            })
            ->unique('id')
            ->values();

        // Get latest message and unread count for each conversation
        $conversationsData = $conversations->map(function ($user) use ($userId) {
            $latestMessage = Message::where(function ($query) use ($userId, $user) {
                $query->where('sender_id', $userId)->where('receiver_id', $user->id);
            })->orWhere(function ($query) use ($userId, $user) {
                $query->where('sender_id', $user->id)->where('receiver_id', $userId);
            })
            ->latest()
            ->first();

            $unreadCount = Message::where('sender_id', $user->id)
                ->where('receiver_id', $userId)
                ->where('is_read', false)
                ->count();

            return [
                'user' => $user,
                'latest_message' => $latestMessage,
                'unread_count' => $unreadCount,
            ];
        })->sortByDesc('latest_message.created_at')->values();

        return view('messages.inbox', compact('conversationsData'));
    }

    /**
     * Display chat with specific user
     */
    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $currentUser = auth()->user();

        // Get all messages between current user and the other user
        $messages = Message::where(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $currentUser->id)->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $user->id)->where('receiver_id', $currentUser->id);
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark messages from the other user as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $currentUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('messages.chat', compact('user', 'messages'));
    }

    /**
     * Store a new message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:5000',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
        ]);

        $message->load(['sender', 'receiver']);

        // Format time on server-side to avoid timezone issues
        $messageData = $message->toArray();
        $messageData['formatted_time'] = $message->created_at->format('H:i');

        // Format avatar URLs to full path
        if (isset($messageData['sender']['avatar']) && $messageData['sender']['avatar']) {
            $messageData['sender']['avatar'] = asset($messageData['sender']['avatar']);
        }
        if (isset($messageData['receiver']['avatar']) && $messageData['receiver']['avatar']) {
            $messageData['receiver']['avatar'] = asset($messageData['receiver']['avatar']);
        }

        return response()->json([
            'success' => true,
            'message' => $messageData,
        ]);
    }

    /**
     * Get new messages for a conversation (polling)
     */
    public function getMessages($username, Request $request)
    {
        $user = User::where('username', $username)->firstOrFail();
        $lastMessageId = $request->get('last_message_id', 0);
        $currentUser = auth()->user();

        $messages = Message::where(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $currentUser->id)->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($currentUser, $user) {
            $query->where('sender_id', $user->id)->where('receiver_id', $currentUser->id);
        })
        ->where('id', '>', $lastMessageId)
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark new messages from the other user as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $currentUser->id)
            ->where('id', '>', $lastMessageId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Format messages with server-side time to avoid timezone issues
        $messagesData = $messages->map(function ($message) {
            $data = $message->toArray();
            $data['formatted_time'] = $message->created_at->format('H:i');

            // Format avatar URLs to full path
            if (isset($data['sender']['avatar']) && $data['sender']['avatar']) {
                $data['sender']['avatar'] = asset($data['sender']['avatar']);
            }
            if (isset($data['receiver']['avatar']) && $data['receiver']['avatar']) {
                $data['receiver']['avatar'] = asset($data['receiver']['avatar']);
            }

            return $data;
        });

        return response()->json([
            'messages' => $messagesData,
        ]);
    }

    /**
     * Get unread messages count
     */
    public function unreadCount()
    {
        $count = Message::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
