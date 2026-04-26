<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Admin - All Research Proposals</h2>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Title</th>
                    <th>Researcher</th>
                    <th>Field</th>
                    <th>Budget Requested</th>
                    <th>Budget Spent</th>
                    <th>Status</th>
                    <th>Document</th>
                    <th>Reviewer Comments</th>
                    <th>Reviewer Suggestions</th>
                    <th>Date Submitted</th>
                    <th>Final Decision</th>
                </tr>
            </thead>

            <tbody>
            @foreach($proposals as $proposal)
                <tr>
                    <td>{{ $proposal->title }}</td>
                    <td>{{ $proposal->user->name }}</td>
                    <td>{{ $proposal->research_field }}</td>
                    <td>{{ $proposal->budget_requested }}</td>
                    <td>{{ $proposal->budget_spent }}</td>

                    <td>
                        @php
                            $status = $proposal->status;

                            $badgeClass = match($status) {
                                'pending' => 'bg-secondary',
                                'approved' => 'bg-success',
                                'rejected' => 'bg-danger',
                                'revision_required' => 'bg-warning text-dark',
                                'final_approved' => 'bg-primary',
                                'final_rejected' => 'bg-dark',
                                default => 'bg-secondary',
                            };
                        @endphp

                        <span class="badge {{ $badgeClass }}">
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>
                    </td>

                    <td>
                        @if($proposal->document_path)
                            <a href="{{ asset('storage/' . $proposal->document_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                View
                            </a>
                            <a href="{{ asset('storage/' . $proposal->document_path) }}" download class="btn btn-sm btn-success">
                                Download
                            </a>
                        @else
                            <span class="text-muted">No file</span>
                        @endif
                    </td>

                    <td>{{ $proposal->review_comments ?? 'No comments yet' }}</td>
                    <td>{{ $proposal->review_suggestions ?? 'No suggestions yet' }}</td>
                    <td>{{ $proposal->created_at }}</td>

                    <td>
                        <form method="POST" action="{{ route('admin.proposals.finalDecision', $proposal->id) }}">
                            @csrf

                            <select name="status" class="form-select mb-2">
                                <option value="final_approved">Final Approved</option>
                                <option value="final_rejected">Final Rejected</option>
                            </select>

                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                Save Decision
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>