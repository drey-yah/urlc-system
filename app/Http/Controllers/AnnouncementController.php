<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\User;
use App\Mail\AnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with(['user', 'likes', 'comments.user', 'comments.replies.user'])
            ->latest()
            ->get();
        return view('announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('announcements', 'public');
        }

        $announcement = Announcement::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'image_path' => $imagePath,
        ]);

        // Send notifications to all users
        $users = User::all();
        foreach ($users as $user) {
            $user->notify(new \App\Notifications\NewAnnouncementNotification($announcement));
        }

        return redirect()->back()->with('success', 'Announcement published successfully!');
    }

    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $announcement->delete();

        return redirect()->back()->with('success', 'Announcement deleted successfully!');
    }
}
