<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Events\NotificationEvent;
use App\Models\AppNotification;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Throwable;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * GET /chat
     * ?user_id=123  -> filter with that user
     * ?q=keyword    -> search message text
     */
    public function index(Request $request)
    {
        $currentUser = $request->user();

        $q = ChatMessage::query()
            ->with(['sender:id,name,avatar_path', 'receiver:id,name,avatar_path']);

        $filteredUserId = null;

        if ($request->filled('user_id')) {
            $filteredUserId = (int) $request->input('user_id');

            $q->where(function ($qq) use ($filteredUserId, $currentUser) {
                $qq->where(function ($w) use ($filteredUserId, $currentUser) {
                        $w->where('sender_id', $currentUser->id)
                          ->where('receiver_id', $filteredUserId);
                    })
                  ->orWhere(function ($w) use ($filteredUserId, $currentUser) {
                        $w->where('sender_id', $filteredUserId)
                          ->where('receiver_id', $currentUser->id);
                    });
            });
        } else {
            $q->where(function ($w) use ($currentUser) {
                $w->where('sender_id', $currentUser->id)
                  ->orWhere('receiver_id', $currentUser->id);
            });
        }

        if ($request->filled('q')) {
            $term = (string) $request->input('q');
            $q->where('message', 'like', '%'.$term.'%');
        }

        // Oldest first
        $messages = $q->orderBy('created_at', 'asc')->get();

        // ===== Mark unread as read if a specific thread is opened =====
        if ($filteredUserId) {
            // All messages SENT BY peer TO me that are currently unread
            $unreadIds = ChatMessage::query()
                ->where('sender_id', $filteredUserId)
                ->where('receiver_id', $currentUser->id)
                ->whereNull('read_at')
                ->pluck('id');

            if ($unreadIds->isNotEmpty()) {
                ChatMessage::whereIn('id', $unreadIds)->update(['read_at' => now()]);

                // Broadcast read receipts to the sender so they can update badges
                try {
                    broadcast(new MessageRead(
                        readerId: $currentUser->id,
                        senderId: $filteredUserId,
                        messageIds: $unreadIds->all()
                    ))->toOthers();
                } catch (Throwable $e) {
                    // optional: logger()->warning('Broadcast read receipts failed', ['e'=>$e->getMessage()]);
                }
            }
        }

        // Users list (exclude me)
        $users = User::query()
            ->whereKeyNot($currentUser->id)
            ->orderBy('name')
            ->get(['id','name','avatar_path']);

        $conversationTitle = __('Conversation');
        if ($filteredUserId) {
            $target = $users->firstWhere('id', $filteredUserId) ?? User::find($filteredUserId);
            if ($target) {
                $conversationTitle = __('Direct Messages with :name', ['name' => $target->name]);
            }
        }

        return view('chat.chat_message', [
            'messages'          => $messages,
            'users'             => $users,
            'currentUser'       => $currentUser,
            'conversationTitle' => $conversationTitle,
        ]);
    }

    /**
     * POST /chat
     * body: message, receiver_id
     */
    public function store(Request $request)
    {
        $currentUserId = Auth::id();

        $data = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
            'receiver_id' => [
                'required','integer',
                Rule::exists('users', 'id'),
                Rule::notIn([$currentUserId]),
            ],
        ]);

        $msg = ChatMessage::create([
            'sender_id'   => $currentUserId,
            'receiver_id' => (int) $data['receiver_id'],
            'message'     => $data['message'],
        ]);

        $msg->load(['sender:id,name,avatar_path', 'receiver:id,name,avatar_path']);

        // 1) Broadcast the chat message to chat channels
        try {

            broadcast(new MessageSent($msg))->toOthers();
        } catch (Throwable $e) {
            // optional: return back()->with('error', $e->getMessage());
        }

        // 2) Broadcast a notification to the receiver's notifications channel
        try {
            $receiverId = (int) $msg->receiver_id;

            $payload = [
                'id'         => (int)$msg->id,
                'title'      => __($msg->message, ['name' => $msg->sender?->name ?? '']),
                'body'       => mb_strimwidth($msg->message, 0, 140, '…'),
                'icon'       => 'fas fa-comment-dots','user-id'=>$receiverId,
                'user_id'=> $receiverId,
                'link'       => route('chat.index', ['user_id' => $currentUserId]),
                'created_at' => optional($msg->created_at)


            ];
            AppNotification::create(attributes: $payload);
            broadcast(new NotificationEvent($receiverId, $payload))->toOthers();
        } catch (Throwable $e) {
            // optional: logger()->warning('Broadcast notification failed', ['e'=>$e->getMessage()]);
        }

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'id' => $msg->id, 'message' => $msg], 201);
        }

        return back();
    }
}
