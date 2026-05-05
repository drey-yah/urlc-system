<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Call for Papers & Announcements</h2>
    </x-slot>

    <div class="row">
        <div class="col-md-8 mx-auto">
            @if(auth()->user()->role == 'admin')
                <!-- Post New Announcement -->
                <div class="card shadow-sm mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h3 class="h6 mb-0">Publish New Announcement</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('announcements.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Title</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Call for Papers 2026" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Content</label>
                                <textarea name="content" class="form-control" rows="3" placeholder="Write the announcement details here..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Image (Optional)</label>
                                <input type="file" name="image" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Publish Now</button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Announcements Feed -->
            @forelse($announcements as $announcement)
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-secondary rounded-circle text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($announcement->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="h6 mb-0 fw-bold">{{ $announcement->user->name }}</h4>
                                    <small class="text-muted">{{ $announcement->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            @if(auth()->user()->role == 'admin')
                                <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Delete this announcement?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0">Delete</button>
                                </form>
                            @endif
                        </div>

                        <h3 class="h5 fw-bold mb-2">{{ $announcement->title }}</h3>
                        <p class="mb-3" style="white-space: pre-line;">{{ $announcement->content }}</p>

                        @if($announcement->image_path)
                            <img src="{{ asset('storage/' . $announcement->image_path) }}" class="img-fluid rounded mb-3 w-100" style="max-height: 800px; object-fit: contain; background-color: #f8f9fa;">
                        @endif

                        <hr>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <form action="{{ route('announcements.like', $announcement->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $announcement->likes->where('user_id', auth()->id())->count() > 0 ? 'btn-primary' : 'btn-outline-primary' }}">
                                    👍 Like ({{ $announcement->likes->count() }})
                                </button>
                            </form>
                            <span class="text-muted">💬 {{ $announcement->comments->count() }} Comments</span>
                        </div>

                        <!-- Comments Section -->
                        <div class="bg-light p-3 rounded">
                            @foreach($announcement->comments as $comment)
                                <div class="mb-3">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px; font-size: 12px;">
                                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                        </div>
                                        <div class="bg-white p-2 rounded shadow-sm flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <small class="fw-bold">{{ $comment->user->name }}</small>
                                                <small class="text-muted" style="font-size: 10px;">{{ $comment->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-1 small">{{ $comment->body }}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Replies -->
                                    @foreach($comment->replies as $reply)
                                        <div class="ms-5 mt-2 d-flex align-items-start gap-2">
                                            <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 25px; height: 25px; font-size: 10px;">
                                                {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                            </div>
                                            <div class="bg-white p-2 rounded shadow-sm flex-grow-1 border-start border-3">
                                                <div class="d-flex justify-content-between">
                                                    <small class="fw-bold">{{ $reply->user->name }}</small>
                                                    <small class="text-muted" style="font-size: 10px;">{{ $reply->created_at->diffForHumans() }}</small>
                                                </div>
                                                <p class="mb-0 small">{{ $reply->body }}</p>
                                            </div>
                                        </div>
                                    @endforeach

                                    <!-- Reply Form -->
                                    <form action="{{ route('announcements.comment', $announcement->id) }}" method="POST" class="ms-5 mt-2 d-flex gap-2">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        <input type="text" name="body" class="form-control form-control-sm" placeholder="Write a reply..." required>
                                        <button type="submit" class="btn btn-sm btn-outline-dark">Reply</button>
                                    </form>
                                </div>
                            @endforeach

                            <!-- New Comment Form -->
                            <form action="{{ route('announcements.comment', $announcement->id) }}" method="POST" class="mt-3">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="body" class="form-control" placeholder="Write a comment..." required>
                                    <button type="submit" class="btn btn-dark">Comment</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <p class="text-muted">No announcements yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
