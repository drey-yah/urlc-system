<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Researcher Dashboard</h2>
    </x-slot>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Submit New Proposal</h5>
                    <p class="card-text">Create and submit a new research proposal for review.</p>
                    <a href="{{ route('proposal.create') }}" class="btn btn-primary">Create Proposal</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">My Proposals</h5>
                    <p class="card-text">View and track the status of all your submitted proposals.</p>
                    <a href="{{ route('proposal.index') }}" class="btn btn-outline-primary">View Proposals</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>