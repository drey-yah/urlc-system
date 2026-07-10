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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ $user->role == 'admin' ? 'bg-danger' : ($user->role == 'dean' ? 'bg-primary' : ($user->role == 'vprei' ? 'bg-dark' : ($user->role == 'reviewer' ? 'bg-info' : 'bg-success'))) }}">
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
                                        <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff (Receiving)</option>
                                        <option value="recording_staff" {{ $user->role == 'recording_staff' ? 'selected' : '' }}>Staff (Recording)</option>
                                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
