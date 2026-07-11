<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,reviewer,researcher,coordinator,staff,recording_staff,dean,vprei',
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->is_approved = \DB::raw('true');
        $user->save();

        return redirect()->back()->with('success', "Role for {$user->name} updated to {$user->role} successfully.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Cannot delete your own account.');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Cannot delete a Super Admin.');
        }

        $user->delete();

        return redirect()->back()->with('success', "User {$user->name} has been deleted successfully.");
    }
}
