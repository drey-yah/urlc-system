<x-app-layout>
    <!-- Page Header -->
    <div class="mb-3 mb-md-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h1 class="h4 h-md-3 fw-bold text-dark d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-bell text-primary"></i> Announcements
            </h1>
            <p class="text-muted small mb-0">Stay updated with official university research updates and news.</p>
        </div>
        @if(auth()->user()->role === 'admin')
            <button class="btn btn-primary px-3 px-md-4 py-2 rounded-pill fw-bold d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                <i class="bi bi-plus-lg"></i> <span class="d-none d-sm-inline">Create Announcement</span><span class="d-sm-none">Create</span>
            </button>
        @endif
    </div>

    <div class="row g-3 g-md-4">
        <div class="col-12">
            @forelse($announcements as $announcement)
                <div class="card border-0 shadow-sm mb-3 mb-md-4 rounded-4 overflow-hidden">
                    <div class="card-body p-3 p-md-4 p-lg-5">
                        <!-- Top Row: Icon + Title + Author Metadata -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle flex-shrink-0" style="width: 40px; height: 40px;">
                                    <i class="bi bi-megaphone-fill fs-5 mb-0"></i>
                                </div>
                                <div class="min-w-0" style="min-width: 0;">
                                    <h4 class="h6 fs-md-5 fw-bold mb-1 text-dark">{{ $announcement->title }}</h4>
                                    <div class="text-muted small d-flex flex-wrap align-items-center gap-2" style="font-size: 0.78rem;">
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-person-circle"></i> {{ $announcement->user->name }}
                                        </span>
                                        <span class="text-muted opacity-50">&bull;</span>
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-calendar3"></i> {{ $announcement->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Content Preview -->
                        <p class="text-secondary mb-3 mb-md-4" style="line-height: 1.65; font-size: 0.92rem;">
                            {{ Str::limit($announcement->content, 260) }}
                        </p>
                        
                        <!-- Action Bar: Likes, Comments, Read More -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 border-top">
                            <div class="d-flex align-items-center gap-2">
                                <form action="{{ route('announcements.like', $announcement->id) }}" method="POST" class="m-0 like-form" data-announcement-id="{{ $announcement->id }}">
                                    @csrf
                                    @php
                                        $hasLiked = $announcement->likes->where('user_id', auth()->id())->isNotEmpty();
                                    @endphp
                                    <button type="submit" class="like-btn-{{ $announcement->id }} btn btn-sm btn-light rounded-pill px-2.5 py-1 px-sm-3 py-sm-1.5 fw-medium d-flex align-items-center gap-1 {{ $hasLiked ? 'text-primary' : 'text-muted' }}" style="font-size: 0.8rem; padding-left: 0.75rem; padding-right: 0.75rem;">
                                        <i class="bi {{ $hasLiked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' }}"></i> 
                                        <span class="like-count-{{ $announcement->id }} fw-bold">{{ $announcement->likes->count() }}</span>
                                        <span class="d-none d-sm-inline">Likes</span>
                                    </button>
                                </form>
                                <button class="btn btn-sm btn-light rounded-pill py-1 px-sm-3 py-sm-1.5 text-muted fw-medium d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#annModal{{ $announcement->id }}" style="font-size: 0.8rem; padding-left: 0.75rem; padding-right: 0.75rem;">
                                    <i class="bi bi-chat-left-text"></i> 
                                    <span class="fw-bold">{{ $announcement->comments->count() }}</span>
                                    <span class="d-none d-sm-inline">Comments</span>
                                </button>
                            </div>
                            <button class="btn btn-primary btn-sm px-3 py-1.5 rounded-pill fw-bold shadow-xs d-inline-flex align-items-center gap-1 text-nowrap" data-bs-toggle="modal" data-bs-target="#annModal{{ $announcement->id }}" style="font-size: 0.82rem;">
                                <span class="d-none d-sm-inline">Read Full Announcement</span>
                                <span class="d-sm-none">Read More</span>
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Announcement Modal -->
                <div class="modal fade" id="annModal{{ $announcement->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg rounded-4">
                            <div class="modal-header border-0 px-3 px-md-4 pt-3 pt-md-4 pb-0">
                                <div class="d-flex align-items-center gap-2.5">
                                    <i class="bi bi-megaphone-fill text-primary fs-4 mb-0 flex-shrink-0"></i>
                                    <h5 class="modal-title fw-bold text-dark mb-0">{{ $announcement->title }}</h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-3 p-md-4 pb-2">
                                <div class="d-flex flex-wrap align-items-center gap-2 gap-md-3 mb-3 small text-muted">
                                    <span><i class="bi bi-calendar3 me-1"></i> {{ $announcement->created_at->format('M d, Y') }}</span>
                                    <span>&bull;</span>
                                    <span><i class="bi bi-person me-1"></i> {{ $announcement->user->name }}</span>
                                </div>
                                <div class="text-dark bg-light p-3 rounded-3 border mb-3" style="line-height: 1.8; white-space: pre-wrap; font-size: 0.95rem;">{{ $announcement->content }}</div>
                                
                                <!-- Modal Interactions Bar -->
                                <div class="d-flex align-items-center gap-3 py-2.5 border-top border-bottom">
                                    <form action="{{ route('announcements.like', $announcement->id) }}" method="POST" class="m-0 like-form" data-announcement-id="{{ $announcement->id }}">
                                        @csrf
                                        @php
                                            $hasLiked = $announcement->likes->where('user_id', auth()->id())->isNotEmpty();
                                        @endphp
                                        <button type="submit" class="like-btn-{{ $announcement->id }} btn btn-sm btn-link text-decoration-none p-0 fw-bold d-flex align-items-center gap-1.5 {{ $hasLiked ? 'text-primary' : 'text-muted' }}">
                                            <i class="bi {{ $hasLiked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' }} fs-5"></i> 
                                            <span class="like-count-{{ $announcement->id }}">{{ $announcement->likes->count() }}</span> Likes
                                        </button>
                                    </form>
                                    <div class="text-muted small fw-bold d-flex align-items-center gap-1.5">
                                        <i class="bi bi-chat-left-text fs-5"></i> {{ $announcement->comments->count() }} Comments
                                    </div>
                                </div>

                                <!-- Comments Section -->
                                <div class="mt-3">
                                    <h6 class="fw-bold mb-3 small text-uppercase text-muted" style="letter-spacing: 0.5px;">Discussion</h6>
                                    
                                    <!-- Comment Form -->
                                    <form action="{{ route('announcements.comment', $announcement->id) }}" method="POST" class="mb-3">
                                        @csrf
                                        <div class="d-flex gap-2">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" name="body" class="form-control bg-light border-0 py-2 px-3 rounded-pill" placeholder="Write a comment..." style="font-size: 0.88rem;" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary px-3 rounded-pill shadow-xs flex-shrink-0"><i class="bi bi-send-fill"></i></button>
                                        </div>
                                    </form>

                                    <!-- Comment List -->
                                    <div class="d-flex flex-column gap-3" style="max-height: 380px; overflow-y: auto;">
                                        @foreach($announcement->comments as $comment)
                                            <div class="d-flex gap-2.5">
                                                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                                </div>
                                                <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                                                    <div class="bg-light p-2.5 px-3 rounded-3 border">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <strong class="small text-dark">{{ $comment->user->name }}</strong>
                                                            <small class="text-muted" style="font-size: 0.72rem;">{{ $comment->created_at->diffForHumans() }}</small>
                                                        </div>
                                                        <p class="mb-0 small text-secondary" style="line-height: 1.5;">{{ $comment->body }}</p>
                                                    </div>
                                                    
                                                    <!-- Reply Button -->
                                                    <button class="btn btn-link text-muted small text-decoration-none p-0 mt-1 ms-2 fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#replyForm{{ $comment->id }}" style="font-size: 0.75rem;">
                                                        <i class="bi bi-reply"></i> Reply
                                                    </button>

                                                    <!-- Reply Form -->
                                                    <div class="collapse mt-2" id="replyForm{{ $comment->id }}">
                                                        <form action="{{ route('announcements.comment', $announcement->id) }}" method="POST" class="d-flex gap-2">
                                                            @csrf
                                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                            <input type="text" name="body" class="form-control form-control-sm bg-light border-0 rounded-pill" placeholder="Write a reply..." required>
                                                            <button type="submit" class="btn btn-sm btn-secondary rounded-pill px-3 shadow-xs">Reply</button>
                                                        </form>
                                                    </div>

                                                    <!-- Replies -->
                                                    @if($comment->replies->count() > 0)
                                                        <div class="mt-2.5 d-flex flex-column gap-2 border-start ps-3 ms-2 border-2">
                                                            @foreach($comment->replies as $reply)
                                                                <div class="d-flex gap-2">
                                                                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 26px; height: 26px; font-size: 0.7rem;">
                                                                        {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                                                    </div>
                                                                    <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                                                                        <div class="bg-light p-2 px-3 rounded-3 border">
                                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                <strong class="small text-dark" style="font-size: 0.78rem;">{{ $reply->user->name }}</strong>
                                                                                <small class="text-muted" style="font-size: 0.68rem;">{{ $reply->created_at->diffForHumans() }}</small>
                                                                            </div>
                                                                            <p class="mb-0 text-secondary" style="font-size: 0.8rem; line-height: 1.45;">{{ $reply->body }}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 p-3 p-md-4 pt-0">
                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm p-5 text-center rounded-4">
                    <i class="bi bi-megaphone text-muted display-4 d-block mb-3"></i>
                    <h5 class="fw-bold text-muted mb-1">No Announcements Yet</h5>
                    <p class="text-muted small mb-0">Check back later for university updates and research calls.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Create Announcement Modal -->
    <div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 px-3 px-md-4 pt-3 pt-md-4 pb-0">
                    <h5 class="modal-title fw-bold text-dark">Create New Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('announcements.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-3 p-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small text-uppercase" style="font-size: 0.75rem;">Announcement Title</label>
                            <input type="text" name="title" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" placeholder="Enter announcement title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small text-uppercase" style="font-size: 0.75rem;">Content</label>
                            <textarea name="content" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" rows="5" placeholder="Enter announcement content" required></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-muted small text-uppercase" style="font-size: 0.75rem;">Image (Optional)</label>
                            <input type="file" name="image" class="form-control bg-light border-0 py-2 px-3 rounded-3">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-3 p-md-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">Create Announcement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.like-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const url = this.action;
                    const token = this.querySelector('input[name="_token"]').value;
                    const announcementId = this.dataset.announcementId;

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Update all like counts for this announcement (feed and modal)
                        document.querySelectorAll('.like-count-' + announcementId).forEach(el => {
                            el.textContent = data.likesCount;
                        });

                        // Update all like buttons for this announcement (feed and modal)
                        document.querySelectorAll('.like-btn-' + announcementId).forEach(btn => {
                            const icon = btn.querySelector('i');
                            if (data.liked) {
                                btn.classList.remove('text-muted');
                                btn.classList.add('text-primary');
                                icon.classList.remove('bi-hand-thumbs-up');
                                icon.classList.add('bi-hand-thumbs-up-fill');
                            } else {
                                btn.classList.remove('text-primary');
                                btn.classList.add('text-muted');
                                icon.classList.remove('bi-hand-thumbs-up-fill');
                                icon.classList.add('bi-hand-thumbs-up');
                            }
                        });
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
        });
    </script>
</x-app-layout>
