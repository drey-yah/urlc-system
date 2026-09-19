<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ResearchProposal;
use App\Models\Announcement;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

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

        $recentUsers = User::latest()->take(5)->get();
        $recentProposals = ResearchProposal::with('user')->latest()->take(5)->get();

        return view('superadmin.dashboard', compact('stats', 'recentUsers', 'recentProposals'));
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

        $user->is_approved = \DB::raw('true');
        $user->save();

        return redirect()->back()->with('success', "Account for {$user->name} has been approved.");
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

    public function activityLogs(Request $request)
    {
        $query = $this->buildActivityLogsQuery($request);

        $logs = $query->paginate(20)->withQueryString();

        $users = User::orderBy('name')->get(['id', 'name', 'email', 'role']);

        $stats = [
            'total_logs' => Activity::count(),
            'today_logs' => Activity::whereDate('created_at', today())->count(),
            'active_users_today' => Activity::whereDate('created_at', today())
                ->whereNotNull('causer_id')
                ->distinct('causer_id')
                ->count('causer_id'),
            'today_logins' => Activity::whereDate('created_at', today())
                ->where(function($q) {
                    $q->where('event', 'login')
                      ->orWhere('description', 'like', '%logged in%');
                })->count(),
        ];

        return view('superadmin.activity_logs', compact('logs', 'users', 'stats'));
    }

    public function exportActivityLogs(Request $request)
    {
        $query = $this->buildActivityLogsQuery($request);
        $filename = 'activity_logs_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function() use ($query) {
            $handle = fopen('php://output', 'w');
            // CSV Header
            fputcsv($handle, ['ID', 'Date & Time', 'User Name', 'User Email', 'Role', 'Action / Event', 'Target Model', 'Target ID', 'Details / IP', 'Raw Properties']);

            $query->chunk(200, function($logs) use ($handle) {
                foreach ($logs as $log) {
                    $causerName = $log->causer ? $log->causer->name : 'System';
                    $causerEmail = $log->causer ? $log->causer->email : '-';
                    $causerRole = $log->causer ? $log->causer->role : '-';
                    $modelName = $log->subject_type ? class_basename($log->subject_type) : '-';
                    $subjectId = $log->subject_id ?: '-';

                    $details = '';
                    if ($log->properties && isset($log->properties['ip'])) {
                        $details = 'IP: ' . $log->properties['ip'];
                        if (isset($log->properties['user_agent'])) {
                            $details .= ' (' . substr($log->properties['user_agent'], 0, 50) . ')';
                        }
                    } elseif ($log->properties && isset($log->properties['attributes'])) {
                        $details = count($log->properties['attributes']) . ' field(s) modified';
                    }

                    fputcsv($handle, [
                        $log->id,
                        $log->created_at->format('Y-m-d H:i:s'),
                        $causerName,
                        $causerEmail,
                        $causerRole,
                        ucfirst($log->event ?? $log->description),
                        $modelName,
                        $subjectId,
                        $details,
                        json_encode($log->properties),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function buildActivityLogsQuery(Request $request)
    {
        $query = Activity::with('causer')->latest();

        // Filter by user (causer)
        if ($request->filled('user_id')) {
            $query->where('causer_type', User::class)
                  ->where('causer_id', $request->user_id);
        }

        // Filter by action / event
        if ($request->filled('event')) {
            $event = $request->event;
            if ($event === 'login') {
                $query->where(function($q) {
                    $q->where('event', 'login')
                      ->orWhere('description', 'like', '%logged in%');
                });
            } elseif ($event === 'logout') {
                $query->where(function($q) {
                    $q->where('event', 'logout')
                      ->orWhere('description', 'like', '%logged out%');
                });
            } else {
                $query->where(function($q) use ($event) {
                    $q->where('event', $event)
                      ->orWhere('description', $event);
                });
            }
        }

        // Date range filtering
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Keyword search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%")
                  ->orWhereHasMorph('causer', [User::class], function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }
}
