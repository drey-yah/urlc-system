<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $folder = $request->query('folder', 'inbox'); // 'inbox' or 'sent'
        $selectedId = $request->query('message');

        if ($folder === 'sent') {
            $messagesQuery = Message::with(['recipient', 'replies.sender'])
                ->where('sender_id', $user->id)
                ->whereNull('parent_id')
                ->latest();
        } else {
            $messagesQuery = Message::with(['sender', 'replies.sender'])
                ->where('recipient_id', $user->id)
                ->whereNull('parent_id')
                ->latest();
        }

        $messages = $messagesQuery->get();

        $selectedMessage = null;
        if ($selectedId) {
            $selectedMessage = Message::with(['sender', 'recipient', 'replies.sender', 'replies.recipient'])->find($selectedId);
        } elseif ($messages->isNotEmpty()) {
            $selectedMessage = $messages->first();
        }

        if ($selectedMessage && $selectedMessage->recipient_id === $user->id && !$selectedMessage->is_read) {
            $selectedMessage->update(['is_read' => \Illuminate\Support\Facades\DB::raw('true')]);
        }

        // Available users for composing messages
        $users = User::where('id', '!=', $user->id)->orderBy('name')->get();

        $unreadCount = Message::where('recipient_id', $user->id)->whereRaw('is_read = false')->count();

        return view('messages.index', compact('messages', 'selectedMessage', 'folder', 'users', 'unreadCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $request->recipient_id,
            'subject' => $request->subject,
            'body' => $request->body,
        ]);

        return redirect()->route('messages.index', ['folder' => 'sent'])
            ->with('success', 'Message sent successfully!');
    }

    public function reply(Request $request, $id)
    {
        $parentMessage = Message::findOrFail($id);
        
        // Ensure user is involved in this message
        if ($parentMessage->sender_id !== auth()->id() && $parentMessage->recipient_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'body' => 'required|string',
        ]);

        $recipientId = ($parentMessage->sender_id === auth()->id()) 
            ? $parentMessage->recipient_id 
            : $parentMessage->sender_id;

        Message::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $recipientId,
            'subject' => 'Re: ' . preg_replace('/^(Re:\s*)+/i', '', $parentMessage->subject),
            'body' => $request->body,
            'parent_id' => $parentMessage->id,
        ]);

        // Mark parent message as unread for the recipient
        $parentMessage->update(['is_read' => \Illuminate\Support\Facades\DB::raw('false')]);

        return redirect()->route('messages.index', ['message' => $parentMessage->id, 'folder' => $request->query('folder', 'inbox')])
            ->with('success', 'Reply sent successfully!');
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);

        if ($message->sender_id !== auth()->id() && $message->recipient_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $message->delete();

        return redirect()->route('messages.index')->with('success', 'Message deleted successfully!');
    }
}
