<h1>My Research Proposals</h1>

<a href="{{ route('proposal.create') }}">Submit New Proposal</a>

<br><br>

<table border="1" cellpadding="10">
    <tr>
        <th>Title</th>
        <th>Field</th>
        <th>Budget</th>
        <th>Status</th>
        <th>Reviewer Comments</th>
        <th>Reviewer Suggestions</th>
        <th>Date Submitted</th>
    </tr>

    @foreach($proposals as $proposal)
    <tr>
        <td>{{ $proposal->title }}</td>
        <td>{{ $proposal->research_field }}</td>
        <td>{{ $proposal->budget_requested }}</td>
        <td>{{ $proposal->status }}</td>
        <td>{{ $proposal->review_comments ?? 'No comments yet' }}</td>
        <td>{{ $proposal->review_suggestions ?? 'No suggestions yet' }}</td>
        <td>{{ $proposal->created_at }}</td>
    </tr>
    @endforeach
</table>