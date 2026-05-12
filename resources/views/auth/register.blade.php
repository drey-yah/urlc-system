<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="d-block" style="height:5rem;width:auto;color:#6c757d;" />
            </a>
        </x-slot>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input id="name" class="form-control" type="text" name="name" value="{{ old('name') }}" required autofocus />
            </div>

            <!-- Email Address -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required />
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required />
            </div>

            <!-- Role Selection -->
            <div class="mb-3">
                <label for="role" class="form-label">Register as</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="researcher">Researcher</option>
                    <option value="reviewer">Reviewer</option>
                    <option value="admin">Administrator (Requires Approval)</option>
                </select>
            </div>

            <div class="d-flex align-items-center justify-content-end">
                <a class="text-decoration-underline text-muted me-3" href="{{ route('login') }}">
                    Already registered?
                </a>

                <button type="submit" class="btn btn-dark">Register</button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
