<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">My Research Proposals</h2>
    </x-slot>

    <a href="{{ route('proposal.create') }}" class="btn btn-primary mb-3">
        Submit New Proposal
    </a>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Field</th>
                    <th>Budget</th>
                    <th>Status</th>
                    <th>Reviewer Comments</th>
                    <th>Reviewer Suggestions</th>
                    <th>Date Submitted</th>
                    <th>Action</th> <!-- ✅ NEW -->
                </tr>
            </thead>

            <tbody>
            @foreach($proposals as $proposal)
                <tr>
                    <td>{{ $proposal->title }}</td>
                    <td>{{ $proposal->research_field }}</td>
                    <td>{{ $proposal->budget_requested }}</td>

                    <td>
                        @if($proposal->status == 'pending')
                            <span class="badge bg-secondary">Pending</span>
                        @elseif($proposal->status == 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($proposal->status == 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @elseif($proposal->status == 'revision_required')
                            <span class="badge bg-warning text-dark">Revision Required</span>
                        @elseif($proposal->status == 'final_approved')
                            <span class="badge bg-primary">Final Approved</span>
                        @elseif($proposal->status == 'final_rejected')
                            <span class="badge bg-dark">Final Rejected</span>
                        @else
                            <span class="badge bg-secondary">{{ $proposal->status }}</span>
                        @endif
                    </td>

                    <td>{{ $proposal->review_comments ?? 'No comments yet' }}</td>
                    <td>{{ $proposal->review_suggestions ?? 'No suggestions yet' }}</td>
                    <td>{{ $proposal->created_at }}</td>

                    <!-- 🔥 NEW ACTION COLUMN -->
                    <td>
                        @if($proposal->status == 'revision_required')
                            <a href="{{ route('proposal.edit', $proposal->id) }}" class="btn btn-warning btn-sm">
                                Edit / Resubmit
                            </a>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>