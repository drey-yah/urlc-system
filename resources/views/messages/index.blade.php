<x-app-layout>
    <div class="container-fluid px-0">
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark d-flex align-items-center gap-3 mb-1">
                    <i class="bi bi-envelope-paper-fill text-primary"></i> Messages
                </h1>
                <p class="text-muted small mb-0">Direct internal messaging platform for researchers, reviewers, and administration.</p>
            </div>
            <button class="btn btn-primary px-4 py-2 rounded-pill fw-bold d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#composeModal">
                <i class="bi bi-pencil-square"></i> Compose Message
            </button>
        </div>

        <!-- Gmail-style Interface Wrapper -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="min-height: 720px;">
            <div class="row g-0">
                <!-- Left Navigation Column (Folders) -->
                <div class="col-lg-3 col-md-4 border-end bg-light p-3">
                    <div class="d-grid mb-3">
                        <button class="btn btn-primary py-2.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-xs" data-bs-toggle="modal" data-bs-target="#composeModal">
                            <i class="bi bi-plus-lg fs-5"></i> Compose
                        </button>
                    </div>

                    <div class="nav flex-column nav-pills gap-1">
                        <a href="{{ route('messages.index', ['folder' => 'inbox']) }}" class="nav-link text-dark fw-semibold d-flex justify-content-between align-items-center py-2.5 px-3 rounded-3 {{ $folder === 'inbox' ? 'active bg-white text-primary shadow-xs' : 'hover-bg-light' }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-inbox-fill text-primary"></i> Inbox
                            </div>
                            @if(isset($unreadCount) && $unreadCount > 0)
                                <span class="badge bg-primary rounded-pill">{{ $unreadCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('messages.index', ['folder' => 'sent']) }}" class="nav-link text-dark fw-semibold d-flex justify-content-between align-items-center py-2.5 px-3 rounded-3 {{ $folder === 'sent' ? 'active bg-white text-primary shadow-xs' : 'hover-bg-light' }}">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-send-fill text-secondary"></i> Sent Mail
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Middle Column: Message List -->
                <div class="col-lg-4 col-md-8 border-end bg-white">
                    <div class="p-3 border-bottom bg-light">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchMessages" class="form-control bg-white border-start-0 py-2" placeholder="Search conversations...">
                        </div>
                    </div>

                    <div class="message-list overflow-auto" style="max-height: 650px;">
                        @forelse($messages as $msg)
                            @php
                                $otherUser = ($folder === 'sent') ? $msg->recipient : $msg->sender;
                                $isSelected = $selectedMessage && $selectedMessage->id === $msg->id;
                                $isUnread = !$msg->is_read && $msg->recipient_id === auth()->id();
                            @endphp
                            <a href="{{ route('messages.index', ['folder' => $folder, 'message' => $msg->id]) }}" 
                               class="d-block p-3 border-bottom text-decoration-none transition-all {{ $isSelected ? 'bg-primary bg-opacity-10 border-start border-primary border-4' : ($isUnread ? 'bg-white font-weight-bold fw-bold' : 'bg-white hover-bg-light') }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                            {{ strtoupper(substr($otherUser->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="text-dark small {{ $isUnread ? 'fw-bold text-black' : 'fw-semibold' }} text-truncate" style="max-width: 140px;">
                                            {{ $otherUser->name ?? 'Unknown User' }}
                                        </span>
                                    </div>
                                    <small class="text-muted x-small" style="font-size: 0.75rem;">
                                        {{ $msg->created_at->isToday() ? $msg->created_at->format('h:i A') : $msg->created_at->format('M d') }}
                                    </small>
                                </div>
                                <div class="ps-4">
                                    <div class="text-dark small mb-1 {{ $isUnread ? 'fw-bold text-black' : 'fw-medium' }} text-truncate">
                                        {{ $msg->subject }}
                                    </div>
                                    <div class="text-muted x-small text-truncate" style="font-size: 0.8rem;">
                                        {{ Str::limit($msg->body, 60) }}
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-5 text-center text-muted">
                                <i class="bi bi-envelope-open display-6 d-block mb-3 opacity-50"></i>
                                <p class="small mb-0">No messages found in {{ $folder }}.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Right Column: Message Detail & Thread Reader -->
                <div class="col-lg-5 col-12 bg-white d-flex flex-column" style="min-height: 650px;">
                    @if($selectedMessage)
                        <!-- Thread Header -->
                        <div class="p-4 border-bottom d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="h5 fw-bold text-dark mb-2">{{ $selectedMessage->subject }}</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary fw-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        {{ strtoupper(substr($selectedMessage->sender->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="small fw-bold text-dark">
                                            {{ $selectedMessage->sender->name ?? 'System User' }}
                                            <span class="badge bg-light text-dark border ms-1 font-monospace">{{ strtoupper($selectedMessage->sender->role ?? 'user') }}</span>
                                        </div>
                                        <div class="x-small text-muted" style="font-size: 0.75rem;">
                                            To: {{ $selectedMessage->recipient->name ?? 'Me' }} &bull; {{ $selectedMessage->created_at->format('F d, Y h:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <form action="{{ route('messages.destroy', $selectedMessage->id) }}" method="POST" onsubmit="return confirm('Delete this conversation thread?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-2" title="Delete Conversation">
                                        <i class="bi bi-trash fs-5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Thread Main Body & Replies -->
                        <div class="p-4 flex-grow-1 overflow-auto" style="max-height: 440px;">
                            <!-- Original Message Card -->
                            <div class="bg-light p-4 rounded-4 mb-4 border">
                                <div class="text-dark" style="line-height: 1.7; white-space: pre-wrap; font-size: 0.95rem;">{{ $selectedMessage->body }}</div>
                            </div>

                            <!-- Replies List -->
                            @if($selectedMessage->replies->count() > 0)
                                <div class="mb-4">
                                    <h6 class="small fw-bold text-muted text-uppercase mb-3 px-1">Replies & Updates ({{ $selectedMessage->replies->count() }})</h6>
                                    @foreach($selectedMessage->replies as $reply)
                                        <div class="p-3 mb-3 rounded-4 border bg-white shadow-xs">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                                        {{ strtoupper(substr($reply->sender->name ?? 'U', 0, 1)) }}
                                                    </div>
                                                    <span class="small fw-bold text-dark">{{ $reply->sender->name }}</span>
                                                </div>
                                                <span class="x-small text-muted" style="font-size: 0.75rem;">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="mb-0 small text-secondary" style="line-height: 1.6; white-space: pre-wrap;">{{ $reply->body }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Inline Reply Box -->
                        <div class="p-3 border-top bg-light mt-auto">
                            <form action="{{ route('messages.reply', ['id' => $selectedMessage->id, 'folder' => $folder]) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <textarea name="body" class="form-control border-0 shadow-xs py-2.5 px-3" rows="2" placeholder="Write a reply..." required></textarea>
                                    <button type="submit" class="btn btn-primary px-4 fw-bold d-flex align-items-center gap-2">
                                        <i class="bi bi-send-fill"></i> Reply
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted p-5">
                            <i class="bi bi-chat-left-dots display-3 mb-3 opacity-25"></i>
                            <h5 class="fw-bold mb-1">No Conversation Selected</h5>
                            <p class="small text-center mb-0">Select a message from the left to view the conversation thread or compose a new message.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Compose Message -->
    <div class="modal fade" id="composeModal" tabindex="-1" aria-labelledby="composeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold text-primary" id="composeModalLabel">
                        <i class="bi bi-pencil-square me-2"></i> New Message
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted uppercase">To (Recipient)</label>
                            <select name="recipient_id" class="form-select bg-light border-0 py-2 px-3 rounded-3" required>
                                <option value="" disabled selected>Select a recipient...</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">
                                        {{ $u->name }} ({{ strtoupper($u->role) }}{{ $u->department ? ' - ' . strtoupper($u->department) : '' }}) - {{ $u->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted uppercase">Subject</label>
                            <input type="text" name="subject" class="form-control bg-light border-0 py-2 px-3 rounded-3" placeholder="Enter message subject" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-muted uppercase">Message Body</label>
                            <textarea name="body" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" rows="6" placeholder="Type your message here..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pb-4 px-4">
                        <button type="button" class="btn btn-light px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm d-flex align-items-center gap-2">
                            <i class="bi bi-send-fill"></i> Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Simple client-side search filter script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchMessages');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const filter = this.value.toLowerCase();
                    const items = document.querySelectorAll('.message-list a');
                    items.forEach(function(item) {
                        const text = item.textContent.toLowerCase();
                        if (text.includes(filter)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>
