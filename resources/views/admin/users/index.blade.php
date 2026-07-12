<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">User Management - Role Assignment</h2>
    </x-slot>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Current Role</th>
                            <th>Status</th>
                            <th>Assign New Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ $user->role == 'admin' ? 'bg-danger' : ($user->role == 'dean' ? 'bg-primary' : ($user->role == 'vprei' ? 'bg-dark' : ($user->role == 'reviewer' ? 'bg-info' : ($user->role == 'budget_officer' ? 'bg-warning text-dark' : 'bg-success')))) }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>
                            <td>
                                @if($user->is_approved)
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill small">
                                        <i class="bi bi-check-circle me-1"></i> Approved
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill small">
                                        <i class="bi bi-clock me-1"></i> Pending Approval
                                    </span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="form-select form-select-sm" style="width: 150px;">
                                        <option value="researcher" {{ $user->role == 'researcher' ? 'selected' : '' }}>Researcher</option>
                                        <option value="reviewer" {{ $user->role == 'reviewer' ? 'selected' : '' }}>Reviewer</option>
                                        <option value="coordinator" {{ $user->role == 'coordinator' ? 'selected' : '' }}>Coordinator</option>
                                        <option value="dean" {{ $user->role == 'dean' ? 'selected' : '' }}>Dean</option>
                                        <option value="vprei" {{ $user->role == 'vprei' ? 'selected' : '' }}>VPREI</option>
                                        <option value="budget_officer" {{ $user->role == 'budget_officer' ? 'selected' : '' }}>Budget Officer</option>
                                        <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff (Receiving)</option>
                                        <option value="recording_staff" {{ $user->role == 'recording_staff' ? 'selected' : '' }}>Staff (Recording)</option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                </form>
                            </td>
                            <td>
                                @if($user->id !== auth()->id() && !$user->isSuperAdmin())
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>

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
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
