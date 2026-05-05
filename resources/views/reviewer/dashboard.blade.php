<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Reviewer Dashboard</h2>
    </x-slot>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Review Proposals</h5>
                    <p class="card-text">View all submitted research proposals and provide your review.</p>
                    <a href="{{ route('reviewer.proposals') }}" class="btn btn-primary">View Proposals</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>