<x-app-layout>
    <div class="mb-5">
        <h1 class="h3 fw-bold mb-1">Recording Staff Dashboard</h1>
        <p class="text-muted">Welcome, {{ Auth::user()->name }} | Track & Record Research Lifecycle</p>
    </div>

    <!-- Tabs for different ledgers -->
    <ul class="nav nav-pills mb-4 gap-2" id="ledgerTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 fw-bold" data-bs-toggle="pill" data-bs-target="#routing" type="button" role="tab">Routing History</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold text-info" data-bs-toggle="pill" data-bs-target="#ongoing" type="button" role="tab">Ongoing Researches</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-bold text-success" data-bs-toggle="pill" data-bs-target="#completed" type="button" role="tab">Completed Researches</button>
        </li>
    </ul>

    <div class="tab-content" id="ledgerTabsContent">
        <!-- Routing History -->
        <div class="tab-pane fade show active" id="routing" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom-0">
                    <h5 class="mb-0 fw-bold">Active Proposals Routing Tracker</h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Code</th>
                                <th>Title</th>
                                <th>Researcher</th>
                                <th class="text-center">Current Phase</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($routingHistory as $proposal)
                            <tr>
                                <td class="ps-4"><span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 0.65rem;">{{ $proposal->proposal_code ?? 'N/A' }}</span></td>
                                <td>
                                    <div class="fw-bold text-dark">{{ Str::limit($proposal->title, 40) }}</div>
                                </td>
                                <td>{{ $proposal->user->name }}</td>
                                <td class="text-center"><span class="fw-medium text-primary">Phase {{ $proposal->current_phase }}</span></td>
                                <td class="text-center"><span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill small fw-bold">{{ strtoupper(str_replace('_', ' ', $proposal->status)) }}</span></td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted small italic">No active proposals routing at the moment.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ongoing Researches -->
        <div class="tab-pane fade" id="ongoing" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-info border-top border-4">
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom-0">
                    <h5 class="mb-0 fw-bold text-info">Ongoing Researches Ledger</h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Code</th>
                                <th>Title</th>
                                <th>Researcher</th>
                                <th class="text-center">Phase</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ongoingResearches as $proposal)
                            <tr>
                                <td class="ps-4"><span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 0.65rem;">{{ $proposal->proposal_code ?? 'N/A' }}</span></td>
                                <td>
                                    <div class="fw-bold text-dark">{{ Str::limit($proposal->title, 40) }}</div>
                                </td>
                                <td>{{ $proposal->user->name }}</td>
                                <td class="text-center"><span class="fw-medium text-info">Phase {{ $proposal->current_phase }}</span></td>
                                <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill small fw-bold">{{ strtoupper(str_replace('_', ' ', $proposal->status)) }}</span></td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted small italic">No ongoing researches.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Completed Researches -->
        <div class="tab-pane fade" id="completed" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden border-success border-top border-4">
                <div class="card-header bg-white pt-4 pb-3 px-4 border-bottom-0">
                    <h5 class="mb-0 fw-bold text-success">Completed Researches Ledger</h5>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Code</th>
                                <th>Title</th>
                                <th>Researcher</th>
                                <th class="text-center">Phase</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($completedResearches as $proposal)
                            <tr>
                                <td class="ps-4"><span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1" style="font-size: 0.65rem;">{{ $proposal->proposal_code ?? 'N/A' }}</span></td>
                                <td>
                                    <div class="fw-bold text-dark">{{ Str::limit($proposal->title, 40) }}</div>
                                </td>
                                <td>{{ $proposal->user->name }}</td>
                                <td class="text-center"><span class="fw-medium text-success">Phase {{ $proposal->current_phase }}</span></td>
                                <td class="text-center"><span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill small fw-bold">{{ strtoupper(str_replace('_', ' ', $proposal->status)) }}</span></td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('proposal.show', $proposal->id) }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill">View</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted small italic">No completed researches yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
