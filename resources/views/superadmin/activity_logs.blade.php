<x-app-layout>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Activity & Audit Logs</h1>
            <p class="text-muted mb-0">Complete audit trail of user authentications, model updates, approvals, and system activities.</p>
        </div>
        <div>
            <a href="{{ route('superadmin.logs.export', request()->query()) }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export to CSV
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">Total Logged Events</span>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                            <i class="bi bi-journal-check h5 mb-0"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['total_logs']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">Today's Activities</span>
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                            <i class="bi bi-activity h5 mb-0"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['today_logs']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">Active Users Today</span>
                        <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                            <i class="bi bi-people h5 mb-0"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['active_users_today']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">Logins Today</span>
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2">
                            <i class="bi bi-box-arrow-in-right h5 mb-0"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($stats['today_logins']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Panel -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('superadmin.logs') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <!-- User Filter -->
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Filter by User</label>
                        <select name="user_id" class="form-select rounded-3">
                            <option value="">All Users</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->name }} ({{ ucfirst(str_replace('_', ' ', $u->role)) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action / Event Filter -->
                    <div class="col-12 col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">Action Type</label>
                        <select name="event" class="form-select rounded-3">
                            <option value="">All Actions</option>
                            <option value="login" {{ request('event') === 'login' ? 'selected' : '' }}>User Login</option>
                            <option value="logout" {{ request('event') === 'logout' ? 'selected' : '' }}>User Logout</option>
                            <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>

                    <!-- Date Range: From -->
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control rounded-3">
                    </div>

                    <!-- Date Range: To -->
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-semibold text-muted mb-1">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control rounded-3">
                    </div>

                    <!-- Keyword Search -->
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-semibold text-muted mb-1">Search Keywords</label>
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search action, name, IP..." class="form-control rounded-start-3">
                            <button type="submit" class="btn btn-primary px-3 rounded-end-3">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                @if(request()->anyFilled(['user_id', 'event', 'date_from', 'date_to', 'search']))
                    <div class="d-flex align-items-center gap-2 mt-3 pt-2 border-top">
                        <span class="small text-muted">Active filters:</span>
                        <a href="{{ route('superadmin.logs') }}" class="btn btn-sm btn-light border rounded-pill px-3 small">
                            <i class="bi bi-x-circle me-1 text-danger"></i>Clear all filters
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0 text-muted small text-uppercase fw-semibold" style="width: 170px;">Date & Time</th>
                        <th class="py-3 border-0 text-muted small text-uppercase fw-semibold" style="width: 220px;">User</th>
                        <th class="py-3 border-0 text-muted small text-uppercase fw-semibold" style="width: 140px;">Action</th>
                        <th class="py-3 border-0 text-muted small text-uppercase fw-semibold" style="width: 180px;">Target</th>
                        <th class="pe-4 py-3 border-0 text-muted small text-uppercase fw-semibold">Details & Changes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <!-- Date & Time -->
                            <td class="ps-4 py-3">
                                <div class="fw-semibold text-dark small">{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="text-muted small">{{ $log->created_at->format('h:i:s A') }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $log->created_at->diffForHumans() }}</div>
                            </td>

                            <!-- User (Causer) -->
                            <td>
                                @if($log->causer)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; font-size: 0.85rem;">
                                            {{ strtoupper(substr($log->causer->name, 0, 1)) }}
                                        </div>
                                        <div class="lh-sm">
                                            <div class="fw-bold text-dark small">{{ $log->causer->name }}</div>
                                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $log->causer->email }}</div>
                                            <span class="badge bg-light text-secondary border rounded-pill px-2 py-0 mt-1" style="font-size: 0.7rem;">
                                                {{ ucfirst(str_replace('_', ' ', $log->causer->role)) }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex align-items-center gap-2 text-muted">
                                        <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                            <i class="bi bi-gear-wide-connected"></i>
                                        </div>
                                        <span class="small fw-semibold">System / Automated</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Action / Event Badge -->
                            <td>
                                @php
                                    $action = strtolower($log->event ?? $log->description);
                                @endphp

                                @if($action === 'login' || str_contains(strtolower($log->description), 'logged in'))
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill small fw-semibold">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Logged In
                                    </span>
                                @elseif($action === 'logout' || str_contains(strtolower($log->description), 'logged out'))
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill small fw-semibold">
                                        <i class="bi bi-box-arrow-right me-1"></i>Logged Out
                                    </span>
                                @elseif($action === 'created')
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-semibold">
                                        <i class="bi bi-plus-circle me-1"></i>Created
                                    </span>
                                @elseif($action === 'updated')
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill small fw-semibold">
                                        <i class="bi bi-pencil me-1"></i>Updated
                                    </span>
                                @elseif($action === 'deleted')
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill small fw-semibold">
                                        <i class="bi bi-trash me-1"></i>Deleted
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small fw-semibold">
                                        {{ ucfirst($log->description) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Target Model -->
                            <td>
                                @if($log->log_name === 'auth')
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded small">
                                        <i class="bi bi-shield-lock me-1"></i>User Session
                                    </span>
                                @elseif($log->subject_type)
                                    @php
                                        $modelName = class_basename($log->subject_type);
                                    @endphp
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded small">
                                        <i class="bi bi-database me-1 text-muted"></i>{{ $modelName }} #{{ $log->subject_id }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <!-- Details & Changes -->
                            <td class="pe-4 text-start">
                                <!-- Auth Event Details (IP and Device) -->
                                @if(isset($log->properties['ip']))
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge bg-light text-dark border px-2 py-1 small">
                                            <i class="bi bi-geo-alt text-primary me-1"></i>IP: {{ $log->properties['ip'] }}
                                        </span>
                                        @if(!empty($log->properties['user_agent']))
                                            <span class="small text-muted text-truncate" style="max-width: 320px;" title="{{ $log->properties['user_agent'] }}">
                                                <i class="bi bi-laptop me-1"></i>{{ \Illuminate\Support\Str::limit($log->properties['user_agent'], 50) }}
                                            </span>
                                        @endif
                                    </div>
                                <!-- Model Changes Diff -->
                                @elseif(isset($log->properties['old']) && isset($log->properties['attributes']))
                                    <div class="small">
                                        @foreach($log->properties['attributes'] as $key => $newValue)
                                            @php
                                                $oldValue = $log->properties['old'][$key] ?? 'N/A';
                                                if (is_bool($oldValue)) $oldValue = $oldValue ? 'true' : 'false';
                                                if (is_bool($newValue)) $newValue = $newValue ? 'true' : 'false';
                                            @endphp
                                            <div class="mb-1">
                                                <strong class="text-muted">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> 
                                                <span class="text-danger text-decoration-line-through">{{ $oldValue }}</span> 
                                                <i class="bi bi-arrow-right mx-1 text-muted"></i> 
                                                <span class="text-success fw-semibold">{{ $newValue }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif(isset($log->properties['attributes']))
                                    <div class="small text-muted">
                                        <i class="bi bi-check2-circle text-success me-1"></i>{{ count($log->properties['attributes']) }} attribute(s) recorded.
                                    </div>
                                @else
                                    <span class="text-muted small">{{ $log->description }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-3 text-black-50"></i>
                                <div class="fw-semibold mb-1">No activity logs found</div>
                                <p class="small text-muted mb-0">Try adjusting your search criteria or clear your active filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
