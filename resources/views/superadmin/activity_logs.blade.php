<x-app-layout>
    <div class="mb-5">
        <h1 class="h3 fw-bold mb-1">Activity Logs</h1>
        <p class="text-muted">Review historical changes and system activities.</p>
    </div>

    <!-- Logs Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0">Date & Time</th>
                        <th class="py-3 border-0">Action By</th>
                        <th class="py-3 border-0">Action</th>
                        <th class="py-3 border-0">Target Model</th>
                        <th class="pe-4 py-3 border-0">Changes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="ps-4 py-3 text-muted small">
                                <div>{{ $log->created_at->format('M d, Y') }}</div>
                                <div>{{ $log->created_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                @if($log->causer)
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                            {{ substr($log->causer->name, 0, 1) }}
                                        </div>
                                        <span class="fw-bold text-dark small">{{ $log->causer->name }}</span>
                                    </div>
                                @else
                                    <span class="text-muted small">System</span>
                                @endif
                            </td>
                            <td>
                                @if($log->description === 'created')
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill small">Created</span>
                                @elseif($log->description === 'updated')
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill small">Updated</span>
                                @elseif($log->description === 'deleted')
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill small">Deleted</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill small">{{ ucfirst($log->description) }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $modelName = class_basename($log->subject_type);
                                @endphp
                                <span class="badge bg-light text-dark border px-2 py-1 rounded small">
                                    {{ $modelName }} #{{ $log->subject_id }}
                                </span>
                            </td>
                            <td class="pe-4 text-start">
                                @if(isset($log->properties['old']) && isset($log->properties['attributes']))
                                    <div class="small">
                                        @foreach($log->properties['attributes'] as $key => $newValue)
                                            @php
                                                $oldValue = $log->properties['old'][$key] ?? 'N/A';
                                                if (is_bool($oldValue)) $oldValue = $oldValue ? 'true' : 'false';
                                                if (is_bool($newValue)) $newValue = $newValue ? 'true' : 'false';
                                            @endphp
                                            <div class="mb-1">
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> 
                                                <span class="text-danger text-decoration-line-through">{{ $oldValue }}</span> 
                                                <i class="bi bi-arrow-right mx-1 text-muted"></i> 
                                                <span class="text-success">{{ $newValue }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif(isset($log->properties['attributes']))
                                    <div class="small text-muted">
                                        {{ count($log->properties['attributes']) }} attribute(s) set.
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-3 text-black-50"></i>
                                No activity logs found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="card-footer bg-white py-3 border-0">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
