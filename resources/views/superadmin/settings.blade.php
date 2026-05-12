<x-app-layout>
    <div class="mb-5">
        <h1 class="h3 fw-bold mb-1">System Settings</h1>
        <p class="text-muted">Configure global portal parameters and limits</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <form action="{{ route('superadmin.settings.update') }}" method="POST">
                    @csrf
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Portal Configuration</h5>

                        <!-- Default Settings if none exist -->
                        @php
                            $currentSettings = $settings->pluck('value', 'key');
                        @endphp

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Portal Name</label>
                            <input type="text" name="settings[portal_name]" class="form-control border-0 bg-light py-2 px-3" 
                                value="{{ $currentSettings['portal_name'] ?? 'URLC Research Portal' }}">
                            <div class="form-text small">The name of the application displayed in the header and emails.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Submissions Status</label>
                            <select name="settings[submissions_enabled]" class="form-select border-0 bg-light py-2 px-3">
                                <option value="true" {{ ($currentSettings['submissions_enabled'] ?? 'true') == 'true' ? 'selected' : '' }}>Open (Accepting Proposals)</option>
                                <option value="false" {{ ($currentSettings['submissions_enabled'] ?? 'true') == 'false' ? 'selected' : '' }}>Closed (Read-Only)</option>
                            </select>
                            <div class="form-text small">Enable or disable new research proposal submissions globally.</div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Max Upload Size (MB)</label>
                                <input type="number" name="settings[max_upload_size]" class="form-control border-0 bg-light py-2 px-3" 
                                    value="{{ $currentSettings['max_upload_size'] ?? '20' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Support Email</label>
                                <input type="email" name="settings[support_email]" class="form-control border-0 bg-light py-2 px-3" 
                                    value="{{ $currentSettings['support_email'] ?? 'support@urlc.edu.ph' }}">
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted text-uppercase">Maintenance Notice</label>
                            <textarea name="settings[maintenance_notice]" class="form-control border-0 bg-light py-2 px-3" rows="3">{{ $currentSettings['maintenance_notice'] ?? '' }}</textarea>
                            <div class="form-text small">Display a custom notice to users if the system is undergoing maintenance.</div>
                        </div>
                    </div>
                    <div class="card-footer bg-white p-4 border-0 text-end">
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">
                            <i class="bi bi-save me-2"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle"></i> Tip
                    </h5>
                    <p class="mb-0 small opacity-90" style="line-height: 1.6;">
                        Settings saved here apply globally to all users. Changes to the **Portal Name** will update the navigation bar and all automated email headers instantly.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
