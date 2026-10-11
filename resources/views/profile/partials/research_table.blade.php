<div>
    <!-- Mobile View: Cards (d-md-none) -->
    <div class="d-md-none mb-4">
        @forelse($proposals as $proposal)
            <div class="card border-0 shadow-sm rounded-4 mb-2.5 p-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-1.5 mb-1.5">
                    <span class="badge bg-light text-dark border fw-mono" style="font-size: 0.7rem;">
                        {{ $proposal->proposal_code ?? 'NO TAG' }}
                    </span>
                    <span class="text-muted small" style="font-size: 0.72rem;">{{ $proposal->research_field ?? 'General' }}</span>
                </div>
                <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.92rem; line-height: 1.35;">
                    <a href="{{ route('proposal.show', $proposal->id) }}" class="text-dark text-decoration-none hover-primary transition">
                        {{ $proposal->title }}
                    </a>
                </h6>
                <div class="small text-muted mb-2" style="font-size: 0.76rem;">
                    @if($type === 'lead')
                        <span>Collaborators: </span>
                        @if($proposal->collaborators->count() > 0)
                            <span class="fw-medium text-dark">{{ $proposal->collaborators->pluck('name')->join(', ') }}</span>
                        @else
                            <span class="fst-italic">None</span>
                        @endif
                    @else
                        <span>Lead: <strong class="text-dark">{{ $proposal->user->name ?? 'Unknown' }}</strong></span>
                    @endif
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="font-size: 0.76rem;">
                    <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1" style="font-size: 0.74rem;">
                        View Proposal
                    </a>
                    @if($proposal->document_path)
                        <a href="{{ route('file.serve', ['path' => $proposal->document_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.74rem;">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                    @else
                        <span class="text-muted small fst-italic">No PDF</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm rounded-4 text-center py-4 text-muted">
                <p class="small mb-0">No research projects found.</p>
            </div>
        @endforelse
    </div>

    <!-- Desktop View: Table (d-none d-md-block) -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0">Proposal Code</th>
                        <th class="py-3 border-0">Title</th>
                        <th class="py-3 border-0">{{ $type === 'lead' ? 'Collaborators' : 'Lead Researcher' }}</th>
                        <th class="pe-4 py-3 border-0 text-end">File</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proposals as $proposal)
                        <tr>
                            <td class="ps-4 py-3">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 0.65rem;">{{ $proposal->proposal_code ?? 'NO TAG' }}</span>
                            </td>
                            <td class="py-3">
                                <a href="{{ route('proposal.show', $proposal->id) }}" class="fw-bold text-dark text-decoration-none hover-primary">{{ $proposal->title }}</a>
                                <div class="text-muted small italic">{{ $proposal->research_field }}</div>
                            </td>
                            <td>
                                @if($type === 'lead')
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($proposal->collaborators as $collab)
                                            <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.65rem;">{{ $collab->name }}</span>
                                        @empty
                                            <span class="text-muted small italic">None</span>
                                        @endforelse
                                    </div>
                                @else
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                            {{ substr($proposal->user->name, 0, 1) }}
                                        </div>
                                        <span class="small fw-medium">{{ $proposal->user->name }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                @if($proposal->document_path)
                                    <a href="{{ route('file.serve', ['path' => $proposal->document_path]) }}" target="_blank" class="text-decoration-none d-flex align-items-center justify-content-end gap-2 text-primary">
                                        <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                                        <span class="small fw-bold">Open File</span>
                                    </a>
                                @else
                                    <span class="text-muted small italic">No File</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted small italic">
                                No research projects found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
