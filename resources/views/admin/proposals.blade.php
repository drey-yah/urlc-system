<h1>Admin - All Research Proposals</h1>

<table border="1" cellpadding="10">
    <tr>
        <th>Title</th>
        <th>Researcher ID</th>
        <th>Field</th>
        <th>Budget Requested</th>
        <th>Budget Spent</th>
        <th>Status</th>
        <th>Document</th>
        <th>Reviewer Comments</th>
        <th>Reviewer Suggestions</th>
        <th>Date Submitted</th>
    </tr>

    @foreach($proposals as $proposal)
    <tr>
        <td>{{ $proposal->title }}</td>
        <td>{{ $proposal->user_id }}</td>
        <td>{{ $proposal->research_field }}</td>
        <td>{{ $proposal->budget_requested }}</td>
        <td>{{ $proposal->budget_spent }}</td>
        <td>{{ $proposal->status }}</td>
        <td>
            @if($proposal->document_path)
                <a href="{{ asset('storage/' . $proposal->document_path) }}" target="_blank">
                    View File
                </a>
            @else
                No file
            @endif
        </td>
        <td>{{ $proposal->review_comments ?? 'No comments yet' }}</td>
        <td>{{ $proposal->review_suggestions ?? 'No suggestions yet' }}</td>
        <td>{{ $proposal->created_at }}</td>
    </tr>
    @endforeach
</table>