<x-app-layout>
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-6 fw-bold mb-1">Researcher Dashboard</h1>
            <p class="text-muted h6 fw-normal">Welcome, {{ Auth::user()->name }}</p>
        </div>
        <a href="{{ route('proposal.create') }}" class="btn btn-primary d-flex align-items-center gap-2 py-2 px-4 shadow-sm">
            <i class="bi bi-plus-lg"></i> Submit New Proposal
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h5 class="mb-0 fw-bold">My Proposals</h5>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Proposal Title</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Phase</th>
                        <th class="text-center">Submitted Date</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($proposals as $proposal)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-file-earmark-text text-muted h4 mb-0"></i>
                                <span class="fw-semibold text-dark">{{ $proposal->title }}</span>
                            </div>
                        </td>
                        <td class="text-center">
                            @php
                                $badgeClass = match($proposal->status) {
                                    'approved', 'final_approved' => 'badge-approved',
                                    'pending' => 'badge-pending',
                                    'rejected', 'final_rejected' => 'badge-rejected',
                                    'revision_required' => 'badge-in-review',
                                    default => 'badge-pending'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ strtoupper(str_replace('_', ' ', $proposal->status)) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="text-muted fw-medium">Phase {{ $proposal->current_phase }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2 text-muted small">
                                <i class="bi bi-calendar3"></i>
                                {{ $proposal->created_at->format('Y-m-d') }}
                            </div>
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2 px-3 border">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-folder2-open display-4 mb-3 d-block"></i>
                                <p class="mb-0">You haven't submitted any proposals yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>