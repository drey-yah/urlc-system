<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-1 fw-bold text-dark">User Management &amp; Role Privileges</h2>
                <p class="text-muted small mb-0">Assign primary and multi-role privileges (e.g., enable Evaluator / Reviewer for Researchers)</p>
            </div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-semibold">
                <i class="bi bi-people-fill me-1"></i> {{ $users->count() }} Registered Users
            </span>
        </div>
    </x-slot>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0">User Details</th>
                            <th class="py-3 border-0">Email</th>
                            <th class="py-3 border-0">Assigned Roles</th>
                            <th class="py-3 border-0">Status</th>
                            <th class="pe-4 py-3 border-0 text-end">Role Management &amp; Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white shadow-xs" 
                                         style="width: 38px; height: 38px; background: var(--ua-maroon, #8C1D2C); font-size: 0.85rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                                        @if($user->campus || $user->department)
                                            <div class="text-muted small" style="font-size: 0.75rem;">
                                                @if($user->campus)<i class="bi bi-geo-alt me-1 text-primary"></i>{{ $user->campus }}@endif
                                                @if($user->campus && $user->department) • @endif
                                                @if($user->department){{ $user->department }}@endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted small">{{ $user->email }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1.5 align-items-center">
                                    <!-- Primary Role Badge -->
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill small fw-semibold">
                                        <i class="bi bi-star-fill text-warning me-1" style="font-size: 0.62rem;"></i> {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>

                                    <!-- Additional Enabled Multi-Roles -->
                                    @if(!empty($user->additional_roles))
                                        @foreach($user->additional_roles as $extraRole)
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-0.5 rounded-pill" style="font-size: 0.72rem;">
                                                <i class="bi bi-plus-circle me-0.5"></i> {{ ucfirst(str_replace('_', ' ', $extraRole)) }}
                                            </span>
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($user->is_approved)
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-1.5 rounded-pill small">
                                        <i class="bi bi-check-circle me-1"></i> Approved
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-1.5 rounded-pill small">
                                        <i class="bi bi-clock me-1"></i> Pending Approval
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    <!-- Prominent Manage Roles Button -->
                                    <button type="button" 
                                            class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1.5 px-3 py-1.5 rounded-pill shadow-xs fw-semibold" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#manageRolesModal{{ $user->id }}"
                                            title="Assign or enable multiple roles">
                                        <i class="bi bi-person-gear"></i>
                                        <span>Roles</span>
                                    </button>

                                    @if($user->id !== auth()->id() && !$user->isSuperAdmin())
                                        <button class="btn btn-sm btn-outline-danger rounded-circle p-0 d-inline-flex align-items-center justify-content-center" 
                                                style="width: 32px; height: 32px;"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteUserModal{{ $user->id }}"
                                                title="Delete user">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Manage Roles Modal -->
                                <div class="modal fade" id="manageRolesModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg text-start" style="border-radius: 16px;">
                                            <form action="{{ route('admin.users.updateRoles', $user->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header border-0 px-4 pt-4 pb-2">
                                                    <div>
                                                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                                            <i class="bi bi-person-badge-fill text-primary"></i> Manage Roles &amp; Privileges
                                                        </h5>
                                                        <span class="text-muted small">{{ $user->name }} ({{ $user->email }})</span>
                                                    </div>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>

                                                <div class="modal-body px-4 py-3">
                                                    <!-- Primary Role -->
                                                    <div class="mb-3.5">
                                                        <label class="form-label small fw-bold text-dark text-uppercase" style="letter-spacing: 0.04em;">
                                                            Primary Role <span class="text-danger">*</span>
                                                        </label>
                                                        <select name="primary_role" class="form-select border-0 bg-light py-2 px-3 fw-semibold" required>
                                                            <option value="researcher" {{ $user->role == 'researcher' ? 'selected' : '' }}>🎓 Researcher</option>
                                                            <option value="reviewer" {{ $user->role == 'reviewer' ? 'selected' : '' }}>🔍 Evaluator / Peer Reviewer</option>
                                                            <option value="coordinator" {{ $user->role == 'coordinator' ? 'selected' : '' }}>📋 College Research Coordinator</option>
                                                            <option value="dean" {{ $user->role == 'dean' ? 'selected' : '' }}>🏛️ College Dean</option>
                                                            <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>📥 Support Staff (Receiving)</option>
                                                            <option value="recording_staff" {{ $user->role == 'recording_staff' ? 'selected' : '' }}>📝 Support Staff (Recording)</option>
                                                            <option value="budget_officer" {{ $user->role == 'budget_officer' ? 'selected' : '' }}>💰 Budget Officer</option>
                                                            <option value="sao_finance" {{ $user->role == 'sao_finance' ? 'selected' : '' }}>💳 Finance Officer</option>
                                                            <option value="vprei" {{ $user->role == 'vprei' ? 'selected' : '' }}>🎖️ VPREI</option>
                                                            <option value="president" {{ $user->role == 'president' ? 'selected' : '' }}>👔 SUC President</option>
                                                            <option value="funding_agency" {{ $user->role == 'funding_agency' ? 'selected' : '' }}>🏢 Funding Agency</option>
                                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>🛡️ Research Director / Admin</option>
                                                        </select>
                                                        <div class="form-text" style="font-size: 0.72rem;">The default landing portal when this user logs in.</div>
                                                    </div>

                                                    <!-- Additional Authorized Roles (Multi-Role) -->
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold text-dark text-uppercase d-flex justify-content-between align-items-center" style="letter-spacing: 0.04em;">
                                                            <span>Additional Enabled Roles</span>
                                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-normal" style="font-size: 0.68rem;">Multi-Role Privilege</span>
                                                        </label>
                                                        <p class="text-muted small mb-2" style="font-size: 0.74rem;">
                                                            Check the roles this user is authorized to switch between. For example, check <strong>Evaluator / Peer Reviewer</strong> so this Researcher can also evaluate proposals.
                                                        </p>

                                                        @php
                                                            $currentExtra = is_array($user->additional_roles) ? $user->additional_roles : [];
                                                            $availableRoles = [
                                                                'reviewer' => ['name' => 'Evaluator / Peer Reviewer', 'icon' => 'bi-clipboard-check-fill', 'desc' => 'Allows reviewing & scoring assigned proposals (RESU-FM-001)'],
                                                                'researcher' => ['name' => 'Researcher', 'icon' => 'bi-mortarboard-fill', 'desc' => 'Allows authoring & submitting research proposals'],
                                                                'coordinator' => ['name' => 'College Coordinator', 'icon' => 'bi-check-circle-fill', 'desc' => 'Allows endorsing college proposals (RESU-FM-003)'],
                                                                'dean' => ['name' => 'College Dean', 'icon' => 'bi-bank2', 'desc' => 'Allows executive noting of college endorsements'],
                                                                'staff' => ['name' => 'Receiving Staff', 'icon' => 'bi-inbox-fill', 'desc' => 'Allows administrative completeness checking'],
                                                                'recording_staff' => ['name' => 'Recording Staff', 'icon' => 'bi-journal-text', 'desc' => 'Allows tracking & logging research outputs'],
                                                                'budget_officer' => ['name' => 'Budget Officer', 'icon' => 'bi-cash-stack', 'desc' => 'Allows certifying availability of funds'],
                                                                'sao_finance' => ['name' => 'Finance Officer', 'icon' => 'bi-bank', 'desc' => 'Allows processing purchase requests'],
                                                                'admin' => ['name' => 'Research Director / Admin', 'icon' => 'bi-shield-check', 'desc' => 'Full research unit administrative review access'],
                                                            ];
                                                        @endphp

                                                        <div class="border rounded-3 p-2 bg-light bg-opacity-50" style="max-height: 280px; overflow-y: auto;">
                                                            @foreach($availableRoles as $roleKey => $info)
                                                                @if($roleKey === $user->role)
                                                                    @continue
                                                                @endif
                                                                <div class="d-flex align-items-start gap-2.5 p-2.5 rounded-3 mb-1.5 border bg-white shadow-xs" style="border-color: rgba(0,0,0,0.06) !important; transition: all 0.15s ease;">
                                                                    <input class="form-check-input m-0 mt-0.5 flex-shrink-0" 
                                                                           type="checkbox" 
                                                                           name="additional_roles[]" 
                                                                           value="{{ $roleKey }}" 
                                                                           id="extra_role_admin_{{ $user->id }}_{{ $roleKey }}"
                                                                           style="cursor: pointer; width: 1.15rem; height: 1.15rem; float: none; margin-left: 0 !important;"
                                                                           {{ in_array($roleKey, $currentExtra) ? 'checked' : '' }}>
                                                                    <label class="form-check-label flex-grow-1 user-select-none m-0" for="extra_role_admin_{{ $user->id }}_{{ $roleKey }}" style="cursor: pointer;">
                                                                        <div class="fw-bold small text-dark d-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                                                                            <i class="bi {{ $info['icon'] }} text-primary"></i> {{ $info['name'] }}
                                                                        </div>
                                                                        <div class="text-muted" style="font-size: 0.72rem; line-height: 1.3;">{{ $info['desc'] }}</div>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer border-0 px-4 pb-4 pt-1 d-flex justify-content-between">
                                                    <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                                        <i class="bi bi-check2-circle me-1"></i> Save Role Privileges
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                            <div class="modal-header border-0 px-4 pt-4 pb-0">
                                                <h5 class="fw-bold text-danger">Remove User?</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4 text-start">
                                                <p class="mb-0">Are you sure you want to remove <strong>{{ $user->name }}</strong> from the system? This action cannot be undone and will delete all associated data.</p>
                                            </div>
                                            <div class="modal-footer border-0 p-4 pt-0">
                                                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger px-4">Delete User</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
