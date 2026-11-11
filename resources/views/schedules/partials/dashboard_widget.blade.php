@php
    $widgetSchedules = \App\Models\ResearchSchedule::where('status', 'published')
        ->where(function ($q) {
            $q->where('start_date', '>=', now()->startOfDay())
              ->orWhere(function ($sub) {
                  $sub->whereNotNull('submission_deadline')->where('submission_deadline', '>=', now()->startOfDay());
              });
        })
        ->orderBy('start_date', 'asc')
        ->take(3)
        ->get();
@endphp

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3 mb-md-4 bg-white">
    <div class="card-header bg-white py-3 px-3 px-md-4 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-primary bg-primary bg-opacity-10" style="width: 32px; height: 32px;">
                <i class="bi bi-calendar2-event-fill fs-6"></i>
            </div>
            <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.98rem;">Research Schedules & Deadlines</h5>
        </div>
        <a href="{{ route('schedules.index') }}" class="small fw-semibold text-primary text-decoration-none d-inline-flex align-items-center gap-1">
            <span>View Calendar</span> <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="card-body p-3 p-md-4 pt-1">
        @forelse($widgetSchedules as $sched)
            <div class="p-3 mb-2.5 rounded-3 bg-light border-start border-4 position-relative transition-all"
                 style="border-color: {{ $sched->event_type === 'call_for_paper' ? '#E5A93C' : ($sched->event_type === 'in_house_review' ? '#8C1D2C' : ($sched->event_type === 'inception_meeting' ? '#10B981' : '#3B82F6')) }} !important;">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-1.5">
                    <span class="badge rounded-pill {{ $sched->event_type === 'call_for_paper' ? 'bg-warning text-dark' : ($sched->event_type === 'in_house_review' ? 'bg-danger text-white' : 'bg-success text-white') }} px-2 py-0.5" style="font-size: 0.68rem;">
                        {{ $sched->eventTypeLabel() }}
                    </span>
                    <span class="text-muted small fw-medium" style="font-size: 0.72rem;">
                        <i class="bi bi-clock"></i> {{ $sched->start_date->diffForHumans() }}
                    </span>
                </div>

                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">
                    <a href="{{ route('schedules.show', $sched->id) }}" class="text-dark text-decoration-none hover-primary">
                        {{ $sched->title }}
                    </a>
                </h6>

                <div class="d-flex flex-wrap align-items-center gap-3 text-muted small mb-2" style="font-size: 0.75rem;">
                    <span><i class="bi bi-calendar-event me-1 text-primary"></i>{{ $sched->start_date->format('M d, Y (g:i A)') }}</span>
                    @if($sched->venue)
                        <span><i class="bi bi-geo-alt me-1 text-danger"></i>{{ Str::limit($sched->venue, 30) }}</span>
                    @endif
                    @if($sched->submission_deadline)
                        <span class="text-danger fw-bold"><i class="bi bi-hourglass-split me-1"></i>Due: {{ $sched->submission_deadline->format('M d') }}</span>
                    @endif
                </div>

                <div class="d-flex align-items-center justify-content-between pt-2 border-top gap-2" style="font-size: 0.75rem;">
                    <a href="{{ route('schedules.show', $sched->id) }}" class="fw-semibold text-primary text-decoration-none">
                        View Details & Guidelines &rarr;
                    </a>
                    <a href="{{ $sched->googleCalendarUrl() }}" target="_blank" rel="noopener noreferrer" class="text-muted text-decoration-none d-inline-flex align-items-center gap-1" title="Add to Google Calendar">
                        <i class="bi bi-google"></i> <span class="d-none d-sm-inline">Google Cal</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-4 px-2 text-muted">
                <i class="bi bi-calendar2-check fs-3 d-block mb-1.5 opacity-50"></i>
                <p class="mb-1 fw-semibold small">No upcoming schedules</p>
                <p class="text-muted small mb-2" style="font-size: 0.75rem;">The Research Director will announce schedules here.</p>
                @if(Auth::check() && (Auth::user()->isDirector() || Auth::user()->isAdmin()))
                    <a href="{{ route('schedules.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
                        <i class="bi bi-plus-lg me-1"></i> Set New Schedule
                    </a>
                @endif
            </div>
        @endforelse
    </div>
</div>
