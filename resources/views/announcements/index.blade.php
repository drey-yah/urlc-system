<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 fw-bold d-flex align-items-center gap-3 mb-2">
                <i class="bi bi-bell text-primary"></i> Announcements
            </h1>
            <p class="text-muted mb-0">Stay updated with the latest announcements and submission guidelines</p>
        </div>
        @if(auth()->user()->role === 'admin')
            <button class="btn btn-primary px-4 d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                <i class="bi bi-plus-lg"></i> Create Announcement
            </button>
        @endif
    </div>

    <!-- Gradient Submission Banner -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="gradient-banner p-4 p-lg-5">
            <h2 class="h3 fw-bold mb-3">Submit Your Research Proposal</h2>
            <p class="mb-5 opacity-90" style="max-width: 800px;">
                We welcome innovative research proposals across all phases. Our review committee is committed to supporting groundbreaking research that advances knowledge and creates real-world impact.
            </p>

            <div class="row g-4 mt-2">
                <div class="col-md-4">
                    <div class="bg-white rounded-4 p-4 text-dark h-100 shadow-sm">
                        <small class="text-primary fw-bold text-uppercase mb-2 d-block">Phase 1</small>
                        <p class="small mb-0 text-muted">Initial proposal submission with preliminary research objectives</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white rounded-4 p-4 text-dark h-100 shadow-sm">
                        <small class="text-primary fw-bold text-uppercase mb-2 d-block">Phase 2-4</small>
                        <p class="small mb-0 text-muted">Detailed methodology and comprehensive research plan</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white rounded-4 p-4 text-dark h-100 shadow-sm">
                        <small class="text-primary fw-bold text-uppercase mb-2 d-block">Phase 5</small>
                        <p class="small mb-0 text-muted">Final implementation with results and analysis</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5">
        <div class="col-lg-8">
            <h3 class="h5 fw-bold mb-4">Recent Announcements</h3>
            
            @forelse($announcements as $announcement)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-file-earmark-text text-primary h4 mb-0"></i>
                                <h4 class="h5 fw-bold mb-0">{{ $announcement->title }}</h4>
                            </div>
                            <small class="text-muted d-flex align-items-center gap-2">
                                <i class="bi bi-calendar3"></i> {{ $announcement->created_at->format('Y-m-d') }}
                            </small>
                        </div>
                        <p class="text-muted mb-4" style="line-height: 1.6;">{{ Str::limit($announcement->content, 200) }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center gap-2 small text-muted">
                                    <i class="bi bi-person"></i> {{ $announcement->user->name }}
                                </div>
                                <div class="d-flex align-items-center gap-3 ms-2 border-start ps-3">
                                    <form action="{{ route('announcements.like', $announcement->id) }}" method="POST" class="m-0 like-form" data-announcement-id="{{ $announcement->id }}">
                                        @csrf
                                        @php
                                            $hasLiked = $announcement->likes->where('user_id', auth()->id())->isNotEmpty();
                                        @endphp
                                        <button type="submit" class="like-btn-{{ $announcement->id }} btn btn-sm btn-link text-decoration-none p-0 fw-medium d-flex align-items-center gap-1 {{ $hasLiked ? 'text-primary' : 'text-muted' }}">
                                            <i class="bi {{ $hasLiked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' }}"></i> 
                                            <span class="like-count-{{ $announcement->id }}">{{ $announcement->likes->count() }}</span>
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-link text-muted text-decoration-none p-0 fw-medium d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#annModal{{ $announcement->id }}">
                                        <i class="bi bi-chat-left-text"></i> {{ $announcement->comments->count() }}
                                    </button>
                                </div>
                            </div>
                            <button class="btn btn-link text-primary p-0 text-decoration-none small fw-bold" data-bs-toggle="modal" data-bs-target="#annModal{{ $announcement->id }}">
                                Read More <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Announcement Modal -->
                <div class="modal fade" id="annModal{{ $announcement->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                            <div class="modal-header border-0 px-4 pt-4 pb-0">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi bi-file-earmark-text text-primary h4 mb-0"></i>
                                    <h5 class="modal-title fw-bold">{{ $announcement->title }}</h5>
                                </div>
                                <button type="button" class="btn-close" data-bs-modal="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 pb-2">
                                <div class="d-flex align-items-center gap-3 mb-4 small text-muted">
                                    <span><i class="bi bi-calendar3"></i> {{ $announcement->created_at->format('M d, Y') }}</span>
                                    <span><i class="bi bi-person"></i> {{ $announcement->user->name }}</span>
                                </div>
                                <div class="text-dark" style="line-height: 1.8; white-space: pre-wrap;">{{ $announcement->content }}</div>
                                
                                <!-- Interactions Bar -->
                                <div class="d-flex align-items-center gap-4 mt-4 py-3 border-top border-bottom">
                                    <form action="{{ route('announcements.like', $announcement->id) }}" method="POST" class="m-0 like-form" data-announcement-id="{{ $announcement->id }}">
                                        @csrf
                                        @php
                                            $hasLiked = $announcement->likes->where('user_id', auth()->id())->isNotEmpty();
                                        @endphp
                                        <button type="submit" class="like-btn-{{ $announcement->id }} btn btn-sm btn-link text-decoration-none p-0 fw-bold d-flex align-items-center gap-2 {{ $hasLiked ? 'text-primary' : 'text-muted' }}">
                                            <i class="bi {{ $hasLiked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' }} fs-5"></i> 
                                            <span class="like-count-{{ $announcement->id }}">{{ $announcement->likes->count() }}</span> Likes
                                        </button>
                                    </form>
                                    <div class="text-muted small fw-bold d-flex align-items-center gap-2">
                                        <i class="bi bi-chat-left-text fs-5"></i> {{ $announcement->comments->count() }} Comments
                                    </div>
                                </div>

                                <!-- Comments Section -->
                                <div class="mt-4">
                                    <h6 class="fw-bold mb-3">Discussion</h6>
                                    
                                    <!-- Comment Form -->
                                    <form action="{{ route('announcements.comment', $announcement->id) }}" method="POST" class="mb-4">
                                        @csrf
                                        <div class="d-flex gap-2">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 38px; height: 38px;">
                                                {{ substr(auth()->user()->name, 0, 1) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" name="body" class="form-control bg-light border-0" placeholder="Write a comment..." required>
                                            </div>
                                            <button type="submit" class="btn btn-primary px-3 shadow-sm"><i class="bi bi-send-fill"></i></button>
                                        </div>
                                    </form>

                                    <!-- Comment List -->
                                    <div class="d-flex flex-column gap-4" style="max-height: 400px; overflow-y: auto;">
                                        @foreach($announcement->comments as $comment)
                                            <div class="d-flex gap-3">
                                                <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 38px; height: 38px;">
                                                    {{ substr($comment->user->name, 0, 1) }}
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="bg-light p-3 rounded-3" style="border: 1px solid rgba(0,0,0,0.03);">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <strong class="small text-dark">{{ $comment->user->name }}</strong>
                                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $comment->created_at->diffForHumans() }}</small>
                                                        </div>
                                                        <p class="mb-0 small text-muted">{{ $comment->body }}</p>
                                                    </div>
                                                    
                                                    <!-- Reply Button -->
                                                    <button class="btn btn-link text-muted small text-decoration-none p-0 mt-1 ms-2 fw-medium" type="button" data-bs-toggle="collapse" data-bs-target="#replyForm{{ $comment->id }}">
                                                        Reply
                                                    </button>

                                                    <!-- Reply Form -->
                                                    <div class="collapse mt-2" id="replyForm{{ $comment->id }}">
                                                        <form action="{{ route('announcements.comment', $announcement->id) }}" method="POST" class="d-flex gap-2">
                                                            @csrf
                                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                            <input type="text" name="body" class="form-control form-control-sm bg-light border-0" placeholder="Write a reply..." required>
                                                            <button type="submit" class="btn btn-sm btn-secondary shadow-sm">Reply</button>
                                                        </form>
                                                    </div>

                                                    <!-- Replies -->
                                                    @if($comment->replies->count() > 0)
                                                        <div class="mt-3 d-flex flex-column gap-3 border-start ps-3 ms-2 border-2">
                                                            @foreach($comment->replies as $reply)
                                                                <div class="d-flex gap-2">
                                                                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 30px; height: 30px; font-size: 0.75rem;">
                                                                        {{ substr($reply->user->name, 0, 1) }}
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div class="bg-light p-2 px-3 rounded-3" style="border: 1px solid rgba(0,0,0,0.03);">
                                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                <strong class="small text-dark" style="font-size: 0.8rem;">{{ $reply->user->name }}</strong>
                                                                                <small class="text-muted" style="font-size: 0.7rem;">{{ $reply->created_at->diffForHumans() }}</small>
                                                                            </div>
                                                                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">{{ $reply->body }}</p>
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
                            <div class="modal-footer border-0 p-4 pt-0">
                                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm p-5 text-center">
                    <p class="text-muted mb-0">No announcements available.</p>
                </div>
            @endforelse
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="h5 fw-bold mb-4">Submission Guidelines</h3>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-4 d-flex align-items-start gap-3">
                            <div class="guideline-step flex-shrink-0">1</div>
                            <div>
                                <p class="mb-0 small"><strong class="text-dark">Prepare Your Proposal:</strong> Ensure your research objectives are clearly defined with preliminary data if available.</p>
                            </div>
                        </li>
                        <li class="mb-4 d-flex align-items-start gap-3">
                            <div class="guideline-step flex-shrink-0">2</div>
                            <div>
                                <p class="mb-0 small"><strong class="text-dark">Submit Through Portal:</strong> Use the researcher dashboard to submit your proposal with all required documentation.</p>
                            </div>
                        </li>
                        <li class="mb-4 d-flex align-items-start gap-3">
                            <div class="guideline-step flex-shrink-0">3</div>
                            <div>
                                <p class="mb-0 small"><strong class="text-dark">Review Process:</strong> Our expert reviewers will evaluate your proposal and provide detailed feedback within 2-3 weeks.</p>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <div class="guideline-step flex-shrink-0">4</div>
                            <div>
                                <p class="mb-0 small"><strong class="text-dark">Email Notifications:</strong> You will receive automated email updates at each stage of the review process.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Announcement Modal -->
    <div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h5 class="modal-title fw-bold">Create New Announcement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('announcements.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small uppercase">Announcement Title</label>
                            <input type="text" name="title" class="form-control bg-light border-0 py-2 px-3" placeholder="Enter announcement title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted small uppercase">Content</label>
                            <textarea name="content" class="form-control bg-light border-0 py-2 px-3" rows="5" placeholder="Enter announcement content" required></textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-muted small uppercase">Image (Optional)</label>
                            <input type="file" name="image" class="form-control bg-light border-0 py-2 px-3">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm">Create Announcement</button>
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
