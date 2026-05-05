<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementInteraction;
use Illuminate\Http\Request;

class AnnouncementInteractionController extends Controller
{
    public function like($id)
    {
        $userId = auth()->id();
        
        $existingLike = AnnouncementInteraction::where('announcement_id', $id)
            ->where('user_id', $userId)
            ->where('type', 'like')
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            return redirect()->back()->with('success', 'Unliked');
        }

        AnnouncementInteraction::create([
            'user_id' => $userId,
            'announcement_id' => $id,
            'type' => 'like',
        ]);

        return redirect()->back()->with('success', 'Liked');
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
