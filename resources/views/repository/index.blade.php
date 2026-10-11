<x-app-layout>
    <!-- Page Header -->
    <div class="mb-3 mb-md-4">
        <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-journal-bookmark-fill text-primary"></i> Research Document Repository
        </h1>
        <p class="text-muted small mb-0">Browse, search, and access all completed institutional research projects.</p>
    </div>

    <!-- Search Bar Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-3 mb-md-4">
        <div class="card-body p-3 p-md-4">
            <form method="GET" action="{{ route('repository.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-9 col-lg-10">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 ps-3 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search by code, title, abstract, or field..." 
                                   class="form-control bg-light border-0 py-2.5 px-2"
                                   style="font-size: 0.9rem;">
                            @if(request('search'))
                                <a href="{{ route('repository.index') }}" class="input-group-text bg-light border-0 text-muted pe-3 text-decoration-none" title="Clear search">
                                    <i class="bi bi-x-circle-fill text-secondary"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-md-3 col-lg-2">
                        <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </div>
                </div>

                @if(request('search'))
                    <div class="mt-2.5 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <small class="text-muted" style="font-size: 0.8rem;">
                            Showing results for: <strong class="text-dark">"{{ request('search') }}"</strong>
                        </small>
                        <a href="{{ route('repository.index') }}" class="btn btn-sm btn-link text-decoration-none text-danger p-0 fw-semibold" style="font-size: 0.78rem;">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset Search Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Repository Content -->
    @if($completedResearches->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 text-center my-3 my-md-4">
            <div class="mb-3 d-inline-flex p-3 bg-secondary bg-opacity-10 text-secondary rounded-circle mx-auto">
                <i class="bi {{ request('search') ? 'bi-search' : 'bi-journal-x' }} fs-1 text-muted"></i>
            </div>
            @if(request('search'))
                <h5 class="fw-bold text-dark mb-1">No Matching Research Found</h5>
                <p class="text-muted small mb-3 mx-auto" style="max-width: 440px; line-height: 1.6;">
                    We couldn't find any completed projects matching <strong>"{{ request('search') }}"</strong>. Try searching with different terms or reset your query.
                </p>
                <div>
                    <a href="{{ route('repository.index') }}" class="btn btn-primary btn-sm px-4 py-2 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Search Filter
                    </a>
                </div>
            @else
                <h5 class="fw-bold text-dark mb-1">No Completed Research in Repository</h5>
                <p class="text-muted small mb-0 mx-auto" style="max-width: 460px; line-height: 1.6;">
                    Institutional research projects that successfully complete Phase 5 (Annual SUC In-House Forum presentation and terminal certifications) will automatically be published and archived here.
                </p>
            @endif
        </div>
    @else
        <div class="row g-3 g-md-4">
            @foreach($completedResearches as $research)
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <!-- Top Bar Labels -->
                        <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2.5 py-1.5 rounded-pill d-inline-flex align-items-center gap-1.5 fw-bold" style="font-size: 0.75rem;">
                                    <i class="bi bi-tag-fill"></i>
                                    {{ $research->proposal_code ?? 'NO CODE' }}
                                </span>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1.5 rounded-pill text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.04em;">
                                    {{ $research->research_field }}
                                </span>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded-pill fw-semibold" style="font-size: 0.75rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Completed: {{ $research->phase_updated_at ? \Carbon\Carbon::parse($research->phase_updated_at)->format('M d, Y') : 'Archived' }}
                            </span>
                        </div>
                        
                        <div class="card-body p-3 p-md-4 p-lg-5">
                            <!-- Title Row -->
                            <div class="mb-3 mb-md-4 pb-3 border-bottom border-light">
                                <small class="text-muted fw-bold text-uppercase d-block mb-1" style="letter-spacing: 0.05em; font-size: 0.72rem;">Research Title</small>
                                <h3 class="h5 fs-md-4 fw-bold text-dark mb-0 lh-base">{{ $research->title }}</h3>
                            </div>
                            
                            <!-- Researcher Row -->
                            <div class="mb-3 mb-md-4 pb-3 border-bottom border-light">
                                <small class="text-muted fw-bold text-uppercase d-block mb-2" style="letter-spacing: 0.05em; font-size: 0.72rem;">Lead Researcher</small>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0" style="width: 42px; height: 42px; font-size: 1rem;">
                                        {{ strtoupper(substr($research->user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0" style="min-width: 0;">
                                        <div class="fw-bold text-dark fs-6">{{ $research->user->name }}</div>
                                        @if($research->user->department)
                                            <div class="small text-muted fw-medium" style="font-size: 0.8rem;"><i class="bi bi-building me-1"></i>{{ $research->user->department }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Description (Abstract) -->
                            <div class="mb-3 mb-md-4">
                                <small class="text-muted fw-bold text-uppercase d-block mb-2" style="letter-spacing: 0.05em; font-size: 0.72rem;">Description (Abstract)</small>
                                <div class="bg-light p-3 p-md-4 rounded-3 text-secondary border" style="line-height: 1.65; font-size: 0.88rem; text-align: justify;">
                                    {{ Str::limit($research->abstract, 500) }}
                                </div>
                            </div>

                            <!-- Rationale Row -->
                            @if($research->rationale)
                            <div class="mb-2">
                                <small class="text-muted fw-bold text-uppercase d-block mb-2" style="letter-spacing: 0.05em; font-size: 0.72rem;">Rationale</small>
                                <div class="bg-light p-3 p-md-4 rounded-3 text-secondary border" style="line-height: 1.65; font-size: 0.88rem; text-align: justify;">
                                    {{ Str::limit($research->rationale, 500) }}
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Action Row -->
                        <div class="card-footer bg-white border-top p-3 p-md-4 d-flex justify-content-end">
                            <a href="{{ route('proposal.show', $research->id) }}" class="btn btn-primary px-4 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center justify-content-center gap-2 rounded-pill col-12 col-sm-auto">
                                <i class="bi bi-file-earmark-pdf-fill fs-5"></i> View Documents &amp; Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4 mt-md-5 d-flex justify-content-center">
            {{ $completedResearches->links() }}
        </div>
    @endif
</x-app-layout>
