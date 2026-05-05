<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Admin Dashboard</h2>
    </x-slot>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Manage Proposals</h5>
                    <p class="card-text">View all research proposals and make final approval decisions.</p>
                    <a href="{{ route('admin.proposals') }}" class="btn btn-primary">View Proposals</a>
                </div>
            </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Manage Users</h5>
                    <p class="card-text">Assign roles to researchers and reviewers.</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-dark">View Users</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>