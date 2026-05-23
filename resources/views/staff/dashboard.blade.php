<x-app-layout>
    <div class="mb-5">
        <h1 class="h3 fw-bold mb-1">Receiving Queue</h1>
        <p class="text-muted">Welcome, {{ Auth::user()->name }} | Supporting Staff</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4">
            <h5 class="mb-0 fw-bold">Endorsed Proposals (Awaiting Receiving)</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0 text-muted small uppercase">Proposal Code</th>
                        <th class="py-3 border-0 text-muted small uppercase">Proposal Title</th>
                        <th class="py-3 border-0 text-muted small uppercase">Researcher</th>
                        <th class="py-3 text-center border-0 text-muted small uppercase">Status</th>
                        <th class="pe-4 py-3 text-end border-0 text-muted small uppercase">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proposals as $proposal)
                    <tr>
                        <td class="ps-4 py-4">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 0.65rem;">{{ $proposal->proposal_code ?? 'NO TAG' }}</span>
                        </td>
                        <td class="py-4">
                            <div class="fw-bold text-dark mb-1">{{ $proposal->title }}</div>
                            <div class="text-muted small italic">{{ $proposal->research_field }}</div>
                        </td>
                        <td class="py-4">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                    {{ substr($proposal->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="small fw-bold">{{ $proposal->user->name }}</div>
                                    <div class="text-muted x-small uppercase fw-semibold">{{ $proposal->user->department }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center py-4">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill small fw-bold">
                                ENDORSED
                            </span>
                        </td>
                        <td class="pe-4 text-end py-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-outline-primary btn-sm px-4 py-2 d-inline-flex align-items-center gap-2 rounded-pill fw-bold shadow-sm">
                                    <i class="bi bi-eye-fill fs-6"></i> View
                                </a>
                                <button type="button" class="btn btn-primary btn-sm px-4 py-2 d-inline-flex align-items-center gap-2 rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#receiveModal{{ $proposal->id }}">
                                    <i class="bi bi-send-fill fs-6"></i> Receive & Forward
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="receiveModal{{ $proposal->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered text-start">
                                        <div class="modal-content border-0 rounded-4 shadow-lg">
                                            <div class="modal-header border-0 bg-light rounded-top-4">
                                                <h5 class="modal-title fw-bold">Administrative Completeness Check</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('staff.proposals.forward', $proposal->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body p-4 text-start">
                                                    <p class="text-muted small mb-4">Please verify the following requirements are met before routing to the Admin for Evaluation Assignment.</p>
                                                    
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input border-primary" type="checkbox" value="" id="check1{{ $proposal->id }}" required>
                                                        <label class="form-check-label small fw-semibold text-dark" for="check1{{ $proposal->id }}">
                                                            Technical Checking Completed
                                                        </label>
                                                    </div>
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input border-primary" type="checkbox" value="" id="check2{{ $proposal->id }}" required>
                                                        <label class="form-check-label small fw-semibold text-dark" for="check2{{ $proposal->id }}">
                                                            Format Checking Completed
                                                        </label>
                                                    </div>
                                                    <div class="form-check mb-4">
                                                        <input class="form-check-input border-primary" type="checkbox" value="" id="check3{{ $proposal->id }}" required>
                                                        <label class="form-check-label small fw-semibold text-dark" for="check3{{ $proposal->id }}">
                                                            Similarity / Plagiarism Check Verified
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 p-4 pt-0">
                                                    <button type="button" class="btn btn-light px-4 py-2 rounded-pill fw-bold text-muted" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm">Log & Route to Admin</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted small italic">No endorsed proposals waiting to be received.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
