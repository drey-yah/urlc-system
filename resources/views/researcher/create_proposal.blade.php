<x-app-layout>
    <div class="row justify-content-center py-5">
        <div class="col-md-10">
            <div class="card border-0 shadow-lg" style="border-radius: 20px;">
                <div class="card-header border-0 px-5 pt-5 pb-0 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4">
                            <i class="bi bi-file-earmark-plus h3 mb-0"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Submit New Research Proposal</h4>
                            <p class="text-muted small mb-0">Provide detailed information about your research project.</p>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('proposal.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body p-5">
                        @if ($errors->any())
                            <div class="alert alert-danger border-0 rounded-4 p-4 mb-4 shadow-sm">
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
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-4 border-start border-4 border-primary ps-3">Basic Information</h5>
                                
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Proposal Title</label>
                                    <input type="text" name="title" class="form-control border-0 bg-light py-3 px-4 rounded-4" placeholder="Enter full research title" required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Research Field</label>
                                    <input type="text" name="research_field" class="form-control border-0 bg-light py-3 px-4 rounded-4" placeholder="e.g. Information Technology, Social Science">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Abstract / Description</label>
                                    <textarea name="abstract" class="form-control border-0 bg-light py-3 px-4 rounded-4" rows="4" placeholder="Provide a concise summary of your research..." required></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Rationale</label>
                                    <textarea name="rationale" class="form-control border-0 bg-light py-3 px-4 rounded-4" rows="4" placeholder="Why is this research being conducted? What is the core motivation?" required></textarea>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Upload Proposal Document (PDF)</label>
                                    <div class="p-4 bg-light rounded-4 text-center border-2 border-dashed border-primary position-relative">
                                        <i class="bi bi-cloud-arrow-up text-primary h1 d-block mb-2"></i>
                                        <p class="mb-0 small fw-bold text-primary">Click to upload or drag & drop</p>
                                        <p class="text-muted x-small mb-0" style="font-size: 0.65rem;">PDF ONLY • MAX 20MB</p>
                                        <input type="file" name="document" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" accept="application/pdf" required style="cursor: pointer;">
                                    </div>
                                    <div id="file-name" class="mt-2 small text-primary fw-bold text-center d-none"></div>
                                </div>
                            </div>

                            <!-- Right Column: Collaborators -->
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-4 border-start border-4 border-info ps-3">Collaborators</h5>
                                <p class="text-muted small mb-4">Select other researchers who are part of this project. (See Figure 2 reference)</p>

                                <div class="card border-0 bg-light rounded-4 overflow-hidden shadow-none mb-4">
                                    <div class="table-responsive" style="max-height: 500px;">
                                        <table class="table table-borderless align-middle mb-0">
                                            <thead class="bg-primary bg-opacity-10">
                                                <tr>
                                                    <th class="ps-3 py-3 small fw-bold text-primary">NAME</th>
                                                    <th class="py-3 small fw-bold text-primary">CAMPUS/DEPT</th>
                                                    <th class="pe-3 py-3 small fw-bold text-primary text-end">ADD</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($researchers as $researcher)
                                                    <tr class="border-bottom border-white border-opacity-50">
                                                        <td class="ps-3 py-3">
                                                            <div class="fw-bold small">{{ $researcher->name }}</div>
                                                            <div class="text-muted" style="font-size: 0.7rem;">{{ $researcher->email }}</div>
                                                        </td>
                                                        <td class="py-3">
                                                            <div class="text-muted small">{{ $researcher->campus ?? 'N/A' }}</div>
                                                            <div class="text-muted" style="font-size: 0.7rem;">{{ $researcher->department ?? 'N/A' }}</div>
                                                        </td>
                                                        <td class="pe-3 py-3 text-end">
                                                            <div class="form-check form-check-inline me-0">
                                                                <input class="form-check-input border-primary" type="checkbox" name="collaborators[]" value="{{ $researcher->id }}">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center py-4 text-muted small">No other researchers available.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="alert alert-info border-0 rounded-4 d-flex align-items-center gap-3 py-3">
                                    <i class="bi bi-info-circle-fill fs-4"></i>
                                    <p class="mb-0 small">Collaborators will be able to view this proposal and its status from their own profiles once submitted.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-0 p-5 pt-0 bg-white d-flex justify-content-end gap-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-light px-5 py-3 rounded-pill fw-bold text-muted">CANCEL</a>
                        <button type="submit" class="btn btn-primary px-5 py-3 rounded-pill fw-bold shadow-sm">SUBMIT PROPOSAL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelector('input[type="file"]').addEventListener('change', function(e) {
            const fileName = e.target.files[0].name;
            const display = document.getElementById('file-name');
            display.innerText = 'Selected: ' + fileName;
            display.classList.remove('d-none');
        });
    </script>
</x-app-layout>