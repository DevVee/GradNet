<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Conversation;
use App\Models\Group;
use App\Models\Message;
use App\Models\MessageReaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    // ─────────────────────────────────────────────────────────────────
    //  CONVERSATION LIST
    // ─────────────────────────────────────────────────────────────────

    /** GET /messages */
    public function index()
    {
        $user = Auth::user();

        // Direct message conversations
        $conversations = Conversation::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->with([
                'user1:id,first_name,last_name,profile_picture',
                'user2:id,first_name,last_name,profile_picture',
                'latestMessage',
            ])
            ->orderByDesc(
                Message::select('sent_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest('sent_at')
                    ->limit(1)
            )
            ->get();

        // Group chats the user is in
        $groups = $user->groups()->with('latestMessage')->get();

        // Ensure batch group chat exists
        $this->ensureBatchGroup($user);

        return view('messages.index', compact('conversations', 'groups', 'user'));
    }

    // ─────────────────────────────────────────────────────────────────
    //  SHOW / SEND (DM)
    // ─────────────────────────────────────────────────────────────────

    /** GET /messages/{conversation} */
    public function show(Conversation $conversation)
    {
        $user = Auth::user();
        $this->authorizeConversation($conversation, $user->id);

        $messages = $conversation->messages()
            ->with(['sender:id,first_name,last_name,profile_picture', 'replyTo.sender', 'attachments'])
            ->orderBy('sent_at')
            ->get();

        $other = $conversation->otherUser($user->id);

        return view('messages.show', compact('conversation', 'messages', 'other', 'user'));
    }

    /** POST /messages/{conversation} — send a message */
    public function send(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        $this->authorizeConversation($conversation, $user->id);

        $request->validate([
            'content'    => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|max:10240|mimes:jpeg,jpg,png,gif,webp,mp4,mov',
        ]);

        abort_if(!$request->filled('content') && !$request->hasFile('attachment'), 422, 'Empty message.');

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $user->id,
            'content'         => $request->input('content', ''),
            'sent_at'         => Carbon::now(),
        ]);

        // Store attachment if present
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('attachments', 'public');
            $type = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image';
            Attachment::create([
                'message_id' => $message->id,
                'file_path'  => $path,
                'file_type'  => $type,
                'file_name'  => $file->getClientOriginalName(),
            ]);
        }

        $conversation->touch();

        $message->load(['sender:id,first_name,last_name,profile_picture', 'attachments']);

        return response()->json($this->formatMessage($message, $user->id));
    }

    // ─────────────────────────────────────────────────────────────────
    //  AJAX POLL
    // ─────────────────────────────────────────────────────────────────

    /** GET /messages/{conversation}/poll?last_message_id=X */
    public function poll(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        $this->authorizeConversation($conversation, $user->id);

        $lastId = (int) $request->get('last_message_id', 0);

        $messages = Message::where('conversation_id', $conversation->id)
            ->where('id', '>', $lastId)
            ->with(['sender:id,first_name,last_name,profile_picture', 'attachments'])
            ->orderBy('sent_at')
            ->get()
            ->map(fn($m) => $this->formatMessage($m, $user->id));

        return response()->json(['messages' => $messages]);
    }

    // ─────────────────────────────────────────────────────────────────
    //  START / DELETE CONVERSATION
    // ─────────────────────────────────────────────────────────────────

    /** POST /messages/new — start a new DM */
    public function startConversation(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $user = Auth::user();

        abort_if($request->user_id == $user->id, 422, 'Cannot message yourself.');

        $existing = Conversation::where(function ($q) use ($user, $request) {
            $q->where('user1_id', $user->id)->where('user2_id', $request->user_id);
        })->orWhere(function ($q) use ($user, $request) {
            $q->where('user1_id', $request->user_id)->where('user2_id', $user->id);
        })->first();

        if ($existing) return redirect()->route('messages.show', $existing->id);

        $convo = Conversation::create([
            'user1_id' => $user->id,
            'user2_id' => $request->user_id,
        ]);

        return redirect()->route('messages.show', $convo->id);
    }

    /** DELETE /messages/{conversation} */
    public function deleteConversation(Conversation $conversation)
    {
        $user = Auth::user();
        $this->authorizeConversation($conversation, $user->id);

        // Mark as deleted for this user (soft-delete approach)
        DB::table('deleted_conversations')->insertOrIgnore([
            'conversation_id' => $conversation->id,
            'user_id'         => $user->id,
        ]);

        return redirect()->route('messages.index')->with('success', 'Conversation removed.');
    }

    /** GET /messages/search — search users to start a DM */
    public function searchUsers(Request $request)
    {
        $q     = $request->get('q', '');
        $users = User::where('id', '!=', Auth::id())
            ->where('status', 'approved')
            ->where(function ($query) use ($q) {
                $query->whereRaw("LOWER(first_name) LIKE ?", ['%' . strtolower($q) . '%'])
                      ->orWhereRaw("LOWER(last_name) LIKE ?",  ['%' . strtolower($q) . '%']);
            })
            ->select('id', 'first_name', 'last_name', 'profile_picture')
            ->limit(8)
            ->get()
            ->map(fn($u) => [
                'id'     => $u->id,
                'name'   => $u->full_name,
                'avatar' => $u->avatar_url,
            ]);

        return response()->json($users);
    }

    // ─────────────────────────────────────────────────────────────────
    //  GROUP CHAT
    // ─────────────────────────────────────────────────────────────────

    /** GET /messages/groups/{group} */
    public function showGroup(Group $group)
    {
        $user = Auth::user();
        abort_if(!$group->members->contains($user->id), 403, 'You are not a member of this group.');

        $messages = Message::where('group_id', $group->id)
            ->with('sender:id,first_name,last_name,profile_picture')
            ->orderBy('sent_at')
            ->get();

        return view('messages.group', compact('group', 'messages', 'user'));
    }

    /** POST /messages/groups/{group} */
    public function sendGroup(Request $request, Group $group)
    {
        $user = Auth::user();
        abort_if(!$group->members->contains($user->id), 403);
        $request->validate(['content' => 'required|string|max:5000']);

        $message = Message::create([
            'group_id'  => $group->id,
            'sender_id' => $user->id,
            'content'   => $request->content,
            'sent_at'   => Carbon::now(),
        ]);

        $message->load('sender:id,first_name,last_name,profile_picture');

        return response()->json($this->formatMessage($message, $user->id));
    }

    /** GET /messages/groups/{group}/poll */
    public function pollGroup(Request $request, Group $group)
    {
        $user = Auth::user();
        abort_if(!$group->members->contains($user->id), 403);

        $lastId = (int) $request->get('last_message_id', 0);

        $messages = Message::where('group_id', $group->id)
            ->where('id', '>', $lastId)
            ->with('sender:id,first_name,last_name,profile_picture')
            ->orderBy('sent_at')
            ->get()
            ->map(fn($m) => $this->formatMessage($m, $user->id));

        return response()->json(['messages' => $messages]);
    }

    // ─────────────────────────────────────────────────────────────────
    //  REACT TO A MESSAGE
    // ─────────────────────────────────────────────────────────────────

    /** POST /messages/react/{message} */
    public function react(Request $request, Message $message)
    {
        $request->validate(['reaction' => 'required|string|max:20']);
        $user = Auth::user();

        $existing = MessageReaction::where('message_id', $message->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            if ($existing->reaction === $request->reaction) {
                $existing->delete();
                $action = 'removed';
            } else {
                $existing->update(['reaction' => $request->reaction]);
                $action = 'changed';
            }
        } else {
            MessageReaction::create([
                'message_id' => $message->id,
                'user_id'    => $user->id,
                'reaction'   => $request->reaction,
            ]);
            $action = 'added';
        }

        $counts = MessageReaction::where('message_id', $message->id)
            ->selectRaw('reaction, COUNT(*) as count')
            ->groupBy('reaction')
            ->pluck('count', 'reaction');

        return response()->json(['action' => $action, 'reactions' => $counts]);
    }

    // ─────────────────────────────────────────────────────────────────
    //  STANDALONE ATTACHMENT UPLOAD (returns JSON with URL)
    // ─────────────────────────────────────────────────────────────────

    /** POST /messages/attachment */
    public function uploadAttachment(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:jpeg,jpg,png,gif,webp,mp4,mov,pdf,doc,docx',
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');
        $type = str_starts_with($file->getMimeType(), 'video/') ? 'video'
              : (str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file');

        return response()->json([
            'url'       => Storage::url($path),
            'path'      => $path,
            'file_type' => $type,
            'file_name' => $file->getClientOriginalName(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    //  HELPERS
    // ─────────────────────────────────────────────────────────────────

    private function authorizeConversation(Conversation $c, int $userId): void
    {
        abort_if($c->user1_id !== $userId && $c->user2_id !== $userId, 403);
    }

    private function formatMessage(Message $m, int $myId): array
    {
        return [
            'id'          => $m->id,
            'content'     => $m->content,
            'sent_at'     => $m->sent_at->format('h:i A'),
            'is_mine'     => $m->sender_id === $myId,
            'sender'      => [
                'id'     => $m->sender->id,
                'name'   => $m->sender->first_name,
                'avatar' => $m->sender->avatar_url,
            ],
            'attachments' => $m->relationLoaded('attachments')
                ? $m->attachments->map(fn($a) => [
                    'url'       => $a->url,
                    'file_type' => $a->file_type,
                    'file_name' => $a->file_name,
                ])->all()
                : [],
        ];
    }

    private function ensureBatchGroup(User $user): void
    {
        if (!$user->program || !$user->graduation_year) return;

        $groupName = $user->program . ' ' . $user->graduation_year;

        DB::transaction(function () use ($user, $groupName) {
            $group = Group::firstOrCreate(
                ['group_name' => $groupName],
                ['created_by' => $user->id]
            );

            // Add user to group if not already member
            if (!$group->members->contains($user->id)) {
                $group->members()->attach($user->id, ['joined_at' => Carbon::now()]);
            }
        });
    }
}
