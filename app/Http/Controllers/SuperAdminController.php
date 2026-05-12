<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ResearchProposal;
use App\Models\Announcement;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_proposals' => ResearchProposal::count(),
            'pending_proposals' => ResearchProposal::where('status', 'pending')->count(),
            'active_announcements' => Announcement::count(),
            // Fixed for PostgreSQL boolean compatibility with emulated prepares
            'pending_admins' => User::where('role', 'admin')->whereRaw('is_approved = false')->count(),
        ];

        return view('superadmin.dashboard', compact('stats'));
    }

    public function users(Request $request)
    {
        $query = User::query();

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        if ($request->has('status') && $request->status != '') {
            // Fixed for PostgreSQL boolean compatibility with emulated prepares
            if ($request->status == 'approved') {
                $query->whereRaw('is_approved = true');
            } else {
                $query->whereRaw('is_approved = false');
            }
        }

        $users = $query->latest()->paginate(20);

        return view('superadmin.users', compact('users'));
    }

    public function approveAdmin($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role !== 'admin') {
            return redirect()->back()->with('error', 'Only Admin accounts require approval.');
        }

        $user->is_approved = \DB::raw('true');
        $user->save();

        return redirect()->back()->with('success', "Admin account for {$user->name} has been approved.");
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Cannot delete a Super Admin.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User removed from the system successfully.');
    }

    public function settings()
    {
        $settings = SystemSetting::all();
        return view('superadmin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->settings as $key => $value) {
            SystemSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'System settings updated successfully.');
    }
}
