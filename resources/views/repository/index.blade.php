<x-app-layout>
    <div class="mb-5">
        <h1 class="h3 fw-bold mb-1">Centralized Research Document Repository</h1>
        <p class="text-muted">Browse and search through all completed research projects.</p>
    </div>

    <!-- Search Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('repository.index') }}" class="d-flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by proposal code, title, abstract, or field..." class="form-control bg-light border-0 py-2 px-3">
                <button type="submit" class="btn btn-dark px-4 fw-bold shadow-sm rounded-3">
                    <i class="bi bi-search me-1"></i> Search
                </button>
            </form>
        </div>
    </div>

    @if($completedResearches->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-journal-x display-4 d-block mb-3 text-secondary opacity-50"></i>
            <p>No completed research found in the repository.</p>
        </div>
    @else
        <div class="row g-5">
            @foreach($completedResearches as $research)
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <!-- Top Bar Labels -->
                        <div class="card-header bg-white border-bottom p-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 shadow-sm d-flex align-items-center gap-2" style="font-size: 0.75rem;">
                                    <i class="bi bi-tag-fill"></i>
                                    {{ $research->proposal_code ?? 'NO TAG' }}
                                </span>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 shadow-sm text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                    {{ $research->research_field }}
                                </span>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2" style="font-size: 0.75rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Completed: {{ $research->phase_updated_at ? \Carbon\Carbon::parse($research->phase_updated_at)->format('M d, Y') : 'N/A' }}
                            </span>
                        </div>
                        
                        <div class="card-body p-4 p-lg-5">
                            <!-- Title Row -->
                            <div class="mb-4 pb-3 border-bottom border-light">
                                <small class="text-muted fw-bold text-uppercase d-block mb-2" style="letter-spacing: 0.05em;">Research Title</small>
                                <h3 class="h4 fw-bold text-dark mb-0 lh-base">{{ $research->title }}</h3>
                            </div>
                            
                            <!-- Researcher Row -->
                            <div class="row g-4 mb-4 pb-3 border-bottom border-light">
                                <div class="col-md-12">
                                    <small class="text-muted fw-bold text-uppercase d-block mb-3" style="letter-spacing: 0.05em;">Lead Researcher</small>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 45px; height: 45px; font-size: 1.1rem;">
                                            {{ substr($research->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-5">{{ $research->user->name }}</div>
                                            @if($research->user->department)
                                                <div class="small text-muted fw-medium"><i class="bi bi-building me-1"></i>{{ $research->user->department }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description Row -->
                            <div class="mb-4">
                                <small class="text-muted fw-bold text-uppercase d-block mb-3" style="letter-spacing: 0.05em;">Description (Abstract)</small>
                                <div class="bg-light p-4 rounded-4 text-muted" style="border: 1px solid rgba(0,0,0,0.03); line-height: 1.7; text-align: justify;">
                                    {{ Str::limit($research->abstract, 500) }}
                                </div>
                            </div>

                            <!-- Rationale Row -->
                            @if($research->rationale)
                            <div class="mb-2">
                                <small class="text-muted fw-bold text-uppercase d-block mb-3" style="letter-spacing: 0.05em;">Rationale</small>
                                <div class="bg-light p-4 rounded-4 text-muted" style="border: 1px solid rgba(0,0,0,0.03); line-height: 1.7; text-align: justify;">
                                    {{ Str::limit($research->rationale, 500) }}
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Action Row -->
                        <div class="card-footer bg-white border-top p-4 d-flex justify-content-end">
                            <a href="{{ route('proposal.show', $research->id) }}" class="btn btn-primary px-4 py-2 fw-bold shadow-sm d-inline-flex align-items-center gap-2 rounded-pill">
                                <i class="bi bi-file-earmark-pdf-fill fs-5"></i> View Documents & Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-5 d-flex justify-content-center">
            {{ $completedResearches->links() }}
        </div>
    @endif
</x-app-layout>
