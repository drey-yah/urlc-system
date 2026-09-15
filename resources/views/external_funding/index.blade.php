<x-app-layout>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1">External Funding</h1>
            <p class="text-muted mb-0">Track funding-agency endorsements, agreements, implementation, and grant closure.</p>
        </div>
    </div>

    @if(auth()->user()->role === 'researcher')
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Submit a Proposal for External Funding</h5>
                @if($proposals->isEmpty())
                    <p class="text-muted mb-0">All of your lead proposals are already in an external-funding workflow.</p>
                @else
                    <form method="POST" action="{{ route('external-funding.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label">Research Proposal</label>
                            <select name="research_proposal_id" class="form-select" required>
                                <option value="">Select a proposal</option>
                                @foreach($proposals as $proposal)
                                    <option value="{{ $proposal->id }}">{{ $proposal->proposal_code }} — {{ $proposal->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3"><label class="form-label">Funding Agency</label><select name="funding_agency_user_id" class="form-select" required><option value="">Select an approved agency</option>@foreach($fundingAgencies as $agency)<option value="{{ $agency->id }}">{{ $agency->organization ?: $agency->name }}</option>@endforeach</select></div>
                        <div class="col-md-3"><label class="form-label">Grant Program</label><input name="grant_program" class="form-control"></div>
                        <div class="col-md-2"><label class="form-label">Requested Amount</label><input name="requested_amount" type="number" min="0" step="0.01" class="form-control"></div>
                        <div class="col-12"><button class="btn btn-primary">Request University Endorsement</button></div>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light"><tr><th class="ps-4">Proposal</th><th>Funding Agency</th><th>Grant Details</th><th>Status</th><th class="pe-4">Actions</th></tr></thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td class="ps-4"><div class="fw-bold">{{ $project->proposal->title }}</div><small class="text-muted">{{ $project->proposal->user->name }}</small></td>
                            <td>{{ $project->funding_agency }}<br><small class="text-muted">{{ $project->grant_program }}</small></td>
                            <td><small>Requested: ₱{{ number_format($project->requested_amount ?? 0, 2) }}</small><br><small>Awarded: ₱{{ number_format($project->awarded_amount ?? 0, 2) }}</small></td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">{{ strtoupper(str_replace('_', ' ', $project->status)) }}</span></td>
                            <td class="pe-4">
                                @if(auth()->user()->role === 'researcher' && $project->status === 'revision_requested')
                                    <form method="POST" action="{{ route('external-funding.resubmit', $project->id) }}" class="mb-2">@csrf<input name="revision_notes" class="form-control form-control-sm mb-1" placeholder="Revision summary" required><button class="btn btn-sm btn-outline-primary">Resubmit to Agency</button></form>
                                @endif
                                @if(auth()->user()->role === 'researcher' && $project->status === 'under_grant_monitoring')
                                    <form method="POST" action="{{ route('external-funding.terminal-report', $project->id) }}" enctype="multipart/form-data">@csrf<input name="terminal_report" type="file" class="form-control form-control-sm mb-1" required><button class="btn btn-sm btn-outline-primary">Submit Terminal Report</button></form>
                                @endif
                                @if((auth()->user()->role === 'vprei' || auth()->user()->isAdmin()) && $project->status === 'pending_university_endorsement')
                                    <form method="POST" action="{{ route('external-funding.endorse-university', $project->id) }}">@csrf<button class="btn btn-sm btn-success">Endorse for University Approval</button></form>
                                @endif
                                @if(auth()->user()->role === 'president' && $project->status === 'pending_president_endorsement')
                                    <form method="POST" action="{{ route('external-funding.endorse-agency', $project->id) }}">@csrf<button class="btn btn-sm btn-success">Endorse to Agency</button></form>
                                @endif
                                @if(auth()->user()->isFundingAgency() && $project->status === 'under_funder_review')
                                    <form method="POST" action="{{ route('external-funding.agency-decision', $project->id) }}">@csrf<select name="decision" class="form-select form-select-sm mb-1"><option value="accepted">Accept</option><option value="revision_requested">Request revision</option><option value="rejected">Reject</option></select><input name="agency_reference_number" class="form-control form-control-sm mb-1" placeholder="Agency reference number"><input name="awarded_amount" type="number" step="0.01" class="form-control form-control-sm mb-1" placeholder="Awarded amount"><textarea name="agency_feedback" class="form-control form-control-sm mb-1" placeholder="Decision notes"></textarea><button class="btn btn-sm btn-primary">Submit Funding Decision</button></form>
                                @endif
                                @if(auth()->user()->role === 'president' && $project->status === 'pending_moa_mou')
                                    <form method="POST" action="{{ route('external-funding.agreement', $project->id) }}" enctype="multipart/form-data">@csrf<input name="moa_mou_file" type="file" class="form-control form-control-sm mb-1" required><input name="grant_start_date" type="date" class="form-control form-control-sm mb-1" required><input name="grant_end_date" type="date" class="form-control form-control-sm mb-1" required><button class="btn btn-sm btn-success">Execute MOA/MOU</button></form>
                                @endif
                                @if((auth()->user()->role === 'vprei' || auth()->user()->isAdmin()) && $project->status === 'grant_active')
                                    <form method="POST" action="{{ route('external-funding.monitor', $project->id) }}">@csrf<button class="btn btn-sm btn-outline-primary">Start Monitoring</button></form>
                                @endif
                                @if((auth()->user()->role === 'vprei' || auth()->user()->isAdmin()) && $project->status === 'pending_grant_closure')
                                    <form method="POST" action="{{ route('external-funding.close', $project->id) }}">@csrf<button class="btn btn-sm btn-success">Close Grant</button></form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5">No external funding projects yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
