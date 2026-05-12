<x-app-layout>
    <div class="row justify-content-center py-5">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white p-4 border-0">
                    <h4 class="fw-bold mb-0">Profile Information</h4>
                    <p class="text-muted small mb-0">Update your account's profile information and email address.</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf @method('PUT')
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Full Name</label>
                                <input type="text" name="name" class="form-control border-0 bg-light py-2 px-3 rounded-3" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                                <input type="email" name="email" class="form-control border-0 bg-light py-2 px-3 rounded-3" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Campus</label>
                                <input type="text" name="campus" class="form-control border-0 bg-light py-2 px-3 rounded-3" value="{{ old('campus', $user->campus) }}" placeholder="e.g. Main Campus">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Department</label>
                                <input type="text" name="department" class="form-control border-0 bg-light py-2 px-3 rounded-3" value="{{ old('department', $user->department) }}" placeholder="e.g. College of Engineering">
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white p-4 border-0">
                    <h4 class="fw-bold mb-0 text-danger">Update Password</h4>
                    <p class="text-muted small mb-0">Ensure your account is using a long, random password to stay secure.</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Current Password</label>
                            <input type="password" name="current_password" class="form-control border-0 bg-light py-2 px-3 rounded-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">New Password</label>
                            <input type="password" name="password" class="form-control border-0 bg-light py-2 px-3 rounded-3" required autocomplete="new-password">
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted text-uppercase">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control border-0 bg-light py-2 px-3 rounded-3" required>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-danger px-4">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
