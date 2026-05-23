<x-app-layout>
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h3 fw-bold mb-1">Researches</h1>
                <p class="text-muted small">Manage your research projects and collaborations</p>
            </div>
            <a href="{{ route('proposal.create') }}" class="btn btn-primary d-flex align-items-center gap-2 py-2 px-4 shadow-sm rounded-pill fw-bold">
                <i class="bi bi-plus-lg"></i> SUBMIT PROPOSAL
            </a>
        </div>

        <!-- Researches / As Lead -->
        <div class="mb-5">
            <h4 class="fw-bold mb-4">Researches <span class="text-primary fs-6 fw-normal ms-2">/ As Lead</span></h4>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-primary bg-opacity-10">
                            <tr>
                                <th class="ps-4 py-3 small fw-bold text-primary border-0">PROPOSAL CODE</th>
                                <th class="py-3 small fw-bold text-primary border-0">TITLE</th>
                                <th class="py-3 small fw-bold text-primary border-0">RATIONALE</th>
                                <th class="py-3 small fw-bold text-primary border-0">COLLABORATORS</th>
                                <th class="py-3 small fw-bold text-primary border-0">STATUS</th>
                                <th class="py-3 small fw-bold text-primary border-0">CURRENT PHASE</th>
                                <th class="pe-4 py-3 small fw-bold text-primary border-0 text-end">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leadProposals as $proposal)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 0.65rem;">{{ $proposal->proposal_code ?? 'NO TAG' }}</span>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-bold text-dark mb-1">{{ $proposal->title }}</div>
                                        <div class="text-muted small italic">{{ $proposal->research_field }}</div>
                                    </td>
                                    <td>
                                        <div class="text-muted small text-truncate" style="max-width: 200px;">
                                            {{ $proposal->rationale ?? 'No rationale provided' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @forelse($proposal->collaborators as $collab)
                                                <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.65rem;">{{ $collab->name }}</span>
                                            @empty
                                                <span class="text-muted small italic">None</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($proposal->status) {
                                                'approved', 'final_approved' => 'bg-success text-success',
                                                'pending' => 'bg-warning text-warning',
                                                'rejected', 'final_rejected' => 'bg-danger text-danger',
                                                'revision_required' => 'bg-info text-info',
                                                default => 'bg-secondary text-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }} bg-opacity-10 px-3 py-2 rounded-pill small fw-bold">
                                            {{ strtoupper(str_replace('_', ' ', $proposal->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ ($proposal->current_phase / 5) * 100 }}%"></div>
                                            </div>
                                            <span class="small fw-bold text-muted">Ph {{ $proposal->current_phase }}</span>
                                        </div>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                                <li><a class="dropdown-item py-2" href="{{ route('proposal.show', $proposal->id) }}"><i class="bi bi-eye me-2"></i> View Details</a></li>
                                                @if(in_array($proposal->status, ['revision_required', 'draft', 'returned_for_revision']))
                                                    <li><a class="dropdown-item py-2 text-primary" href="{{ route('proposal.edit', $proposal->id) }}"><i class="bi bi-pencil-square me-2"></i> Edit Proposal</a></li>
                                                @endif
                                                @if($proposal->status === 'pending')
                                                    <li>
                                                        <form action="{{ route('proposal.destroy', $proposal->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this submission?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="dropdown-item py-2 text-danger"><i class="bi bi-x-circle me-2"></i> Cancel Submission</button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted small italic">You haven't submitted any research proposals as lead.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Researches / As Collaborator -->
        <div>
            <h4 class="fw-bold mb-4">Researches <span class="text-info fs-6 fw-normal ms-2">/ As Collaborator</span></h4>
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-info bg-opacity-10">
                            <tr>
                                <th class="ps-4 py-3 small fw-bold text-info border-0">PROPOSAL CODE</th>
                                <th class="py-3 small fw-bold text-info border-0">TITLE</th>
                                <th class="py-3 small fw-bold text-info border-0">LEAD RESEARCHER</th>
                                <th class="py-3 small fw-bold text-info border-0">COLLABORATORS</th>
                                <th class="py-3 small fw-bold text-info border-0">STATUS</th>
                                <th class="pe-4 py-3 small fw-bold text-info border-0 text-end">CURRENT PHASE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($collaboratedProposals as $proposal)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 0.65rem;">{{ $proposal->proposal_code ?? 'NO TAG' }}</span>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-bold text-dark mb-1">{{ $proposal->title }}</div>
                                        <div class="text-muted small italic">{{ $proposal->research_field }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                {{ substr($proposal->user->name, 0, 1) }}
                                            </div>
                                            <div class="small fw-bold">{{ $proposal->user->name }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($proposal->collaborators as $collab)
                                                @if($collab->id !== auth()->id())
                                                    <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.65rem;">{{ $collab->name }}</span>
                                                @else
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border px-2 py-1" style="font-size: 0.65rem;">You</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ 
                                            $proposal->status == 'pending' ? 'bg-warning text-warning' : 
                                            (str_contains($proposal->status, 'approved') ? 'bg-success text-success' : 'bg-danger text-danger') 
                                        }} bg-opacity-10 px-3 py-2 rounded-pill small fw-bold">
                                            {{ strtoupper(str_replace('_', ' ', $proposal->status)) }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <span class="fw-bold text-muted small">Phase {{ $proposal->current_phase }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted small italic">You are not listed as a collaborator in any research project.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>