<h1>Submit Research Proposal</h1>

<form method="POST" action="{{ route('proposal.store') }}" enctype="multipart/form-data">
    @csrf

    <label>Title:</label><br>
    <input type="text" name="title"><br><br>

    <label>Abstract:</label><br>
    <textarea name="abstract"></textarea><br><br>

    <label>Research Field:</label><br>
    <input type="text" name="research_field"><br><br>

    <label>Budget Requested:</label><br>
    <input type="number" name="budget_requested"><br><br>

    <label>Upload Document:</label><br>
    <input type="file" name="document"><br><br>

    <button type="submit">Submit</button>
</form>