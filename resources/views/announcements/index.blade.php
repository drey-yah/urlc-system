<x-app-layout>
    <div class="mb-4">
        <h1 class="h3 fw-bold d-flex align-items-center gap-3 mb-2">
            <i class="bi bi-bell text-primary"></i> Call for Papers
        </h1>
        <p class="text-muted mb-0">Stay updated with the latest announcements and submission guidelines</p>
    </div>

    <!-- Gradient Submission Banner -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="gradient-banner p-4 p-lg-5">
            <h2 class="h3 fw-bold mb-3">Submit Your Research Proposal</h2>
            <p class="mb-5 opacity-90" style="max-width: 800px;">
                We welcome innovative research proposals across all phases. Our review committee is committed to supporting groundbreaking research that advances knowledge and creates real-world impact.
            </p>

            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <div class="bg-white rounded-4 p-4 text-dark h-100 shadow-sm">
                        <small class="text-primary fw-bold text-uppercase mb-2 d-block">Phase 1</small>
                        <p class="small mb-0 text-muted">Initial proposal submission with preliminary research objectives</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white rounded-4 p-4 text-dark h-100 shadow-sm">
                        <small class="text-primary fw-bold text-uppercase mb-2 d-block">Phase 2-4</small>
                        <p class="small mb-0 text-muted">Detailed methodology and comprehensive research plan</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white rounded-4 p-4 text-dark h-100 shadow-sm">
                        <small class="text-primary fw-bold text-uppercase mb-2 d-block">Phase 5</small>
                        <p class="small mb-0 text-muted">Final implementation with results and analysis</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-lg-8">
            <h3 class="h5 fw-bold mb-4">Recent Announcements</h3>
            
            @forelse($announcements as $announcement)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-file-earmark-text text-primary h4 mb-0"></i>
                                <h4 class="h5 fw-bold mb-0">{{ $announcement->title }}</h4>
                            </div>
                            <small class="text-muted d-flex align-items-center gap-2">
                                <i class="bi bi-calendar3"></i> {{ $announcement->created_at->format('Y-m-d') }}
                            </small>
                        </div>
                        <p class="text-muted mb-4" style="line-height: 1.6;">{{ Str::limit($announcement->content, 200) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2 small text-muted">
                                <i class="bi bi-person"></i> Posted by {{ $announcement->user->name }}
                            </div>
                            <button class="btn btn-link text-primary p-0 text-decoration-none small fw-bold" data-bs-toggle="modal" data-bs-target="#annModal{{ $announcement->id }}">
                                Read More <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Announcement Modal -->
                <div class="modal fade" id="annModal{{ $announcement->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                            <div class="modal-header border-0 px-4 pt-4 pb-0">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi bi-file-earmark-text text-primary h4 mb-0"></i>
                                    <h5 class="modal-title fw-bold">{{ $announcement->title }}</h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-modal="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <div class="d-flex align-items-center gap-3 mb-4 small text-muted">
                                    <span><i class="bi bi-calendar3"></i> {{ $announcement->created_at->format('M d, Y') }}</span>
                                    <span><i class="bi bi-person"></i> {{ $announcement->user->name }}</span>
                                </div>
                                <div class="text-muted" style="line-height: 1.8; white-space: pre-wrap;">{{ $announcement->content }}</div>
                            </div>
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm p-5 text-center">
                    <p class="text-muted mb-0">No announcements available.</p>
                </div>
            @endforelse
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="h5 fw-bold mb-4">Submission Guidelines</h3>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-4 d-flex align-items-start gap-3">
                            <div class="guideline-step flex-shrink-0">1</div>
                            <div>
                                <p class="mb-0 small"><strong class="text-dark">Prepare Your Proposal:</strong> Ensure your research objectives are clearly defined with preliminary data if available.</p>
                            </div>
                        </li>
                        <li class="mb-4 d-flex align-items-start gap-3">
                            <div class="guideline-step flex-shrink-0">2</div>
                            <div>
                                <p class="mb-0 small"><strong class="text-dark">Submit Through Portal:</strong> Use the researcher dashboard to submit your proposal with all required documentation.</p>
                            </div>
                        </li>
                        <li class="mb-4 d-flex align-items-start gap-3">
                            <div class="guideline-step flex-shrink-0">3</div>
                            <div>
                                <p class="mb-0 small"><strong class="text-dark">Review Process:</strong> Our expert reviewers will evaluate your proposal and provide detailed feedback within 2-3 weeks.</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div class="guideline-step flex-shrink-0">4</div>
                            <div>
                                <p class="mb-0 small"><strong class="text-dark">Email Notifications:</strong> You will receive automated email updates at each stage of the review process.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
