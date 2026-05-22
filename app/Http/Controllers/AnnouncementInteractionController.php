<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementInteraction;
use Illuminate\Http\Request;

class AnnouncementInteractionController extends Controller
{
    public function like(Request $request, $id)
    {
        $userId = auth()->id();
        
        $existingLike = AnnouncementInteraction::where('announcement_id', $id)
            ->where('user_id', $userId)
            ->where('type', 'like')
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            AnnouncementInteraction::create([
                'user_id' => $userId,
                'announcement_id' => $id,
                'type' => 'like',
            ]);
            $liked = true;
        }

        if ($request->wantsJson() || $request->ajax()) {
            $likesCount = AnnouncementInteraction::where('announcement_id', $id)->where('type', 'like')->count();
            return response()->json(['liked' => $liked, 'likesCount' => $likesCount]);
        }

        return redirect()->back()->with('success', $liked ? 'Liked' : 'Unliked');
    }

    public function comment(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string',
            'parent_id' => 'nullable|exists:announcement_interactions,id',
        ]);

        AnnouncementInteraction::create([
            'user_id' => auth()->id(),
            'announcement_id' => $id,
            'type' => $request->parent_id ? 'reply' : 'comment',
            'body' => $request->body,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->back()->with('success', 'Comment posted');
    }
}
