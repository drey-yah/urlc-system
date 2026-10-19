@php
    $statusMap = [
        'draft' => ['title' => 'Draft Created', 'color' => 'secondary', 'icon' => 'bi-file-earmark'],
        'pending_coordinator_endorsement' => ['title' => 'Submitted for Coordinator Endorsement', 'color' => 'warning', 'icon' => 'bi-send'],
        'pending_dean_noting' => ['title' => 'Endorsed by Coordinator & Forwarded to Dean', 'color' => 'primary', 'icon' => 'bi-check2-circle'],
        'noted_by_dean' => ['title' => 'Endorsement Noted by College Dean', 'color' => 'info', 'icon' => 'bi-journal-check'],
        'submitted_to_research_unit' => ['title' => 'Transmitted to Research Unit Support Staff', 'color' => 'secondary', 'icon' => 'bi-arrow-right-circle'],
        'pending_director_review' => ['title' => 'Received by Research Unit & Queued for Director', 'color' => 'primary', 'icon' => 'bi-inbox-fill'],
        'accepted_for_in_house_review' => ['title' => 'Accepted for In-House Review by Director', 'color' => 'success', 'icon' => 'bi-card-checklist'],
        'pending_reviewer_evaluation' => ['title' => 'Assigned for Peer Review', 'color' => 'info', 'icon' => 'bi-person-check'],
        'under_review' => ['title' => 'Under Technical Review', 'color' => 'info', 'icon' => 'bi-hourglass-split'],
        'revision_required' => ['title' => 'Returned for Revision by Reviewer', 'color' => 'warning', 'icon' => 'bi-arrow-repeat'],
        'approved_with_revisions' => ['title' => 'Reviewer Approved with Revisions', 'color' => 'primary', 'icon' => 'bi-check-all'],
        'approved' => 'Technical Review Approved',
        'final_copy_submitted' => ['title' => 'Final Revised Manuscript Submitted', 'color' => 'primary', 'icon' => 'bi-cloud-check'],
        'final_copy_noted_by_dean' => ['title' => 'Final Copy Noted by College Dean', 'color' => 'info', 'icon' => 'bi-clipboard-check'],
        'funds_certified' => ['title' => 'Funds Certified Available by Budget Officer', 'color' => 'success', 'icon' => 'bi-cash-coin'],
        'endorsed_to_vprei' => ['title' => 'Endorsed to VPREI for Executive Approval', 'color' => 'primary', 'icon' => 'bi-award'],
        'final_approved' => ['title' => 'Executive Approval Granted & NTP Issued by VPREI', 'color' => 'success', 'icon' => 'bi-patch-check-fill'],
        'final_rejected' => ['title' => 'Proposal Disapproved', 'color' => 'danger', 'icon' => 'bi-x-octagon-fill'],
        'rejected' => ['title' => 'Proposal Rejected', 'color' => 'danger', 'icon' => 'bi-x-circle-fill'],
        'returned_for_revision' => ['title' => 'Returned for Revision', 'color' => 'warning', 'icon' => 'bi-arrow-left-circle'],
        'archived' => ['title' => 'Archived to University Institutional Repository', 'color' => 'dark', 'icon' => 'bi-archive-fill'],
    ];

    $phaseTitles = [
        1 => 'Phase 1: Proposal Submission & Review',
        2 => 'Phase 2: Project Implementation & Milestones',
        3 => 'Phase 3: Research Presentation & Dissemination',
        4 => 'Phase 4: Refereed Journal Publication',
        5 => 'Phase 5: Institutional Archiving & Local Forum',
    ];

    $activities = $proposal->activities->sortByDesc('created_at');
    $milestones = $proposal->milestones->sortBy('target_date');
    $documents = $proposal->documents ? $proposal->documents->sortByDesc('version') : collect();
@endphp

<div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
    <!-- Collapsible Header -->
    <div class="card-header bg-white py-3 px-3 px-lg-4 d-flex justify-content-between align-items-center cursor-pointer accordion-workspace-header collapsed"
         data-bs-toggle="collapse" 
         data-bs-target="#lifecycleHistoryCollapse" 
         role="button" 
         aria-expanded="false"
         id="lifecycleHistoryHeader">
        <div class="d-flex align-items-center gap-2 gap-md-3 flex-grow-1 min-w-0">
            <i class="bi bi-chevron-down fs-5 text-muted workspace-chevron flex-shrink-0"></i>
            <div class="min-w-0">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="h6 fs-md-5 fw-bold text-dark mb-0 text-truncate">
                        <i class="bi bi-clock-history text-primary me-1"></i>
                        <span>Proposal Lifecycle, Stage &amp; Milestone History</span>
                    </h3>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-0.5" style="font-size: 0.7rem;">
                        {{ $activities->count() ?: 1 }} Actions Logged
                    </span>
                    @if($milestones->count() > 0)
                        <span class="badge bg-warning bg-opacity-10 text-dark rounded-pill px-2.5 py-0.5 d-none d-sm-inline" style="font-size: 0.7rem;">
                            {{ $milestones->count() }} Milestones
                        </span>
                    @endif
                </div>
                <p class="text-muted small mb-0 d-none d-md-block mt-0.5" style="font-size: 0.78rem;">
                    Chronological audit trail of routing endorsements, peer evaluations, executive clearances, and milestones.
                </p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-shrink-0 ms-2">
            <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill fw-semibold text-uppercase d-none d-sm-inline" style="font-size: 0.72rem;">
                {{ str_replace('_', ' ', $proposal->status) }}
            </span>
            <span class="btn btn-sm btn-light border rounded-pill px-3 py-1 text-muted small fw-medium d-inline-flex align-items-center gap-1.5" style="font-size: 0.75rem;">
                <span class="toggle-text">Show History</span>
                <i class="bi bi-chevron-down small"></i>
            </span>
        </div>
    </div>

    <!-- Collapsible Container (starts collapsed to save space) -->
    <div class="collapse" id="lifecycleHistoryCollapse">
        <!-- Sub Tabs inside collapsible -->
        <div class="bg-light bg-opacity-50 px-3 px-md-4 pt-3 border-top border-bottom">
            <ul class="nav nav-tabs border-bottom-0 gap-2" id="historyTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2 border-0 rounded-top-3" id="stages-tab" data-bs-toggle="tab" data-bs-target="#stages-content" type="button" role="tab" aria-selected="true" style="font-size: 0.85rem;">
                        <i class="bi bi-diagram-3-fill text-primary"></i>
                        <span>Stage &amp; Routing History</span>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2" style="font-size: 0.7rem;">
                            {{ $activities->count() ?: 1 }}
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2 border-0 rounded-top-3" id="milestones-tab" data-bs-toggle="tab" data-bs-target="#milestones-content" type="button" role="tab" aria-selected="false" style="font-size: 0.85rem;">
                        <i class="bi bi-flag-fill text-warning"></i>
                        <span>Milestones &amp; Deliverables</span>
                        <span class="badge bg-warning bg-opacity-10 text-dark rounded-pill px-2" style="font-size: 0.7rem;">
                            {{ $milestones->count() }}
                        </span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-semibold d-inline-flex align-items-center gap-2 px-3 py-2 border-0 rounded-top-3" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs-content" type="button" role="tab" aria-selected="false" style="font-size: 0.85rem;">
                        <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
                        <span>Document Versions</span>
                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2" style="font-size: 0.7rem;">
                            {{ $documents->count() }}
                        </span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-3 p-md-4">
            <div class="tab-content" id="historyTabsContent">
            
            <!-- ── TAB 1: STAGE & ROUTING AUDIT TIMELINE ── -->
            <div class="tab-pane fade show active" id="stages-content" role="tabpanel" aria-labelledby="stages-tab">
                <div class="timeline-container position-relative py-2 ps-2 ps-md-4">
                    
                    @if($activities->count() > 0)
                        @foreach($activities as $activity)
                            @php
                                $props = $activity->properties ?? [];
                                $attrs = $props['attributes'] ?? [];
                                $old = $props['old'] ?? [];
                                $causer = $activity->causer;
                                $causerName = $causer ? $causer->name : 'System';
                                $causerRole = $causer ? ucfirst(str_replace('_', ' ', $causer->role)) : 'Automated';
                                
                                // Default attributes
                                $eventTitle = 'Proposal Updated';
                                $eventDesc = 'Proposal details updated by ' . $causerName;
                                $eventColor = 'secondary';
                                $eventIcon = 'bi-arrow-clockwise';
                                $badgeText = 'Update';

                                if ($activity->event === 'created') {
                                    $eventTitle = 'Proposal Submitted';
                                    $eventDesc = 'Initial proposal manuscript drafted and submitted by ' . $causerName . ' for administrative endorsement.';
                                    $eventColor = 'primary';
                                    $eventIcon = 'bi-file-earmark-plus-fill';
                                    $badgeText = 'Phase 1 Initial';
                                } elseif (isset($attrs['current_phase']) && (!isset($old['current_phase']) || $attrs['current_phase'] != $old['current_phase'])) {
                                    $newPhase = $attrs['current_phase'];
                                    $eventTitle = "Advanced to Phase {$newPhase}";
                                    $eventDesc = ($phaseTitles[$newPhase] ?? "Phase {$newPhase}") . " initiated by {$causerName} ({$causerRole}).";
                                    $eventColor = 'purple';
                                    $eventIcon = 'bi-award-fill';
                                    $badgeText = "Phase {$newPhase}";
                                } elseif (isset($attrs['status']) && (!isset($old['status']) || $attrs['status'] != $old['status'])) {
                                    $st = $attrs['status'];
                                    $stInfo = $statusMap[$st] ?? null;
                                    $eventTitle = is_array($stInfo) ? $stInfo['title'] : ($stInfo ?? ucwords(str_replace('_', ' ', $st)));
                                    $eventColor = is_array($stInfo) ? $stInfo['color'] : 'primary';
                                    $eventIcon = is_array($stInfo) ? $stInfo['icon'] : 'bi-check-circle-fill';
                                    
                                    $oldTitle = isset($old['status']) ? (is_array($statusMap[$old['status']] ?? null) ? $statusMap[$old['status']]['title'] : ucwords(str_replace('_', ' ', $old['status']))) : 'Initial Status';
                                    $eventDesc = "Workflow progressed from \"{$oldTitle}\" by {$causerName} ({$causerRole}).";
                                    $badgeText = strtoupper(str_replace('_', ' ', $st));
                                } elseif (isset($attrs['reviewer_id'])) {
                                    $eventTitle = 'Peer Reviewer Assigned';
                                    $eventDesc = "Technical evaluator assigned to review manuscript by {$causerName} ({$causerRole}).";
                                    $eventColor = 'info';
                                    $eventIcon = 'bi-person-check-fill';
                                    $badgeText = 'Review Assignment';
                                }
                            @endphp

                            <div class="timeline-item d-flex gap-3 position-relative pb-4">
                                <!-- Vertical connecting line -->
                                @if(!$loop->last)
                                    <div class="timeline-line position-absolute" style="left: 19px; top: 38px; bottom: 0; width: 2px; background: #e2e8f0;"></div>
                                @endif

                                <!-- Node Icon -->
                                <div class="timeline-node rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0 shadow-xs z-1" 
                                     style="width: 40px; height: 40px; background: {{ $eventColor === 'purple' ? 'linear-gradient(135deg, #8B5CF6, #6D28D9)' : ($eventColor === 'success' ? 'linear-gradient(135deg, #10B981, #059669)' : ($eventColor === 'warning' ? 'linear-gradient(135deg, #F59E0B, #D97706)' : ($eventColor === 'danger' ? 'linear-gradient(135deg, #EF4444, #DC2626)' : 'linear-gradient(135deg, #3B82F6, #1D4ED8)'))) }};">
                                    <i class="bi {{ $eventIcon }}" style="font-size: 1.1rem;"></i>
                                </div>

                                <!-- Node Details -->
                                <div class="timeline-content flex-grow-1 p-3 rounded-3 bg-light border border-opacity-75">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-1.5 mb-1.5">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <h4 class="fw-bold text-dark fs-6 mb-0">{{ $eventTitle }}</h4>
                                            <span class="badge bg-white text-dark border rounded-pill px-2.5 py-0.5" style="font-size: 0.68rem;">
                                                {{ $badgeText }}
                                            </span>
                                        </div>
                                        <div class="text-muted small d-inline-flex align-items-center gap-1" style="font-size: 0.74rem;">
                                            <i class="bi bi-clock"></i>
                                            <span>{{ $activity->created_at->format('M d, Y • h:i A') }}</span>
                                            <span class="d-none d-md-inline text-secondary">({{ $activity->created_at->diffForHumans() }})</span>
                                        </div>
                                    </div>

                                    <p class="text-secondary small mb-2 lh-base" style="font-size: 0.82rem;">
                                        {{ $eventDesc }}
                                    </p>

                                    <!-- Actor Badge -->
                                    <div class="d-flex align-items-center gap-2 pt-1 border-top border-light-subtle">
                                        <div class="rounded-circle bg-white text-primary fw-bold d-flex align-items-center justify-content-center border" style="width: 24px; height: 24px; font-size: 0.65rem;">
                                            {{ strtoupper(substr($causerName, 0, 1)) }}
                                        </div>
                                        <span class="text-dark small fw-medium" style="font-size: 0.76rem;">{{ $causerName }}</span>
                                        @if($causer && $causer->role)
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-0.5 rounded-pill" style="font-size: 0.65rem;">
                                                {{ ucfirst(str_replace('_', ' ', $causer->role)) }}
                                            </span>
                                        @endif
                                        @if($causer && $causer->campus)
                                            <span class="text-muted" style="font-size: 0.72rem;">
                                                <i class="bi bi-geo-alt me-0.5"></i>{{ $causer->campus }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback for proposals created prior to activity logging -->
                        <div class="timeline-item d-flex gap-3 position-relative pb-4">
                            <div class="timeline-line position-absolute" style="left: 19px; top: 38px; bottom: 0; width: 2px; background: #e2e8f0;"></div>
                            <div class="timeline-node rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0 shadow-xs z-1" 
                                 style="width: 40px; height: 40px; background: linear-gradient(135deg, #10B981, #059669);">
                                <i class="bi bi-check2-circle" style="font-size: 1.1rem;"></i>
                            </div>
                            <div class="timeline-content flex-grow-1 p-3 rounded-3 bg-light border">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h4 class="fw-bold text-dark fs-6 mb-0">Current State: {{ ucwords(str_replace('_', ' ', $proposal->status)) }}</h4>
                                    <span class="text-muted small">{{ $proposal->updated_at->format('M d, Y • h:i A') }}</span>
                                </div>
                                <p class="text-muted small mb-0">Proposal is currently in <strong>Phase {{ $proposal->current_phase }}</strong> with active status <strong>{{ str_replace('_', ' ', $proposal->status) }}</strong>.</p>
                            </div>
                        </div>

                        <div class="timeline-item d-flex gap-3 position-relative">
                            <div class="timeline-node rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0 shadow-xs z-1" 
                                 style="width: 40px; height: 40px; background: linear-gradient(135deg, #3B82F6, #1D4ED8);">
                                <i class="bi bi-file-earmark-plus-fill" style="font-size: 1.1rem;"></i>
                            </div>
                            <div class="timeline-content flex-grow-1 p-3 rounded-3 bg-light border">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h4 class="fw-bold text-dark fs-6 mb-0">Initial Proposal Submission</h4>
                                    <span class="text-muted small">{{ $proposal->created_at->format('M d, Y • h:i A') }}</span>
                                </div>
                                <p class="text-muted small mb-0">Submitted by <strong>{{ $proposal->user->name }}</strong> (Lead Researcher).</p>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <!-- ── TAB 2: MILESTONES & DELIVERABLES HISTORY ── -->
            <div class="tab-pane fade" id="milestones-content" role="tabpanel" aria-labelledby="milestones-tab">
                @if($milestones->count() > 0)
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <p class="text-muted small mb-0">
                            <strong>{{ $milestones->where('status', 'approved')->count() }} of {{ $milestones->count() }}</strong> implementation milestones approved.
                        </p>
                        @if($proposal->current_phase >= 2)
                            <a href="#phase2WorkspaceCollapse" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="collapse" data-bs-target="#phase2WorkspaceCollapse">
                                <i class="bi bi-bar-chart-steps me-1"></i> View Interactive Gantt Chart
                            </a>
                        @endif
                    </div>

                    <div class="d-flex flex-column gap-2.5">
                        @foreach($milestones as $m)
                            <div class="p-3 bg-light rounded-3 border d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0 mt-1" 
                                         style="width: 32px; height: 32px; background: {{ $m->status === 'approved' ? '#10B981' : ($m->status === 'rejected' ? '#EF4444' : '#F59E0B') }};">
                                        <i class="bi {{ $m->status === 'approved' ? 'bi-check2' : ($m->status === 'rejected' ? 'bi-x' : 'bi-hourglass-split') }}" style="font-size: 1rem;"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <h5 class="fw-bold text-dark small mb-0">{{ $m->title }}</h5>
                                            <span class="badge {{ $m->status === 'approved' ? 'bg-success' : ($m->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }} rounded-pill px-2.5 py-0.5" style="font-size: 0.65rem;">
                                                {{ ucfirst($m->status) }}
                                            </span>
                                        </div>
                                        <p class="text-secondary small mb-1" style="font-size: 0.78rem;">{{ $m->description }}</p>
                                        <div class="text-muted d-flex align-items-center gap-2 flex-wrap" style="font-size: 0.72rem;">
                                            @if($m->start_date && $m->target_date)
                                                <span><i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($m->start_date)->format('M d, Y') }} &rarr; {{ \Carbon\Carbon::parse($m->target_date)->format('M d, Y') }}</span>
                                            @elseif($m->start_date)
                                                <span><i class="bi bi-calendar3 me-1"></i>Starts: {{ \Carbon\Carbon::parse($m->start_date)->format('M d, Y') }}</span>
                                            @endif
                                            <span>&bull;</span>
                                            <span>Logged: {{ $m->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2 flex-shrink-0 w-100 w-md-auto justify-content-end">
                                    @if($m->document_path)
                                        <a href="{{ route('file.serve', ['path' => $m->document_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 0.75rem;">
                                            <i class="bi bi-paperclip me-1"></i> Attachment
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 py-md-5 text-muted">
                        <i class="bi bi-flag fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        <h6 class="fw-bold mb-1">No Implementation Milestones Yet</h6>
                        <p class="small text-muted mb-0">Milestones are scheduled and submitted during <strong>Phase 2: Project Implementation</strong>.</p>
                    </div>
                @endif
            </div>

            <!-- ── TAB 3: DOCUMENT VERSIONS HISTORY ── -->
            <div class="tab-pane fade" id="docs-content" role="tabpanel" aria-labelledby="docs-tab">
                @if($documents->count() > 0)
                    <!-- Mobile View -->
                    <div class="d-flex flex-column gap-2.5 d-md-none">
                        @foreach($documents as $doc)
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                            Version {{ $doc->version }}
                                        </span>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                            Phase {{ $doc->phase }}
                                        </span>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.72rem;">{{ $doc->created_at->format('M d, Y') }}</small>
                                </div>
                                <div class="p-2 bg-white rounded-2 border mb-2">
                                    <small class="text-muted text-uppercase d-block" style="font-size: 0.65rem;">Document Tag</small>
                                    <span class="fw-mono text-dark small text-break" style="font-size: 0.75rem;">{{ $doc->document_tag }}</span>
                                </div>
                                <a href="{{ route('file.serve', ['path' => $doc->file_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 rounded-pill fw-bold" style="font-size: 0.78rem;">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> View / Download PDF
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <!-- Desktop View Table -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-nowrap small text-uppercase text-muted">
                                    <th>Document Tag</th>
                                    <th>Version</th>
                                    <th>Phase</th>
                                    <th>Date Uploaded</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($documents as $doc)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary text-wrap" style="max-width: 260px; font-size: 0.74rem;">
                                                {{ $doc->document_tag }}
                                            </span>
                                        </td>
                                        <td class="fw-bold text-primary">V{{ $doc->version }}</td>
                                        <td>Phase {{ $doc->phase }}</td>
                                        <td class="text-muted small">{{ $doc->created_at->format('M d, Y • h:i A') }}</td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('file.serve', ['path' => $doc->file_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 0.75rem;">
                                                <i class="bi bi-download me-1"></i> View PDF
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @elseif($proposal->document_path)
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 p-3 border rounded-3 bg-light">
                        <div>
                            <h6 class="mb-0 fw-bold">Original Proposal Manuscript</h6>
                            <small class="text-muted">Primary Submission Document</small>
                        </div>
                        <a href="{{ route('file.serve', ['path' => $proposal->document_path]) }}" target="_blank" class="btn btn-sm btn-outline-primary px-4 rounded-pill">
                            <i class="bi bi-download me-1"></i> View PDF
                        </a>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-file-earmark-x fs-2 d-block mb-1 text-secondary opacity-50"></i>
                        <span class="small">No uploaded document versions on file.</span>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const historyCollapse = document.getElementById('lifecycleHistoryCollapse');
        const header = document.getElementById('lifecycleHistoryHeader');
        if (historyCollapse && header) {
            const toggleText = header.querySelector('.toggle-text');
            historyCollapse.addEventListener('show.bs.collapse', function () {
                if (toggleText) toggleText.textContent = 'Hide History';
            });
            historyCollapse.addEventListener('hide.bs.collapse', function () {
                if (toggleText) toggleText.textContent = 'Show History';
            });
        }
    });
</script>
