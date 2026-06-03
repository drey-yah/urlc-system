<x-app-layout>
    <div class="row justify-content-center py-5">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg" style="border-radius: 16px;">
                <div class="card-header border-0 px-4 pt-4 pb-0 bg-white">
                    <h5 class="fw-bold mb-0">Edit / Resubmit Proposal</h5>
                </div>
                <form method="POST" action="{{ route('proposal.update', $proposal->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger py-2 small">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small uppercase">Proposal Title</label>
                            <input type="text" name="title" class="form-control bg-light border-0 py-2 px-3" value="{{ $proposal->title }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small uppercase">Research Field</label>
                            <input type="text" name="research_field" class="form-control bg-light border-0 py-2 px-3" value="{{ $proposal->research_field }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small uppercase">Description / Abstract</label>
                            <textarea name="abstract" class="form-control bg-light border-0 py-2 px-3" rows="4" required>{{ $proposal->abstract }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small uppercase">Rationale</label>
                            <textarea name="rationale" class="form-control bg-light border-0 py-2 px-3" rows="4" required>{{ $proposal->rationale }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted small uppercase">Current Document</label>
                            <div class="bg-light p-3 rounded-3 d-flex justify-content-between align-items-center">
                                <span class="small text-muted"><i class="bi bi-file-earmark-pdf me-2"></i> {{ basename($proposal->document_path) }}</span>
                                <a href="{{ \Storage::url($proposal->document_path) }}" target="_blank" class="btn btn-sm btn-outline-primary px-3">View</a>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold text-muted small uppercase">Upload Revised Document (Optional)</label>
                            <input type="file" name="document" class="form-control bg-light border-0 py-2" accept="application/pdf">
                            <small class="text-muted" style="font-size: 0.7rem;">Leave blank to keep the current file.</small>
                        </div>
                    </div>
                    <div class="card-footer border-0 p-4 pt-0 bg-white d-flex justify-content-end gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" name="action" value="draft" class="btn btn-outline-secondary px-4 fw-bold">Save as Draft</button>
                        <button type="submit" name="action" value="submit" class="btn btn-primary px-4 fw-bold">Update Proposal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>