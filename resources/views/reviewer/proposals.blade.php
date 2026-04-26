<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Reviewer - Research Proposals</h2>
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
                    <th>Budget</th>
                    <th>Status</th>
                    <th>Document</th>
                    <th>Comments</th>
                    <th>Suggestions</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            @foreach($proposals as $proposal)
            <tr>
                <td>{{ $proposal->title }}</td>
                <td>{{ $proposal->user->name }}</td>
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
                    @endif
                </td>

                <td>
                    @if($proposal->document_path)
                        <a href="{{ asset('storage/' . $proposal->document_path) }}" target="_blank" class="btn btn-sm btn-primary">View</a>
                        <a href="{{ asset('storage/' . $proposal->document_path) }}" download class="btn btn-sm btn-success">Download</a>
                    @else
                        <span class="text-muted">No file</span>
                    @endif
                </td>

                <td>{{ $proposal->review_comments }}</td>
                <td>{{ $proposal->review_suggestions }}</td>

                <td>
                    <form method="POST" action="{{ route('reviewer.proposals.updateStatus', $proposal->id) }}">
                        @csrf

                        <select name="status" class="form-select mb-2">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="revision_required">Revision Required</option>
                        </select>

                        <textarea name="review_comments" class="form-control mb-2" placeholder="Comments">{{ $proposal->review_comments }}</textarea>

                        <textarea name="review_suggestions" class="form-control mb-2" placeholder="Suggestions">{{ $proposal->review_suggestions }}</textarea>

                        <button type="submit" class="btn btn-primary btn-sm w-100">Update</button>
                    </form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>