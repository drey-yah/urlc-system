<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 border-0">Proposal Code</th>
                    <th class="py-3 border-0">Title</th>
                    <th class="py-3 border-0">Rationale</th>
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
                            <div class="fw-bold text-primary">{{ $proposal->title }}</div>
                            <div class="text-muted small italic">{{ $proposal->research_field }}</div>
                        </td>
                        <td>
                            <div class="text-muted small text-truncate" style="max-width: 250px;">
                                {{ $proposal->rationale ?? 'No rationale provided' }}
                            </div>
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
                        <td colspan="5" class="text-center py-5 text-muted small italic">
                            No research projects found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
