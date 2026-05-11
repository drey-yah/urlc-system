<x-app-layout>
    <div class="row justify-content-center py-5">
        <div class="col-md-6">
            <div class="card border-0 shadow-lg" style="border-radius: 16px;">
                <div class="card-header border-0 px-4 pt-4 pb-0 bg-white">
                    <h5 class="fw-bold mb-0">Submit New Proposal</h5>
                </div>
                <form method="POST" action="{{ route('proposal.store') }}" enctype="multipart/form-data">
                    @csrf
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
                            <input type="text" name="title" class="form-control bg-light border-0 py-2 px-3" placeholder="Enter proposal title" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small uppercase">Description / Abstract</label>
                            <textarea name="abstract" class="form-control bg-light border-0 py-2 px-3" rows="4" placeholder="Enter proposal description" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small uppercase">Research Field</label>
                            <input type="text" name="research_field" class="form-control bg-light border-0 py-2 px-3" placeholder="e.g. Computer Science">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold text-muted small uppercase">Upload Document (PDF)</label>
                            <input type="file" name="document" class="form-control bg-light border-0 py-2" accept="application/pdf" required>
                            <small class="text-muted" style="font-size: 0.7rem;">Only PDF files are allowed. Max size 20MB.</small>
                        </div>
                    </div>
                    <div class="card-footer border-0 p-4 pt-0 bg-white d-flex justify-content-end gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-light px-4">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Submit Proposal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>