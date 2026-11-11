<x-app-layout>
    <!-- Top Back Navigation -->
    <div class="mb-3 d-flex align-items-center justify-content-between">
        <a href="{{ route('schedules.index') }}" class="btn btn-sm btn-light rounded-pill px-3 fw-semibold text-muted d-inline-flex align-items-center gap-1.5 shadow-xs">
            <i class="bi bi-arrow-left"></i> <span>Back to Schedules</span>
        </a>

        <!-- Export Buttons -->
        <div class="d-flex align-items-center gap-1.5">
            <a href="{{ $schedule->googleCalendarUrl() }}" target="_blank" rel="noopener noreferrer" 
               class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5 shadow-xs">
                <i class="bi bi-google"></i> <span>Add to Google Calendar</span>
            </a>
            <a href="{{ route('schedules.ics', $schedule->id) }}" 
               class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-muted fw-semibold d-inline-flex align-items-center gap-1.5 shadow-xs">
                <i class="bi bi-download"></i> <span>Download .ICS</span>
            </a>
        </div>
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

    <div class="row g-4">
        <!-- Main Content Column -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                <div class="p-4 p-md-5">
                    <!-- Category & Status Badges -->
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge rounded-pill {{ $schedule->event_type === 'call_for_paper' ? 'bg-warning text-dark' : ($schedule->event_type === 'in_house_review' ? 'bg-danger text-white' : 'bg-success text-white') }} px-3 py-1.5 fw-bold" style="font-size: 0.78rem;">
                            <i class="bi {{ $schedule->eventTypeIcon() }} me-1"></i> {{ $schedule->eventTypeLabel() }}
                        </span>
                        @if($schedule->academic_year)
                            <span class="badge rounded-pill bg-light text-muted border px-3 py-1.5" style="font-size: 0.78rem;">
                                Academic Year {{ $schedule->academic_year }}
                            </span>
                        @endif
                        @if($schedule->isOngoing())
                            <span class="badge rounded-pill bg-primary text-white px-3 py-1.5" style="font-size: 0.78rem;">
                                ● Active Now
                            </span>
                        @elseif($schedule->isUpcoming())
                            <span class="badge rounded-pill bg-info text-dark px-3 py-1.5" style="font-size: 0.78rem;">
                                In {{ now()->diffForHumans($schedule->start_date, true) }}
                            </span>
                        @else
                            <span class="badge rounded-pill bg-secondary text-white px-3 py-1.5" style="font-size: 0.78rem;">
                                Concluded
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="h3 fw-bold text-dark mb-2">{{ $schedule->title }}</h1>

                    <div class="text-muted small d-flex flex-wrap align-items-center gap-3 mb-4" style="font-size: 0.82rem;">
                        <span><i class="bi bi-person-circle"></i> Announced by Research Director ({{ $schedule->creator->name ?? 'Admin' }})</span>
                        <span>&bull;</span>
                        <span><i class="bi bi-calendar3"></i> Posted {{ $schedule->created_at->format('M d, Y') }}</span>
                    </div>

                    <!-- Contextual Banner CTA based on event type -->
                    @if($schedule->event_type === 'call_for_paper')
                        <div class="p-3.5 rounded-4 mb-4 border d-flex flex-wrap align-items-center justify-content-between gap-3" 
                             style="background: linear-gradient(135deg, rgba(229,169,60,0.12) 0%, rgba(255,255,255,0.9) 100%); border-color: rgba(229,169,60,0.3) !important;">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-plus-fill text-warning fs-5"></i> Ready to submit your research proposal?
                                </h6>
                                <p class="text-muted small mb-0">Prepare your Form RESU-FM-015 and submit before the cut-off date.</p>
                            </div>
                            <a href="{{ route('proposal.create') }}" class="btn btn-warning rounded-pill px-4 py-2 fw-bold text-dark shadow-sm">
                                <i class="bi bi-plus-circle-fill me-1"></i> Submit Proposal Now
                            </a>
                        </div>
                    @elseif($schedule->event_type === 'in_house_review')
                        <div class="p-3.5 rounded-4 mb-4 border d-flex flex-wrap align-items-center justify-content-between gap-3"
                             style="background: linear-gradient(135deg, rgba(140,29,44,0.08) 0%, rgba(255,255,255,0.9) 100%); border-color: rgba(140,29,44,0.2) !important;">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2" style="color: var(--ua-maroon, #8C1D2C) !important;">
                                    <i class="bi bi-journal-check fs-5"></i> In-House Review Presentation Session
                                </h6>
                                <p class="text-muted small mb-0">Check the official roster of shortlisted proposals and panel evaluation forms.</p>
                            </div>
                            <a href="{{ route('in_house_reviews.index') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" style="background-color: var(--ua-maroon, #8C1D2C); border-color: var(--ua-maroon, #8C1D2C);">
                                <i class="bi bi-folder-check me-1"></i> View In-House Dossier
                            </a>
                        </div>
                    @elseif($schedule->event_type === 'inception_meeting')
                        <div class="p-3.5 rounded-4 mb-4 border d-flex flex-wrap align-items-center justify-content-between gap-3"
                             style="background: linear-gradient(135deg, rgba(16,185,129,0.1) 0%, rgba(255,255,255,0.9) 100%); border-color: rgba(16,185,129,0.25) !important;">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2 text-success">
                                    <i class="bi bi-rocket-takeoff-fill fs-5"></i> Inception Meeting & Work Plan Alignment
                                </h6>
                                <p class="text-muted small mb-0">For approved researchers entering Phase 2 project mobilization and fund release.</p>
                            </div>
                            <a href="{{ route('inception_researches.index') }}" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm">
                                <i class="bi bi-file-earmark-spreadsheet me-1"></i> View Inception Dossier
                            </a>
                        </div>
                    @endif

                    <!-- Guidelines and Details -->
                    <div class="mb-4">
                        <h5 class="fw-bold text-dark mb-3">Guidelines & Activity Details</h5>
                        @if($schedule->description)
                            <div class="text-secondary" style="line-height: 1.7; white-space: pre-line; font-size: 0.95rem;">
                                {{ $schedule->description }}
                            </div>
                        @else
                            <p class="text-muted font-italic">No additional written guidelines provided. Refer to official university memos.</p>
                        @endif
                    </div>

                    <!-- Attachment Download Card -->
                    @if($schedule->attachment_path)
                        <div class="card border bg-light rounded-4 p-3 mb-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-white border d-flex align-items-center justify-content-center text-primary" style="width: 42px; height: 42px;">
                                        <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">{{ $schedule->attachment_name ?: 'Guidelines & Mechanics Document' }}</div>
                                        <div class="text-muted" style="font-size: 0.74rem;">Official attached memo from Research Directorate</div>
                                    </div>
                                </div>
                                <a href="{{ route('schedules.downloadAttachment', $schedule->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1 shadow-xs">
                                    <i class="bi bi-download"></i> Download Attachment
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- RSVP & Attendance Confirmation -->
                    <div class="border rounded-4 p-4 bg-light bg-opacity-50">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div>
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar-check text-primary"></i> Attendance & RSVP
                                </h6>
                                <p class="text-muted small mb-0">Confirm your attendance to help the Director organize seatings, food, and panel slots.</p>
                            </div>
                            @if($userRsvp)
                                <span class="badge rounded-pill {{ $userRsvp->status === 'confirmed' ? 'bg-success' : ($userRsvp->status === 'declined' ? 'bg-danger' : 'bg-warning text-dark') }} px-3 py-1.5 fw-bold">
                                    Your Status: {{ ucfirst($userRsvp->status) }}
                                </span>
                            @endif
                        </div>

                        <form action="{{ route('schedules.rsvp', $schedule->id) }}" method="POST">
                            @csrf
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-5">
                                    <select name="status" class="form-select rounded-pill text-sm" required>
                                        <option value="confirmed" {{ ($userRsvp && $userRsvp->status === 'confirmed') ? 'selected' : '' }}>✅ Yes, I will attend</option>
                                        <option value="declined" {{ ($userRsvp && $userRsvp->status === 'declined') ? 'selected' : '' }}>❌ No, I cannot attend</option>
                                        <option value="tentative" {{ ($userRsvp && $userRsvp->status === 'tentative') ? 'selected' : '' }}>⏳ Tentative / Pending Schedule</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-5">
                                    <input type="text" name="remarks" class="form-control rounded-pill text-sm" placeholder="Optional remark (e.g. Presenter for Group 3)" value="{{ $userRsvp ? $userRsvp->remarks : '' }}">
                                </div>
                                <div class="col-12 col-md-2">
                                    <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold shadow-xs">
                                        Save
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Director Attendance Roster (Director / Admin Only) -->
            @if($isDirector)
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-people-fill text-primary"></i> Attendance Roster ({{ $schedule->attendees->count() }})
                            </h5>
                            <p class="text-muted small mb-0">Responses from faculty researchers, reviewers, and coordinators.</p>
                        </div>
                        <span class="badge bg-success rounded-pill px-3 py-1.5 fw-bold">
                            {{ $schedule->confirmedAttendees->count() }} Confirmed
                        </span>
                    </div>
                    <div class="card-body p-0">
                        @if($schedule->attendees->isEmpty())
                            <div class="p-4 text-center text-muted small">
                                No attendees have submitted their RSVP yet.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">User</th>
                                            <th>Role</th>
                                            <th>Department / Campus</th>
                                            <th>Status</th>
                                            <th>Remarks</th>
                                            <th class="pe-4">Responded</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($schedule->attendees as $attendee)
                                            <tr>
                                                <td class="ps-4 fw-bold text-dark">
                                                    {{ $attendee->user->name }}
                                                    <div class="text-muted small fw-normal">{{ $attendee->user->email }}</div>
                                                </td>
                                                <td><span class="badge bg-light text-dark border">{{ ucfirst($attendee->user->role) }}</span></td>
                                                <td class="text-muted">{{ $attendee->user->department ?: ($attendee->user->campus ?: 'Main Campus') }}</td>
                                                <td>
                                                    <span class="badge rounded-pill {{ $attendee->status === 'confirmed' ? 'bg-success' : ($attendee->status === 'declined' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                        {{ ucfirst($attendee->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-muted font-italic">{{ $attendee->remarks ?: '—' }}</td>
                                                <td class="pe-4 text-muted small">{{ $attendee->updated_at->diffForHumans() }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Specs Column -->
        <div class="col-12 col-lg-4">
            <!-- Event Specs Card -->
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h6 class="fw-bold text-dark mb-3 text-uppercase small" style="letter-spacing: 0.5px;">Schedule Information</h6>

                <!-- Date & Time -->
                <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary flex-shrink-0" style="width: 38px; height: 38px;">
                        <i class="bi bi-calendar3 fs-6"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Start Schedule</div>
                        <div class="fw-bold text-dark">{{ $schedule->start_date->format('l, F j, Y') }}</div>
                        <div class="text-muted small">{{ $schedule->start_date->format('g:i A') }}</div>
                    </div>
                </div>

                @if($schedule->end_date)
                    <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-secondary flex-shrink-0" style="width: 38px; height: 38px;">
                            <i class="bi bi-clock-history fs-6"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-semibold">End Schedule</div>
                            <div class="fw-bold text-dark">{{ $schedule->end_date->format('l, F j, Y') }}</div>
                            <div class="text-muted small">{{ $schedule->end_date->format('g:i A') }}</div>
                        </div>
                    </div>
                @endif

                <!-- Submission Deadline -->
                @if($schedule->submission_deadline)
                    <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger flex-shrink-0" style="width: 38px; height: 38px;">
                            <i class="bi bi-hourglass-bottom fs-6"></i>
                        </div>
                        <div>
                            <div class="text-danger small fw-bold">Submission Cut-Off</div>
                            <div class="fw-bold text-dark">{{ $schedule->submission_deadline->format('M d, Y') }}</div>
                            <div class="text-muted small">{{ $schedule->submission_deadline->format('g:i A') }}</div>
                        </div>
                    </div>
                @endif

                <!-- Venue -->
                <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning flex-shrink-0" style="width: 38px; height: 38px;">
                        <i class="bi bi-geo-alt-fill fs-6"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Venue / Location</div>
                        <div class="fw-bold text-dark">{{ $schedule->venue ?: 'To be announced (TBA)' }}</div>
                    </div>
                </div>

                <!-- Virtual Meeting Link -->
                @if($schedule->meeting_link)
                    <div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success flex-shrink-0" style="width: 38px; height: 38px;">
                            <i class="bi bi-camera-video-fill fs-6"></i>
                        </div>
                        <div class="overflow-hidden">
                            <div class="text-muted small fw-semibold">Virtual Conference Link</div>
                            <a href="{{ $schedule->meeting_link }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold text-white mt-1 d-inline-flex align-items-center gap-1 shadow-xs">
                                <i class="bi bi-box-arrow-up-right"></i> Join Online Session
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Target Audience -->
                <div>
                    <div class="text-muted small fw-semibold mb-1.5">Intended Audience</div>
                    <div class="d-flex flex-wrap gap-1">
                        @if(empty($schedule->target_audience) || in_array('all', (array)$schedule->target_audience))
                            <span class="badge bg-light text-dark border rounded-pill">All University Researchers & Staff</span>
                        @else
                            @foreach((array)$schedule->target_audience as $aud)
                                <span class="badge bg-light text-dark border rounded-pill">{{ ucfirst(str_replace('_', ' ', $aud)) }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Director Controls (Director / Admin Only) -->
            @if($isDirector)
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                    <h6 class="fw-bold text-dark mb-3 text-uppercase small" style="letter-spacing: 0.5px;">Director Actions</h6>
                    
                    <div class="d-grid gap-2">
                        <!-- Send Immediate Reminder Button -->
                        <button type="button" class="btn btn-warning rounded-pill py-2 fw-bold text-dark shadow-xs" data-bs-toggle="modal" data-bs-target="#directorReminderModal">
                            <i class="bi bi-bell-fill me-1"></i> Send Immediate Reminder
                        </button>

                        <!-- Edit Button -->
                        <button type="button" class="btn btn-light border rounded-pill py-2 fw-semibold text-dark" data-bs-toggle="modal" data-bs-target="#editScheduleModal">
                            <i class="bi bi-pencil-square me-1"></i> Edit Schedule Details
                        </button>

                        <!-- Delete Button -->
                        <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Delete this schedule? All RSVP records will be removed.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger rounded-pill py-2 fw-semibold w-100">
                                <i class="bi bi-trash me-1"></i> Delete Schedule
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Immediate Reminder Modal -->
                <div class="modal fade" id="directorReminderModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="{{ route('schedules.reminder', $schedule->id) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            @csrf
                            <div class="modal-header border-0 pb-0 pt-4 px-4">
                                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-bell-fill text-warning"></i> Dispatch Priority Reminder
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-4 py-3">
                                <p class="text-muted small mb-3">
                                    Notify all targeted users right away through the in-app notification bell and email alert.
                                </p>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-dark">Director's Urgent Note:</label>
                                    <textarea name="custom_note" rows="3" class="form-control rounded-3 text-sm" placeholder="e.g. Please be reminded that registration starts strictly at 8:00 AM."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">
                                    <i class="bi bi-send-fill me-1"></i> Send Reminder Now
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Edit Schedule Modal -->
                <div class="modal fade" id="editScheduleModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="max-height: calc(100vh - 2rem);">
                        <form action="{{ route('schedules.update', $schedule->id) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="max-height: calc(100vh - 2rem); display: flex; flex-direction: column;">
                            @csrf
                            @method('PUT')
                            <div class="modal-header border-0 pb-3 pt-4 px-4 bg-white border-bottom flex-shrink-0">
                                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-pencil-square" style="color: var(--ua-maroon, #8C1D2C);"></i>
                                    Edit Research Schedule
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-4 py-3" style="overflow-y: auto; flex: 1 1 auto; max-height: calc(85vh - 140px);">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-dark">Schedule Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control rounded-3" value="{{ $schedule->title }}" required>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold small text-dark">Schedule Type <span class="text-danger">*</span></label>
                                        <select name="event_type" class="form-select rounded-3" required>
                                            <option value="call_for_paper" {{ $schedule->event_type === 'call_for_paper' ? 'selected' : '' }}>📢 Call for Papers / Research Proposals</option>
                                            <option value="in_house_review" {{ $schedule->event_type === 'in_house_review' ? 'selected' : '' }}>🎓 In-House Review Schedule</option>
                                            <option value="inception_meeting" {{ $schedule->event_type === 'inception_meeting' ? 'selected' : '' }}>🚀 Inception Meeting Schedule</option>
                                            <option value="other" {{ $schedule->event_type === 'other' ? 'selected' : '' }}>📅 Other Institutional Research Activity</option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold small text-dark">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select rounded-3" required>
                                            <option value="published" {{ $schedule->status === 'published' ? 'selected' : '' }}>Published (Active)</option>
                                            <option value="completed" {{ $schedule->status === 'completed' ? 'selected' : '' }}>Completed / Concluded</option>
                                            <option value="cancelled" {{ $schedule->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold small text-dark">Academic Year</label>
                                        <input type="text" name="academic_year" class="form-control rounded-3" value="{{ $schedule->academic_year }}">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold small text-dark">Venue / Room</label>
                                        <input type="text" name="venue" class="form-control rounded-3" value="{{ $schedule->venue }}">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold small text-dark">Start Date & Time <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="start_date" class="form-control rounded-3" value="{{ $schedule->start_date ? $schedule->start_date->format('Y-m-d\TH:i') : '' }}" required>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold small text-dark">End Date & Time</label>
                                        <input type="datetime-local" name="end_date" class="form-control rounded-3" value="{{ $schedule->end_date ? $schedule->end_date->format('Y-m-d\TH:i') : '' }}">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold small text-dark">Submission Deadline</label>
                                        <input type="datetime-local" name="submission_deadline" class="form-control rounded-3" value="{{ $schedule->submission_deadline ? $schedule->submission_deadline->format('Y-m-d\TH:i') : '' }}">
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label class="form-label fw-bold small text-dark">Virtual Meeting Link</label>
                                        <input type="url" name="meeting_link" class="form-control rounded-3" value="{{ $schedule->meeting_link }}">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-dark">Replace Memo Attachment (Optional)</label>
                                        <input type="file" name="attachment" class="form-control rounded-3" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg">
                                        @if($schedule->attachment_name)
                                            <div class="text-muted small mt-1">Current file: {{ $schedule->attachment_name }}</div>
                                        @endif
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-bold small text-dark">Guidelines & Mechanics</label>
                                        <textarea name="description" rows="4" class="form-control rounded-3 text-sm">{{ $schedule->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-3 px-4 pb-4 bg-white border-top flex-shrink-0">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
