<x-app-layout>
    <!-- Unified Module Header -->
    <div class="mb-3 mb-md-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-megaphone-fill" style="color: var(--ua-maroon, #8C1D2C);"></i> Announcements &amp; Research Schedules
            </h1>
            <p class="text-muted small mb-0">Official university research calendar, call for papers, in-house reviews, and circulars.</p>
        </div>
        @if($isDirector)
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('announcements.index') }}" class="btn btn-outline-primary px-3 py-2 rounded-pill fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs" style="font-size: 0.85rem;">
                    <i class="bi bi-chat-left-text"></i> <span>View Announcements</span>
                </a>
                <button class="btn btn-primary px-3 px-md-4 py-2 rounded-pill fw-bold d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                    <i class="bi bi-calendar-plus-fill"></i> <span>Set New Schedule</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Unified Module Navigation Tabs -->
    <div class="d-flex flex-wrap align-items-center gap-2 mb-4 border-bottom pb-3">
        <a href="{{ route('schedules.index') }}" class="btn btn-sm rounded-pill px-3.5 py-2 fw-bold d-inline-flex align-items-center gap-2 btn-primary shadow-xs">
            <i class="bi bi-calendar2-week-fill"></i>
            <span>Research Schedules &amp; Deadlines</span>
            <span class="badge rounded-pill bg-white text-primary ms-1" style="font-size: 0.72rem;">{{ $stats['total_active'] }}</span>
        </a>
        <a href="{{ route('announcements.index') }}" class="btn btn-sm rounded-pill px-3.5 py-2 fw-bold d-inline-flex align-items-center gap-2 btn-light text-muted border">
            <i class="bi bi-megaphone-fill"></i>
            <span>Announcements Feed</span>
            @php $announcementTotal = \App\Models\Announcement::count(); @endphp
            <span class="badge rounded-pill bg-secondary text-white ms-1" style="font-size: 0.72rem;">{{ $announcementTotal }}</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-xs border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-xs border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Metrics Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Active Upcoming</span>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ $stats['total_active'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 44px; height: 44px;">
                        <i class="bi bi-calendar-check-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Call for Papers</span>
                        <h3 class="fw-bold text-warning mb-0 mt-1">{{ $stats['call_for_papers'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning" style="width: 44px; height: 44px;">
                        <i class="bi bi-megaphone-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">In-House Reviews</span>
                        <h3 class="fw-bold mb-0 mt-1" style="color: var(--ua-maroon, #8C1D2C);">{{ $stats['in_house_reviews'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10" style="width: 44px; height: 44px; color: var(--ua-maroon, #8C1D2C);">
                        <i class="bi bi-journal-check fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Inception Sessions</span>
                        <h3 class="fw-bold text-success mb-0 mt-1">{{ $stats['inception_meetings'] }}</h3>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 44px; height: 44px;">
                        <i class="bi bi-rocket-takeoff-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <!-- Event Type Pills -->
            <div class="d-flex flex-wrap gap-1.5">
                <a href="{{ route('schedules.index', array_merge(request()->except('type', 'page'), ['type' => 'all'])) }}" 
                   class="btn btn-sm rounded-pill px-3 fw-semibold {{ (!request()->filled('type') || request('type') === 'all') ? 'btn-primary' : 'btn-light text-muted' }}">
                    All Types
                </a>
                <a href="{{ route('schedules.index', array_merge(request()->except('type', 'page'), ['type' => 'call_for_paper'])) }}" 
                   class="btn btn-sm rounded-pill px-3 fw-semibold {{ request('type') === 'call_for_paper' ? 'btn-warning text-dark' : 'btn-light text-muted' }}">
                    <i class="bi bi-megaphone me-1"></i> Call for Papers
                </a>
                <a href="{{ route('schedules.index', array_merge(request()->except('type', 'page'), ['type' => 'in_house_review'])) }}" 
                   class="btn btn-sm rounded-pill px-3 fw-semibold {{ request('type') === 'in_house_review' ? 'btn-danger' : 'btn-light text-muted' }}">
                    <i class="bi bi-journal-check me-1"></i> In-House Review
                </a>
                <a href="{{ route('schedules.index', array_merge(request()->except('type', 'page'), ['type' => 'inception_meeting'])) }}" 
                   class="btn btn-sm rounded-pill px-3 fw-semibold {{ request('type') === 'inception_meeting' ? 'btn-success' : 'btn-light text-muted' }}">
                    <i class="bi bi-rocket-takeoff me-1"></i> Inception Schedule
                </a>
            </div>

            <!-- Timeframe selector -->
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-semibold">Show:</span>
                <div class="btn-group btn-group-sm rounded-pill bg-light p-0.5 border" role="group">
                    <a href="{{ route('schedules.index', array_merge(request()->except('timeframe', 'page'), ['timeframe' => 'all'])) }}" 
                       class="btn btn-sm rounded-pill {{ (!request()->filled('timeframe') || request('timeframe') === 'all') ? 'btn-white shadow-xs fw-bold' : 'btn-light text-muted' }}">
                        All
                    </a>
                    <a href="{{ route('schedules.index', array_merge(request()->except('timeframe', 'page'), ['timeframe' => 'upcoming'])) }}" 
                       class="btn btn-sm rounded-pill {{ request('timeframe') === 'upcoming' ? 'btn-white shadow-xs fw-bold' : 'btn-light text-muted' }}">
                        Upcoming
                    </a>
                    <a href="{{ route('schedules.index', array_merge(request()->except('timeframe', 'page'), ['timeframe' => 'past'])) }}" 
                       class="btn btn-sm rounded-pill {{ request('timeframe') === 'past' ? 'btn-white shadow-xs fw-bold' : 'btn-light text-muted' }}">
                        Past
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedules Feed -->
    <div class="row g-3 g-md-4">
        @forelse($schedules as $schedule)
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative transition-all" style="border-left: 5px solid {{ $schedule->event_type === 'call_for_paper' ? '#E5A93C' : ($schedule->event_type === 'in_house_review' ? '#8C1D2C' : ($schedule->event_type === 'inception_meeting' ? '#10B981' : '#64748b')) }} !important;">
                    <div class="card-body p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-1" 
                                     style="width: 44px; height: 44px; background-color: {{ $schedule->event_type === 'call_for_paper' ? 'rgba(229, 169, 60, 0.15)' : ($schedule->event_type === 'in_house_review' ? 'rgba(140, 29, 44, 0.12)' : 'rgba(16, 185, 129, 0.12)') }}; color: {{ $schedule->event_type === 'call_for_paper' ? '#b45309' : ($schedule->event_type === 'in_house_review' ? '#8C1D2C' : '#059669') }};">
                                    <i class="bi {{ $schedule->eventTypeIcon() }} fs-5"></i>
                                </div>
                                <div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <span class="badge rounded-pill {{ $schedule->event_type === 'call_for_paper' ? 'bg-warning text-dark' : ($schedule->event_type === 'in_house_review' ? 'bg-danger text-white' : 'bg-success text-white') }} px-2.5 py-1 fw-bold" style="font-size: 0.72rem;">
                                            {{ $schedule->eventTypeLabel() }}
                                        </span>
                                        @if($schedule->academic_year)
                                            <span class="badge rounded-pill bg-light text-muted border px-2.5 py-1" style="font-size: 0.72rem;">
                                                AY {{ $schedule->academic_year }}
                                            </span>
                                        @endif
                                        @if($schedule->isOngoing())
                                            <span class="badge rounded-pill bg-primary text-white px-2.5 py-1 animate-pulse" style="font-size: 0.72rem;">
                                                ● In Progress
                                            </span>
                                        @elseif($schedule->isUpcoming())
                                            <span class="badge rounded-pill bg-info text-dark px-2.5 py-1" style="font-size: 0.72rem;">
                                                Upcoming (in {{ now()->diffForHumans($schedule->start_date, true) }})
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-secondary text-white px-2.5 py-1" style="font-size: 0.72rem;">
                                                Concluded
                                            </span>
                                        @endif
                                    </div>
                                    <h3 class="h5 fw-bold text-dark mb-1">
                                        <a href="{{ route('schedules.show', $schedule->id) }}" class="text-dark text-decoration-none hover-primary">
                                            {{ $schedule->title }}
                                        </a>
                                    </h3>
                                    <div class="text-muted small d-flex flex-wrap align-items-center gap-2" style="font-size: 0.78rem;">
                                        <span><i class="bi bi-person-circle"></i> Set by Research Director ({{ $schedule->creator->name ?? 'Admin' }})</span>
                                        <span>&bull;</span>
                                        <span><i class="bi bi-clock-history"></i> Posted {{ $schedule->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Calendar Export & Share Actions -->
                            <div class="d-flex align-items-center gap-1.5">
                                <a href="{{ $schedule->googleCalendarUrl() }}" target="_blank" rel="noopener noreferrer" 
                                   class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 fw-semibold d-inline-flex align-items-center gap-1" 
                                   title="Add directly to Google Calendar">
                                    <i class="bi bi-google"></i> <span class="d-none d-md-inline">Add to Google Cal</span>
                                </a>
                                <a href="{{ route('schedules.ics', $schedule->id) }}" 
                                   class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-muted fw-semibold d-inline-flex align-items-center gap-1"
                                   title="Download .ICS file for Outlook / Apple Calendar">
                                    <i class="bi bi-download"></i> <span class="d-none d-md-inline">.ICS</span>
                                </a>
                            </div>
                        </div>

                        <!-- Schedule Spec Grid -->
                        <div class="row g-2 p-3 rounded-4 bg-light bg-opacity-75 mb-3 border" style="font-size: 0.85rem;">
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="text-muted small"><i class="bi bi-calendar3 me-1 text-primary"></i> Date & Time:</div>
                                <div class="fw-bold text-dark mt-0.5">
                                    {{ $schedule->start_date->format('M d, Y (D)') }}
                                    <span class="text-muted fw-normal">at {{ $schedule->start_date->format('g:i A') }}</span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="text-muted small"><i class="bi bi-geo-alt-fill me-1 text-danger"></i> Venue:</div>
                                <div class="fw-bold text-dark mt-0.5 text-truncate" title="{{ $schedule->venue ?: 'Campus/Virtual' }}">
                                    {{ $schedule->venue ?: 'TBA / Campus Facility' }}
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="text-muted small"><i class="bi bi-hourglass-split me-1 text-warning"></i> Submission Deadline:</div>
                                <div class="fw-bold {{ $schedule->submission_deadline ? 'text-danger' : 'text-muted' }} mt-0.5">
                                    {{ $schedule->submission_deadline ? $schedule->submission_deadline->format('M d, Y g:i A') : 'No cut-off specified' }}
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="text-muted small"><i class="bi bi-people-fill me-1 text-success"></i> Confirmed Attendance:</div>
                                <div class="fw-bold text-dark mt-0.5">
                                    <span class="badge bg-success bg-opacity-15 text-success rounded-pill px-2">
                                        {{ $schedule->confirmedAttendees->count() }} Attending
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Description Snippet -->
                        @if($schedule->description)
                            <p class="text-secondary mb-3 small" style="line-height: 1.6;">
                                {{ Str::limit(strip_tags($schedule->description), 200) }}
                            </p>
                        @endif

                        <!-- Action Bar -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top">
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('schedules.show', $schedule->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs">
                                    <span>View Details & Guidelines</span> <i class="bi bi-arrow-right"></i>
                                </a>

                                @if($schedule->meeting_link)
                                    <a href="{{ $schedule->meeting_link }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-camera-video-fill"></i> Join Virtual Session
                                    </a>
                                @endif

                                @if($schedule->attachment_path)
                                    <a href="{{ route('schedules.downloadAttachment', $schedule->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-muted fw-semibold d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-paperclip"></i> Memo Attachment
                                    </a>
                                @endif
                            </div>

                            @if($isDirector)
                                <div class="d-flex align-items-center gap-1.5">
                                    <!-- Send Immediate Reminder Button -->
                                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1 fw-bold text-dark d-inline-flex align-items-center gap-1"
                                            data-bs-toggle="modal" data-bs-target="#reminderModal-{{ $schedule->id }}" title="Send an immediate alert reminder via bell and email">
                                        <i class="bi bi-bell-fill text-warning"></i> <span>Send Reminder</span>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" class="m-0 d-inline" onsubmit="return confirm('Are you sure you want to delete this schedule? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger border rounded-circle p-1.5" title="Delete Schedule">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Immediate Reminder Modal for this Schedule -->
                @if($isDirector)
                    <div class="modal fade" id="reminderModal-{{ $schedule->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <form action="{{ route('schedules.reminder', $schedule->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                @csrf
                                <div class="modal-header border-0 pb-0 pt-4 px-4">
                                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-bell-fill text-warning"></i> Send Immediate Reminder
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body px-4 py-3">
                                    <p class="text-muted small mb-3">
                                        This will immediately dispatch a priority in-app notification and email alert to all targeted participants regarding:
                                        <br><strong>{{ $schedule->title }}</strong>
                                    </p>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small text-dark">Director's Urgent Note (Optional):</label>
                                        <textarea name="custom_note" rows="3" class="form-control rounded-3 text-sm" placeholder="e.g. Please bring 3 printed copies of your proposal and arrive 15 minutes before the call time."></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">
                                        <i class="bi bi-send-fill me-1"></i> Send Alert Now
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                    <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-calendar-x fs-3 text-muted"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">No Schedules Found</h5>
                    <p class="text-muted small mb-3">There are no research schedules matching your selected filters at this time.</p>
                    @if($isDirector)
                        <div>
                            <button class="btn btn-primary rounded-pill px-4 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                                <i class="bi bi-plus-lg me-1"></i> Create First Schedule
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endforelse

        <div class="col-12 mt-3">
            {{ $schedules->links() }}
        </div>
    </div>

    <!-- Create Schedule Modal (Director / Admin Only) -->
    @if($isDirector)
        <div class="modal fade" id="createScheduleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="max-height: calc(100vh - 2rem);">
                <form action="{{ route('schedules.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="max-height: calc(100vh - 2rem); display: flex; flex-direction: column;">
                    @csrf
                    <div class="modal-header border-0 pb-3 pt-4 px-4 bg-white border-bottom flex-shrink-0">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-calendar-plus-fill" style="color: var(--ua-maroon, #8C1D2C);"></i>
                            Set Research Schedule & Announcement
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-3" style="overflow-y: auto; flex: 1 1 auto; max-height: calc(85vh - 140px);">
                        <div class="row g-3">
                                <!-- Title -->
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-dark">Schedule Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control rounded-3" placeholder="e.g. AY 2026-2027 University In-House Review of Research Proposals" required>
                                </div>

                                <!-- Event Type -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Schedule Type <span class="text-danger">*</span></label>
                                    <select name="event_type" class="form-select rounded-3" required>
                                        <option value="call_for_paper">📢 Call for Papers / Research Proposals</option>
                                        <option value="in_house_review">🎓 In-House Review Schedule</option>
                                        <option value="inception_meeting">🚀 Inception Meeting Schedule</option>
                                        <option value="other">📅 Other Institutional Research Activity</option>
                                    </select>
                                </div>

                                <!-- Academic Year -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Academic Year</label>
                                    <input type="text" name="academic_year" class="form-control rounded-3" placeholder="e.g. 2026-2027" value="{{ date('Y') . '-' . (date('Y') + 1) }}">
                                </div>

                                <!-- Start Date & Time -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Event Start Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="start_date" class="form-control rounded-3" required>
                                </div>

                                <!-- End Date & Time -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Event End Date & Time</label>
                                    <input type="datetime-local" name="end_date" class="form-control rounded-3">
                                </div>

                                <!-- Submission Deadline -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Submission Deadline / Cut-Off (if applicable)</label>
                                    <input type="datetime-local" name="submission_deadline" class="form-control rounded-3">
                                    <div class="form-text" style="font-size: 0.75rem;">Automatic reminders will alert researchers ahead of this cut-off.</div>
                                </div>

                                <!-- Venue -->
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Physical Venue / Room</label>
                                    <input type="text" name="venue" class="form-control rounded-3" placeholder="e.g. URLC Audio-Visual Hall, Main Campus">
                                </div>

                                <!-- Virtual Meeting Link -->
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-dark">Virtual Meeting Link (Zoom / Google Meet)</label>
                                    <input type="url" name="meeting_link" class="form-control rounded-3" placeholder="https://meet.google.com/xxx-xxxx-xxx">
                                </div>

                                <!-- Target Audience -->
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-dark d-block">Target Audience & Notification Recipients</label>
                                    <div class="d-flex flex-wrap gap-3 p-2.5 rounded-3 bg-light border">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[]" value="all" id="aud_all" checked>
                                            <label class="form-check-label small fw-semibold" for="aud_all">All Faculty & Staff</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[]" value="researcher" id="aud_researcher">
                                            <label class="form-check-label small" for="aud_researcher">Faculty Researchers</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[]" value="reviewer" id="aud_reviewer">
                                            <label class="form-check-label small" for="aud_reviewer">Reviewers / Evaluators</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[]" value="coordinator" id="aud_coordinator">
                                            <label class="form-check-label small" for="aud_coordinator">College Coordinators</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[]" value="dean" id="aud_dean">
                                            <label class="form-check-label small" for="aud_dean">College Deans</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[]" value="budget_officer" id="aud_budget">
                                            <label class="form-check-label small" for="aud_budget">Budget & Finance Officers</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Attachment Memo -->
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-dark">Upload Guidelines Memo or Call Document (PDF / DOCX, max 10MB)</label>
                                    <input type="file" name="attachment" class="form-control rounded-3" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg">
                                </div>

                                <!-- Description / Guidelines -->
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-dark">Guidelines, Objectives & Special Instructions</label>
                                    <textarea name="description" rows="4" class="form-control rounded-3 text-sm" placeholder="Provide complete presentation mechanics, formatting requirements, panel instructions, or contact details..."></textarea>
                                </div>

                                <!-- Cross-post to Announcements -->
                                <div class="col-12">
                                    <div class="form-check form-switch p-2.5 bg-light rounded-3 border">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="cross_post_announcement" value="1" id="crossPostAnnounce" checked>
                                        <label class="form-check-label fw-bold small text-dark" for="crossPostAnnounce">
                                            Cross-post to the University Announcements Feed
                                        </label>
                                        <div class="text-muted ms-4" style="font-size: 0.74rem;">Creates an announcement entry so it appears on the Announcement page and live notifications.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-3 px-4 pb-4 bg-white border-top flex-shrink-0">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                <i class="bi bi-send-check-fill me-1"></i> Publish & Notify Users
                            </button>
                        </div>
                    </form>
            </div>
        </div>
    @endif
</x-app-layout>
