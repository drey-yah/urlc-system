<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Edit / Resubmit Research Proposal</h2>
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

    <form method="POST" action="{{ route('proposal.update', $proposal->id) }}" enctype="multipart/form-data" class="card p-4 shadow-sm">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Title:</label>
            <input type="text" name="title" class="form-control" value="{{ $proposal->title }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Abstract:</label>
            <textarea name="abstract" class="form-control" rows="4" required>{{ $proposal->abstract }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Research Field:</label>
            <input type="text" name="research_field" class="form-control" value="{{ $proposal->research_field }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Budget Requested:</label>
            <input type="number" name="budget_requested" class="form-control" value="{{ $proposal->budget_requested }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Current Document:</label><br>

            @if($proposal->document_path)
                <a href="{{ asset('storage/' . $proposal->document_path) }}" target="_blank" class="btn btn-sm btn-primary">
                    View Current File
                </a>
            @else
                <span class="text-muted">No file uploaded</span>
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label">Upload Revised Document (PDF only):</label>
            <input type="file" name="document" class="form-control" accept="application/pdf">
            <small class="text-muted">Leave blank if you do not want to replace the current file.</small>
        </div>

        <button type="submit" class="btn btn-primary">
            Resubmit Proposal
        </button>
    </form>
</x-app-layout>