<x-app-layout>
    <div class="mb-3 mb-md-4">
        <a href="{{ route('dashboard') }}" class="btn btn-link text-muted p-0 text-decoration-none d-inline-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="row justify-content-center pb-4 pb-md-5">
        <div class="col-12 col-xl-11 col-xxl-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Header -->
                <div class="card-header border-0 px-3 px-md-4 px-lg-5 pt-4 pt-md-5 pb-0 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-2.5 p-md-3 rounded-4 flex-shrink-0">
                            <i class="bi bi-file-earmark-plus fs-3 mb-0"></i>
                        </div>
                        <div>
                            <h4 class="h5 h-md-4 fw-bold text-dark mb-1">Submit New Research Proposal</h4>
                            <p class="text-muted small mb-0">Provide detailed information about your research project.</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('proposal.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body p-3 p-md-4 p-lg-5">
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 rounded-4 p-3 p-md-4 mb-4 shadow-sm">
                                <h6 class="fw-bold mb-2">Please correct the following errors:</h6>
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-4">
                            <!-- Left Column: Basic Info -->
                            <div class="col-12 col-lg-6">
                                <h5 class="fw-bold mb-3 mb-md-4 border-start border-4 border-primary ps-3 text-dark">Basic Information</h5>
                                
                                <div class="mb-3 mb-md-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Proposal Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control border-0 bg-light py-2.5 py-md-3 px-3 px-md-4 rounded-4" placeholder="Enter full research title" required>
                                </div>

                                <div class="mb-3 mb-md-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Research Field</label>
                                    <input type="text" name="research_field" class="form-control border-0 bg-light py-2.5 py-md-3 px-3 px-md-4 rounded-4" placeholder="e.g. Information Technology, Social Science">
                                </div>

                                <div class="mb-3 mb-md-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Abstract / Description <span class="text-danger">*</span></label>
                                    <textarea name="abstract" class="form-control border-0 bg-light py-2.5 py-md-3 px-3 px-md-4 rounded-4" rows="4" placeholder="Provide a concise summary of your research..." required></textarea>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.75rem;">Upload Proposal Document (PDF) <span class="text-danger">*</span></label>
                                    <div class="p-3 p-md-4 bg-light rounded-4 text-center border-2 border-primary position-relative" style="border-style: dashed !important;">
                                        <i class="bi bi-cloud-arrow-up text-primary display-6 d-block mb-1"></i>
                                        <p class="mb-0 small fw-bold text-primary">Click to upload or drag & drop</p>
                                        <p class="text-muted x-small mb-0" style="font-size: 0.7rem;">PDF ONLY • MAX 20MB</p>
                                        <input type="file" name="document" id="proposalDoc" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept="application/pdf" required style="cursor: pointer;">
                                    </div>
                                    <div id="file-name" class="mt-2 small text-success fw-bold text-center d-none p-2 bg-success bg-opacity-10 rounded-3"></div>
                                </div>
                            </div>

                            <!-- Right Column: Collaborators -->
                            <div class="col-12 col-lg-6">
                                <h5 class="fw-bold mb-2 border-start border-4 border-info ps-3 text-dark">Collaborators</h5>
                                <p class="text-muted small mb-3 mb-md-4">Select faculty co-authors and researchers collaborating on this study.</p>

                                <div class="collaborator-picker card border-0 bg-light rounded-4 overflow-hidden mb-3">
                                    <div class="p-2.5 p-md-3 border-bottom bg-primary bg-opacity-10 d-flex justify-content-between align-items-center">
                                        <span class="small fw-bold text-primary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                            <i class="bi bi-people-fill me-1"></i> Available Researchers
                                        </span>
                                        <span class="badge bg-white text-dark border rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                            {{ $researchers->count() }} Available
                                        </span>
                                    </div>

                                    <div class="overflow-auto p-2 p-md-2.5" style="max-height: 430px;">
                                        @forelse($researchers as $researcher)
                                            <label class="collaborator-item d-flex align-items-center justify-content-between p-2.5 mb-2 rounded-3 bg-white border shadow-xs user-select-none transition-all" style="cursor: pointer;">
                                                <div class="d-flex align-items-center gap-2.5 me-2 min-w-0" style="min-width: 0;">
                                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                                        {{ strtoupper(substr($researcher->name, 0, 1)) }}
                                                    </div>
                                                    <div class="min-w-0" style="min-width: 0;">
                                                        <div class="fw-bold text-dark small text-truncate">{{ $researcher->name }}</div>
                                                        <div class="text-muted d-flex align-items-center gap-1 flex-wrap" style="font-size: 0.72rem;">
                                                            <span class="text-truncate">{{ $researcher->department ? strtoupper($researcher->department) : 'No Dept' }}</span>
                                                            @if($researcher->campus)
                                                                <span>&bull;</span>
                                                                <span class="text-truncate">{{ $researcher->campus }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 ms-2">
                                                    <input class="form-check-input border-primary rounded" type="checkbox" name="collaborators[]" value="{{ $researcher->id }}" style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                                                </div>
                                            </label>
                                        @empty
                                            <div class="text-center py-4 text-muted small">No other researchers available.</div>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="alert alert-info border-0 rounded-4 d-flex align-items-center gap-3 py-3 mb-0">
                                    <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
                                    <p class="mb-0 small" style="font-size: 0.8rem; line-height: 1.4;">Collaborators will be able to view this proposal and monitor its review status from their own dashboard once submitted.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions (Responsive Stack on Mobile) -->
                    <div class="card-footer border-0 px-3 px-md-4 px-lg-5 pb-4 pb-md-5 pt-0 bg-white">
                        <div class="d-flex flex-column-reverse flex-md-row justify-content-md-end align-items-stretch align-items-md-center gap-2 gap-md-3">
                            <a href="{{ route('dashboard') }}" class="btn btn-light px-4 py-2.5 py-md-3 rounded-pill fw-bold text-muted text-center shadow-xs">
                                CANCEL
                            </a>
                            <button type="submit" name="action" value="draft" class="btn btn-outline-secondary px-4 py-2.5 py-md-3 rounded-pill fw-bold shadow-xs">
                                <i class="bi bi-save me-1"></i> SAVE AS DRAFT
                            </button>
                            <button type="submit" name="action" value="submit" class="btn btn-primary px-4 px-md-5 py-2.5 py-md-3 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-send-fill me-1"></i> SUBMIT PROPOSAL
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('proposalDoc').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const fileName = e.target.files[0].name;
                const display = document.getElementById('file-name');
                display.innerText = 'Selected PDF: ' + fileName;
                display.classList.remove('d-none');
            }
        });
    </script>
</x-app-layout>