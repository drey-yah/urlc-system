<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Proposal Details</h2>
    </x-slot>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="h5 mb-0">{{ $proposal->title }}</h3>
                <div>
                    @if($proposal->status == 'pending')
                        <span class="badge bg-secondary">Pending</span>
                    @elseif($proposal->status == 'approved')
                        <span class="badge bg-success">Approved</span>
                    @elseif($proposal->status == 'rejected')
                        <span class="badge bg-danger">Rejected</span>
                    @elseif($proposal->status == 'revision_required')
                        <span class="badge bg-warning text-dark">Revision Required</span>
                    @elseif($proposal->status == 'final_approved')
                        <span class="badge bg-primary">Final Approved</span>
                    @elseif($proposal->status == 'final_rejected')
                        <span class="badge bg-dark">Final Rejected</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4 text-muted fw-bold">Researcher:</div>
                <div class="col-md-8">{{ $proposal->user->name }} ({{ $proposal->user->email }})</div>
            </div>
            <div class="row mb-4">
                <div class="col-md-4 text-muted fw-bold">Research Field:</div>
                <div class="col-md-8">{{ $proposal->research_field ?? 'N/A' }}</div>
            </div>
            <div class="row mb-4">
                <div class="col-md-4 text-muted fw-bold">Budget Requested:</div>
                <div class="col-md-8">₱{{ number_format($proposal->budget_requested, 2) }}</div>
            </div>
            <div class="row mb-4">
                <div class="col-md-4 text-muted fw-bold">Abstract:</div>
                <div class="col-md-8">
                    <p class="mb-0" style="white-space: pre-line;">{{ $proposal->abstract }}</p>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-4 text-muted fw-bold">Document:</div>
                <div class="col-md-8">
                    @if($proposal->document_path)
                        <a href="{{ asset('storage/' . $proposal->document_path) }}" target="_blank" class="btn btn-sm btn-primary">
                            View Document (PDF)
                        </a>
                    @else
                        <span class="text-muted">No document uploaded</span>
                    @endif
                </div>
            </div>

            <hr>

            <div class="row mb-4">
                <div class="col-md-4 text-muted fw-bold">Reviewer Comments:</div>
                <div class="col-md-8">
                    <p class="mb-0 text-secondary">{{ $proposal->review_comments ?? 'No comments yet' }}</p>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-4 text-muted fw-bold">Reviewer Suggestions:</div>
                <div class="col-md-8">
                    <p class="mb-0 text-secondary">{{ $proposal->review_suggestions ?? 'No suggestions yet' }}</p>
                </div>
            </div>
        </div>
        <div class="card-footer bg-light">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
            @if(auth()->user()->role == 'researcher' && $proposal->status == 'revision_required')
                <a href="{{ route('proposal.edit', $proposal->id) }}" class="btn btn-warning">Edit & Resubmit</a>
            @endif
        </div>
    </div>
</x-app-layout>
