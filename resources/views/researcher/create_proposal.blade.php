<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Submit Research Proposal</h2>
    </x-slot>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('proposal.store') }}" enctype="multipart/form-data" class="card p-4 shadow-sm">
        @csrf

        <div class="mb-3">
            <label class="form-label">Title:</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Abstract:</label>
            <textarea name="abstract" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Research Field:</label>
            <input type="text" name="research_field" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Budget Requested:</label>
            <input type="number" name="budget_requested" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Document (PDF only):</label>
            <input type="file" name="document" class="form-control" accept="application/pdf" required>
            <small class="text-muted">Only PDF files are allowed.</small>
        </div>

        <button type="submit" class="btn btn-primary">
            Submit Proposal
        </button>
    </form>
</x-app-layout>