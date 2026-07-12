<x-app-layout>
    <div class="mb-5">
        <h1 class="h3 fw-bold mb-1">Budget Certification & Funds Verification</h1>
        <p class="text-muted">Welcome, Budget Officer {{ Auth::user()->name }}</p>
    </div>

    <!-- Section: Proposals Awaiting Certification -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4">
            <h5 class="mb-0 fw-bold text-primary">Proposals Awaiting Budget Certification</h5>
            <small class="text-muted">Review proposals noted by College Deans and certify them for availability of funds before Research Director endorsement to VPREI.</small>
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
                    @forelse($pendingCertification as $proposal)
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
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                    {{ substr($proposal->user->name, 0, 1) }}
                                </div>
                                <div class="small fw-bold">{{ $proposal->user->name }}</div>
                            </div>
                        </td>
                        <td class="text-center py-4">
                            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill small fw-bold">
                                AWAITING FUNDS CERTIFICATION
                            </span>
                        </td>
                        <td class="pe-4 text-end py-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-outline-primary btn-sm px-4 py-2 d-inline-flex align-items-center gap-2 rounded-pill fw-bold shadow-sm">
                                    <i class="bi bi-eye-fill fs-6"></i> View Details
                                </a>
                                <form action="{{ route('budget.certify', $proposal->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm px-4 py-2 d-inline-flex align-items-center gap-2 rounded-pill fw-bold shadow-sm" onclick="return confirm('Are you sure you want to certify this research proposal for availability of funds?');">
                                        <i class="bi bi-cash-stack fs-6"></i> Certify Funds
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted small italic">No proposals awaiting budget certification.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section: Certified Proposals History -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4">
            <h5 class="mb-0 fw-bold text-success">Certified Proposals History</h5>
            <small class="text-muted">History of proposals certified by the Budget Office.</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0 text-muted small uppercase">Proposal Code</th>
                        <th class="py-3 border-0 text-muted small uppercase">Proposal Title</th>
                        <th class="py-3 border-0 text-muted small uppercase">Researcher</th>
                        <th class="py-3 text-center border-0 text-muted small uppercase">Current status</th>
                        <th class="pe-4 py-3 text-end border-0 text-muted small uppercase">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certifiedProposals as $proposal)
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
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                    {{ substr($proposal->user->name, 0, 1) }}
                                </div>
                                <div class="small fw-bold">{{ $proposal->user->name }}</div>
                            </div>
                        </td>
                        <td class="text-center py-4">
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill small fw-bold text-uppercase">
                                {{ str_replace('_', ' ', $proposal->status) }}
                            </span>
                        </td>
                        <td class="pe-4 text-end py-4">
                            <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-outline-secondary btn-sm px-4 py-2 d-inline-flex align-items-center gap-2 rounded-pill fw-bold shadow-sm">
                                <i class="bi bi-eye-fill fs-6"></i> View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted small italic">No certified proposals history.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
