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
                                    <td colspan="6" class="text-center py-5 text-muted small italic">You haven't submitted any research proposals as lead.</td>
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

        <!-- Work Plan & Gantt Chart Overview Section -->
        <div class="mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Project Work Plans <span class="text-success fs-6 fw-normal ms-2">/ Gantt Chart Overview</span></h4>
                    <p class="text-muted small mb-0">Overview of scheduled milestone activities across your research projects</p>
                </div>
            </div>

            @php
                $allMilestones = collect();
                foreach($leadProposals as $p) {
                    foreach($p->milestones as $m) {
                        $m->proposal_title = $p->title;
                        $m->proposal_id = $p->id;
                        $allMilestones->push($m);
                    }
                }
                foreach($collaboratedProposals as $p) {
                    foreach($p->milestones as $m) {
                        $m->proposal_title = $p->title;
                        $m->proposal_id = $p->id;
                        $allMilestones->push($m);
                    }
                }
            @endphp

            @if($allMilestones->isNotEmpty())
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                                    <i class="bi bi-kanban fs-4"></i>
                                </div>
                                <div>
                                    <div class="h4 fw-bold mb-0">{{ $allMilestones->count() }}</div>
                                    <div class="text-muted small">Total Milestones</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                                    <i class="bi bi-check-circle fs-4"></i>
                                </div>
                                <div>
                                    <div class="h4 fw-bold mb-0">{{ $allMilestones->where('status', 'approved')->count() }}</div>
                                    <div class="text-muted small">Completed / Approved</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                                    <i class="bi bi-hourglass-split fs-4"></i>
                                </div>
                                <div>
                                    <div class="h4 fw-bold mb-0">{{ $allMilestones->where('status', 'pending')->count() }}</div>
                                    <div class="text-muted small">Pending Verification</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="list-group list-group-flush">
                        @foreach($allMilestones->sortByDesc('created_at')->take(5) as $milestone)
                            <div class="list-group-item px-0 py-3 border-bottom-0 border-top">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                    <div>
                                        <span class="fw-bold text-dark me-2">{{ $milestone->title }}</span>
                                        <span class="badge bg-light text-primary border px-2 py-1 small">{{ $milestone->proposal_title }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($milestone->start_date && $milestone->target_date)
                                            <span class="small text-muted me-2"><i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($milestone->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($milestone->target_date)->format('M d, Y') }}</span>
                                        @endif
                                        <a href="{{ route('proposal.show', $milestone->proposal_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View Gantt</a>
                                    </div>
                                </div>
                                <div class="progress rounded-pill" style="height: 8px;">
                                    <div class="progress-bar {{ $milestone->status == 'approved' ? 'bg-success' : 'bg-warning' }}" 
                                         role="progressbar" 
                                         style="width: {{ $milestone->status == 'approved' ? '100%' : '50%' }};"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-light">
                    <i class="bi bi-calendar-week text-muted display-5 d-block mb-3"></i>
                    <h6 class="fw-bold text-muted">No Work Plan Scheduled Yet</h6>
                    <p class="text-muted small mb-0">Open any research proposal details to add milestone target dates and start building your Work Plan.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>