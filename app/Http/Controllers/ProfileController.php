<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function show($id = null)
    {
        $user = $id ? User::findOrFail($id) : auth()->user();
        
        $data = [];
        $stats = [];

        if ($user->role === 'researcher') {
            $leadProposals = $user->leadProposals()->with('collaborators')->get();
            $collaboratedProposals = $user->collaboratedProposals()->with('user')->get();
            
            $stats = [
                'total' => $leadProposals->count() + $collaboratedProposals->count(),
                'lead' => $leadProposals->count(),
                'collaborated' => $collaboratedProposals->count(),
            ];
            $data = compact('leadProposals', 'collaboratedProposals');
        } elseif ($user->role === 'reviewer') {
            $assignedProposals = \App\Models\ResearchProposal::whereHas('assignments', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })->with('user')->get();

            $stats = [
                'total_assigned' => $assignedProposals->count(),
                'pending_review' => $assignedProposals->where('status', 'pending')->count(),
                'completed_review' => $assignedProposals->whereIn('status', ['approved', 'rejected', 'revision_required'])->count(),
            ];
            $data = compact('assignedProposals');
        } else { // Admin / Super Admin
            $stats = [
                'total_proposals' => \App\Models\ResearchProposal::count(),
                'total_users' => User::count(),
                'active_announcements' => \App\Models\Announcement::count(),
            ];
            $data = [
                'recent_proposals' => \App\Models\ResearchProposal::with('user')->latest()->take(5)->get()
            ];
        }

        return view('profile.show', array_merge(['user' => $user, 'stats' => $stats], $data));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'campus' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($request->only('name', 'email', 'campus', 'department'));

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')->with('success', 'Password changed successfully.');
    }
}
