<h1>All Research Proposals</h1>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<table border="1" cellpadding="10">
    <tr>
        <th>Title</th>
        <th>Researcher ID</th>
        <th>Field</th>
        <th>Budget</th>
        <th>Status</th>
        <th>Document</th>
        <th>Review Comments</th>
        <th>Review Suggestions</th>
        <th>Action</th>
    </tr>

    @foreach($proposals as $proposal)
    <tr>
        <td>{{ $proposal->title }}</td>
        <td>{{ $proposal->user_id }}</td>
        <td>{{ $proposal->research_field }}</td>
        <td>{{ $proposal->budget_requested }}</td>
        <td>{{ $proposal->status }}</td>

        <!-- Document -->
        <td>
            @if($proposal->document_path)
                <a href="{{ asset('storage/' . $proposal->document_path) }}" target="_blank">
                    View File
                </a>
            @else
                No file
            @endif
        </td>

        <!-- Comments -->
        <td>{{ $proposal->review_comments }}</td>

        <!-- Suggestions -->
        <td>{{ $proposal->review_suggestions }}</td>

        <!-- Action -->
        <td>
            <form method="POST" action="{{ route('reviewer.proposals.updateStatus', $proposal->id) }}">
                @csrf

                <select name="status">
                    <option value="pending" {{ $proposal->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $proposal->status == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $proposal->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="revision_required" {{ $proposal->status == 'revision_required' ? 'selected' : '' }}>Revision Required</option>
                </select>

                <br><br>

                <textarea name="review_comments" placeholder="Write review comments">{{ $proposal->review_comments }}</textarea>

                <br><br>

                <textarea name="review_suggestions" placeholder="Write suggestions">{{ $proposal->review_suggestions }}</textarea>

                <br><br>

                <button type="submit">Update</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>