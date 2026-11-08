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
                                <select name="campus" class="form-select border-0 bg-light py-2 px-3 rounded-3">
                                    <option value="">— Select Campus —</option>
                                    <option value="Main Campus (Sibalom)" {{ old('campus', $user->campus) == 'Main Campus (Sibalom)' ? 'selected' : '' }}>Main Campus (Sibalom)</option>
                                    <option value="TLMC (Tibiao)" {{ old('campus', $user->campus) == 'TLMC (Tibiao)' ? 'selected' : '' }}>TLMC - Tario Lim Memorial Campus</option>
                                    <option value="Hamtic Campus" {{ old('campus', $user->campus) == 'Hamtic Campus' ? 'selected' : '' }}>Hamtic Campus</option>
                                    <option value="Libertad Extension Campus" {{ in_array(old('campus', $user->campus), ['Libertad Extension Campus', 'Libertad Campus']) ? 'selected' : '' }}>Libertad Extension Campus</option>
                                    <option value="Caluya Extension Campus" {{ in_array(old('campus', $user->campus), ['Caluya Extension Campus', 'Caluya Campus']) ? 'selected' : '' }}>Caluya Extension Campus</option>
                                    @if($user->campus && !in_array($user->campus, ['Main Campus (Sibalom)', 'TLMC (Tibiao)', 'Hamtic Campus', 'Libertad Extension Campus', 'Libertad Campus', 'Caluya Extension Campus', 'Caluya Campus']))
                                        <option value="{{ $user->campus }}" selected>{{ $user->campus }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Department</label>
                                <input type="text" name="department" class="form-control border-0 bg-light py-2 px-3 rounded-3" value="{{ old('department', $user->department) }}" placeholder="e.g. College of Engineering">
                            </div>

                            <!-- Collaboration & Tagging Privacy -->
                            <div class="col-12 mt-3 pt-3 border-top">
                                <label class="form-label small fw-bold text-muted text-uppercase d-flex align-items-center gap-1.5 mb-1">
                                    <i class="bi bi-shield-lock-fill text-primary"></i> Collaboration &amp; Tagging Privacy
                                </label>
                                <p class="text-muted small mb-3">
                                    Control whether other faculty researchers can search, tag, and invite you as a research co-proponent when creating proposals (just like Facebook profile privacy).
                                </p>
                                
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="p-3 border rounded-3 d-flex align-items-start gap-2.5 h-100 cursor-pointer transition-all bg-white shadow-xs" style="cursor: pointer;">
                                            <input type="radio" name="allow_collaboration" value="1" class="form-check-input mt-1 border-primary" {{ old('allow_collaboration', $user->allow_collaboration ?? true) ? 'checked' : '' }}>
                                            <div>
                                                <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                                    <i class="bi bi-globe text-success"></i> Public (Allow Tagging)
                                                </div>
                                                <div class="text-muted" style="font-size: 0.76rem; line-height: 1.35;">
                                                    Your name appears in the Available Researchers list. Other lead researchers can tag and invite you as a co-proponent (you still get to Accept or Decline each invitation).
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="p-3 border rounded-3 d-flex align-items-start gap-2.5 h-100 cursor-pointer transition-all bg-white shadow-xs" style="cursor: pointer;">
                                            <input type="radio" name="allow_collaboration" value="0" class="form-check-input mt-1 border-secondary" {{ !old('allow_collaboration', $user->allow_collaboration ?? true) ? 'checked' : '' }}>
                                            <div>
                                                <div class="fw-bold text-dark small d-flex align-items-center gap-1">
                                                    <i class="bi bi-lock-fill text-danger"></i> Private (Do Not Tag)
                                                </div>
                                                <div class="text-muted" style="font-size: 0.76rem; line-height: 1.35;">
                                                    Keep your profile hidden from the collaborator selection picker. Other researchers cannot tag or add your name to their research proposals.
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
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
