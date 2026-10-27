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

                        @if($proposal->status === 'returned_for_revision' && $proposal->return_reason)
                            <div class="alert alert-danger border-0 rounded-3 mb-4 p-3 shadow-xs" style="background-color: #fef2f2; border-left: 4px solid #ef4444 !important;">
                                <div class="d-flex align-items-start gap-2.5">
                                    <div class="text-danger fs-5"><i class="bi bi-exclamation-octagon-fill"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="fw-bold mb-0 text-danger">
                                                @if($proposal->returned_by_role === 'staff')
                                                    Receiving Staff Revision Remarks (Administrative Completeness)
                                                @elseif($proposal->returned_by_role === 'dean')
                                                    College Dean Revision Remarks
                                                @else
                                                    {{ ucfirst($proposal->returned_by_role ?? 'Coordinator') }} Revision Remarks
                                                @endif
                                            </h6>
                                            @if($proposal->returned_at)
                                                <span class="text-muted" style="font-size: 0.72rem;">
                                                    {{ \Carbon\Carbon::parse($proposal->returned_at)->format('M d, Y h:i A') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-muted small mb-2">
                                            @if($proposal->returned_by_role === 'staff')
                                                The <strong>Research Unit Receiving Staff</strong> returned this proposal for administrative incompleteness. Once updated, your proposal will be <strong>fast-tracked directly back to the Receiving Staff</strong>.
                                            @elseif($proposal->returned_by_role === 'dean')
                                                The <strong>College Dean</strong> returned this proposal for revision. Please address the feedback below before updating:
                                            @else
                                                The <strong>College Coordinator</strong> returned this proposal for revision. Please address the feedback below before updating:
                                            @endif
                                        </p>
                                        <div class="p-2.5 bg-white rounded-2 border border-danger border-opacity-25 small text-dark fw-medium" style="white-space: pre-wrap; font-size: 0.85rem;">{{ $proposal->return_reason }}</div>
                                    </div>
                                </div>
                            </div>
                        @elseif($proposal->status === 'revision_required' && ($proposal->review_comments || $proposal->review_suggestions))
                            <div class="alert alert-warning border-0 rounded-3 mb-4 p-3 shadow-xs" style="background-color: #fffbeb; border-left: 4px solid #f59e0b !important;">
                                <div class="d-flex align-items-start gap-2.5">
                                    <div class="text-warning fs-5"><i class="bi bi-chat-left-text-fill"></i></div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1 text-dark">Reviewer Feedback</h6>
                                        @if($proposal->review_comments)
                                            <div class="small text-muted mb-1"><strong>Comments:</strong> {{ $proposal->review_comments }}</div>
                                        @endif
                                        @if($proposal->review_suggestions)
                                            <div class="small text-muted"><strong>Suggestions:</strong> {{ $proposal->review_suggestions }}</div>
                                        @endif
                                    </div>
                                </div>
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

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-muted small uppercase">Current Document</label>
                            <div class="bg-light p-3 rounded-3 d-flex justify-content-between align-items-center">
                                <span class="small text-muted"><i class="bi bi-file-earmark-pdf me-2"></i> {{ basename($proposal->document_path) }}</span>
                                <a href="{{ route('file.serve', ['path' => $proposal->document_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary px-3">View</a>
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
                        <button type="submit" name="action" value="submit" class="btn btn-primary px-4 fw-bold">
                            {{ $proposal->returned_by_role === 'staff' ? 'Update & Resubmit to Research Unit' : 'Update Proposal' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>