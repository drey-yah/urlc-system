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
            'role' => 'required|in:admin,reviewer,researcher,coordinator,staff,recording_staff,dean,vprei,budget_officer,sao_finance,president,funding_agency',
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->is_approved = \DB::raw('true');
        $user->save();

        return redirect()->back()->with('success', "Role for {$user->name} updated to {$user->role} successfully.");
    }

    public function updateRoles(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $allowedRoles = [
            'researcher', 'reviewer', 'coordinator', 'dean', 
            'staff', 'recording_staff', 'budget_officer', 
            'sao_finance', 'vprei', 'president', 'funding_agency', 'admin'
        ];

        $request->validate([
            'primary_role' => 'required|in:' . implode(',', $allowedRoles),
            'additional_roles' => 'nullable|array',
            'additional_roles.*' => 'in:' . implode(',', $allowedRoles),
        ]);

        $primaryRole = $request->primary_role;
        $additionalRoles = $request->additional_roles ?? [];

        // Exclude primary role from additional_roles to prevent duplicates
        $additionalRoles = array_values(array_diff($additionalRoles, [$primaryRole]));

        $user->update([
            'role' => $primaryRole,
            'additional_roles' => !empty($additionalRoles) ? $additionalRoles : null,
            'is_approved' => \DB::raw('true'),
        ]);

        return redirect()->back()->with('success', "Roles for {$user->name} have been updated successfully.");
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
