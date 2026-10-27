@php
    $user = auth()->user();
    $allNotifications = $user ? $user->notifications : collect();
    $unreadCount = $user ? $user->unreadNotifications->count() : 0;
    $totalCount = $allNotifications->count();
    $isMobileView = $isMobile ?? false;
    $uniqueSuffix = $isMobileView ? 'mobile' : 'desktop';
@endphp

<div class="dropdown notification-dropdown-wrapper">
    @if($isMobileView)
        <button type="button" 
                class="btn btn-link text-white p-1 text-decoration-none position-relative notif-bell-btn" 
                id="notifDropdown-{{ $uniqueSuffix }}" 
                data-bs-toggle="dropdown" 
                data-bs-auto-close="outside"
                aria-expanded="false" 
                title="Notifications">
            <i class="bi bi-bell-fill fs-5" style="color: var(--ua-gold, #E5A93C); display: inline-block;"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-dark shadow-xs fw-bold px-1.5 py-0.5 notif-count-badge {{ $unreadCount > 0 ? '' : 'd-none' }}" 
                  style="font-size: 0.62rem; min-width: 17px; margin-left: -4px; margin-top: 4px;">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        </button>
    @else
        <button type="button" 
                class="btn btn-white bg-white border rounded-circle shadow-xs position-relative d-inline-flex align-items-center justify-content-center p-0 notif-bell-btn" 
                id="notifDropdown-{{ $uniqueSuffix }}" 
                data-bs-toggle="dropdown" 
                data-bs-auto-close="outside"
                aria-expanded="false" 
                title="Notifications"
                style="width: 40px; height: 40px; color: #4b5563; transition: all 0.2s ease;">
            <i class="bi bi-bell-fill fs-5" style="color: var(--ua-maroon, #8C1D2C); display: inline-block;"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-2 border-white shadow-xs fw-bold px-1.5 py-0.5 notif-count-badge {{ $unreadCount > 0 ? '' : 'd-none' }}" 
                  style="font-size: 0.65rem; min-width: 18px; margin-left: -4px; margin-top: 4px;">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        </button>
    @endif

    <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-0 overflow-hidden notif-dropdown-menu" 
         aria-labelledby="notifDropdown-{{ $uniqueSuffix }}"
         style="width: 380px; max-width: 92vw; margin-top: 0.6rem; z-index: 1060;">
        
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between px-3.5 py-3 border-bottom bg-white" style="border-color: rgba(0,0,0,0.06) !important;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle p-1.5 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(140, 29, 44, 0.08); color: #8C1D2C;">
                    <i class="bi bi-bell-fill fs-6"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.92rem;">Notifications</h6>
                    <span class="notif-header-unread-sub {{ $unreadCount > 0 ? 'text-danger fw-semibold' : 'text-muted' }} small" style="font-size: 0.72rem;">
                        {{ $unreadCount > 0 ? $unreadCount . ' new update' . ($unreadCount > 1 ? 's' : '') : 'All caught up' }}
                    </span>
                </div>
            </div>
            @if($unreadCount > 0)
                <form action="{{ route('notifications.markRead') }}" method="POST" class="m-0 notif-mark-read-form">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold text-primary" style="font-size: 0.75rem;">
                        <i class="bi bi-check2-all me-1"></i>Mark all read
                    </button>
                </form>
            @endif
        </div>

        <!-- Scrollable Notification List (Showing 10 by default) -->
        <div class="notif-scroll-container notif-scroll-container-{{ $uniqueSuffix }}" style="max-height: 400px; overflow-y: auto;">
            @forelse($allNotifications as $index => $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $isExtra = $index >= 10;
                    $titleLower = strtolower($notification->data['title'] ?? '');
                    
                    if (str_contains($titleLower, 'return')) {
                        $badgeBg = 'rgba(239, 68, 68, 0.12)';
                        $badgeColor = '#dc2626';
                        $itemIcon = 'bi-arrow-return-left';
                    } elseif (str_contains($titleLower, 'endorse') || str_contains($titleLower, 'noted') || str_contains($titleLower, 'accept') || str_contains($titleLower, 'approve')) {
                        $badgeBg = 'rgba(16, 185, 129, 0.12)';
                        $badgeColor = '#059669';
                        $itemIcon = 'bi-check-circle-fill';
                    } elseif (str_contains($titleLower, 'announcement')) {
                        $badgeBg = 'rgba(245, 158, 11, 0.12)';
                        $badgeColor = '#d97706';
                        $itemIcon = 'bi-megaphone-fill';
                    } else {
                        $badgeBg = 'rgba(59, 130, 246, 0.12)';
                        $badgeColor = '#2563eb';
                        $itemIcon = $notification->data['icon'] ?? 'bi-bell-fill';
                    }
                @endphp

                <a href="{{ route('notifications.readSingle', $notification->id) }}" 
                   class="notification-item d-flex align-items-start gap-2.5 p-3 text-decoration-none border-bottom transition-all {{ $isExtra ? 'd-none extra-notif-item-'.$uniqueSuffix : '' }}"
                   style="border-color: rgba(0,0,0,0.05) !important; {{ $isUnread ? 'background-color: #fef8f8; border-left: 3.5px solid #8C1D2C !important;' : 'background-color: #ffffff;' }}">
                    
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" 
                         style="width: 34px; height: 34px; background: {{ $badgeBg }}; color: {{ $badgeColor }};">
                        <i class="bi {{ $itemIcon }} fs-6"></i>
                    </div>

                    <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                        <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                            <div class="fw-bold text-dark text-truncate small {{ $isUnread ? 'text-primary' : '' }}" style="font-size: 0.82rem; line-height: 1.25;">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </div>
                            @if($isUnread)
                                <span class="badge rounded-circle p-1 bg-danger flex-shrink-0" style="width: 6px; height: 6px;" title="Unread"></span>
                            @endif
                        </div>

                        <p class="text-muted mb-1 small text-wrap" style="font-size: 0.76rem; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $notification->data['message'] ?? '' }}
                        </p>

                        <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.68rem;">
                            <i class="bi bi-clock"></i>
                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-5 px-3">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="bi bi-bell-slash fs-4 text-muted"></i>
                    </div>
                    <p class="text-dark fw-semibold small mb-0">No notifications yet</p>
                    <span class="text-muted" style="font-size: 0.72rem;">You're all caught up! New updates will appear here.</span>
                </div>
            @endforelse
        </div>

        <!-- See More Button (when total notifications > 10) -->
        @if($totalCount > 10)
            <div class="p-2 border-top bg-light bg-opacity-50 text-center see-more-wrapper-{{ $uniqueSuffix }}" id="seeMoreWrapper-{{ $uniqueSuffix }}">
                <button type="button" 
                        class="btn btn-sm btn-light border text-primary fw-semibold rounded-pill px-3 py-1 shadow-xs" 
                        onclick="toggleSeeMoreNotifications('{{ $uniqueSuffix }}', this)"
                        style="font-size: 0.75rem;">
                    <i class="bi bi-chevron-down me-1"></i> See more ({{ $totalCount - 10 }} more)
                </button>
            </div>
        @endif

        <!-- Footer -->
        <div class="p-2.5 px-3 bg-white border-top text-center d-flex justify-content-between align-items-center">
            <span class="text-muted small" style="font-size: 0.72rem;">
                Showing <strong class="text-dark notif-count-showing">{{ min(10, $totalCount) }}</strong> of <strong class="text-dark notif-count-total">{{ $totalCount }}</strong>
            </span>
            <a href="{{ route('announcements.index') }}" class="small text-decoration-none fw-semibold text-muted" style="font-size: 0.74rem;">
                Announcements <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

@once
<!-- Live Floating Notification Toast Container -->
<div id="liveNotificationToastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1090; margin-top: 65px; pointer-events: none;"></div>

<script>
    let currentUnreadCount = {{ $unreadCount }};
    let lastKnownNotificationId = '{{ $allNotifications->first() ? $allNotifications->first()->id : "" }}';
    let isPolling = false;

    function pollLiveNotifications() {
        if (isPolling || document.hidden) return;
        isPolling = true;

        fetch('{{ route('notifications.poll') }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Poll error');
            return response.json();
        })
        .then(data => {
            if (typeof data.unread_count !== 'undefined') {
                const newCount = data.unread_count;
                const previousCount = currentUnreadCount;
                
                // If unread count increased and there are notifications, show a live toast for the newest update
                if (newCount > previousCount && data.notifications && data.notifications.length > 0) {
                    const newest = data.notifications[0];
                    if (newest && newest.is_unread && newest.id !== lastKnownNotificationId) {
                        lastKnownNotificationId = newest.id;
                        showLiveToast(newest);
                    }
                }
                
                currentUnreadCount = newCount;
                updateLiveBadges(newCount);

                // Update the dropdown menus live
                if (data.notifications) {
                    updateLiveDropdownLists(data);
                }
            }
        })
        .catch(() => {})
        .finally(() => {
            isPolling = false;
        });
    }

    function updateLiveBadges(count) {
        const badges = document.querySelectorAll('.notif-count-badge');
        const bellIcons = document.querySelectorAll('.notif-bell-btn i');

        badges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        });

        // Trigger gentle bell shake if there are unread notifications
        if (count > 0) {
            bellIcons.forEach(icon => {
                icon.classList.remove('animate-bell-ring');
                void icon.offsetWidth;
                icon.classList.add('animate-bell-ring');
            });
        }
    }

    function showLiveToast(notif) {
        const container = document.getElementById('liveNotificationToastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = 'live-notif-toast p-3 mb-2 shadow-lg border border-danger border-opacity-25';
        toast.style.pointerEvents = 'auto';

        toast.innerHTML = `
            <div class="d-flex align-items-start gap-2.5">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" 
                     style="width: 32px; height: 32px; background: ${notif.badge_bg}; color: ${notif.badge_color};">
                    <i class="bi ${notif.icon} fs-6"></i>
                </div>
                <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                    <div class="d-flex align-items-center justify-content-between mb-0.5">
                        <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-0.5 rounded-pill fw-bold" style="font-size: 0.65rem;">NEW UPDATE</span>
                        <button type="button" class="btn-close" style="font-size: 0.65rem;" onclick="this.closest('.live-notif-toast').remove()"></button>
                    </div>
                    <div class="fw-bold text-dark text-truncate small mb-1" style="font-size: 0.82rem;">${notif.title}</div>
                    <p class="text-muted small mb-1.5 text-truncate" style="font-size: 0.74rem;">${notif.message}</p>
                    <a href="${notif.read_url}" class="btn btn-sm btn-primary rounded-pill py-0.5 px-3 fw-semibold shadow-xs" style="font-size: 0.72rem;">
                        View Update <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        `;

        container.appendChild(toast);

        // Auto remove after 7 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(50px)';
                setTimeout(() => toast.remove(), 400);
            }
        }, 7000);
    }

    function updateLiveDropdownLists(data) {
        // Update header subtext
        document.querySelectorAll('.notif-header-unread-sub').forEach(el => {
            if (data.unread_count > 0) {
                el.textContent = `${data.unread_count} new update${data.unread_count > 1 ? 's' : ''}`;
                el.className = 'notif-header-unread-sub text-danger fw-semibold small';
            } else {
                el.textContent = 'All caught up';
                el.className = 'notif-header-unread-sub text-muted small';
            }
        });

        // Update counts in footer
        document.querySelectorAll('.notif-count-showing').forEach(el => el.textContent = Math.min(10, data.total_count));
        document.querySelectorAll('.notif-count-total').forEach(el => el.textContent = data.total_count);

        // Update lists for desktop and mobile
        ['desktop', 'mobile'].forEach(suffix => {
            const container = document.querySelector('.notif-scroll-container-' + suffix);
            if (!container || !data.notifications) return;

            if (data.notifications.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5 px-3">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                            <i class="bi bi-bell-slash fs-4 text-muted"></i>
                        </div>
                        <p class="text-dark fw-semibold small mb-0">No notifications yet</p>
                        <span class="text-muted" style="font-size: 0.72rem;">You're all caught up! New updates will appear here.</span>
                    </div>
                `;
                return;
            }

            let html = '';
            data.notifications.forEach((notification, index) => {
                const isExtra = index >= 10;
                html += `
                    <a href="${notification.read_url}" 
                       class="notification-item d-flex align-items-start gap-2.5 p-3 text-decoration-none border-bottom transition-all ${isExtra ? 'd-none extra-notif-item-' + suffix : ''}"
                       style="border-color: rgba(0,0,0,0.05) !important; ${notification.is_unread ? 'background-color: #fef8f8; border-left: 3.5px solid #8C1D2C !important;' : 'background-color: #ffffff;'}">
                        
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 mt-0.5" 
                             style="width: 34px; height: 34px; background: ${notification.badge_bg}; color: ${notification.badge_color};">
                            <i class="bi ${notification.icon} fs-6"></i>
                        </div>

                        <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                            <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                <div class="fw-bold text-dark text-truncate small ${notification.is_unread ? 'text-primary' : ''}" style="font-size: 0.82rem; line-height: 1.25;">
                                    ${notification.title}
                                </div>
                                ${notification.is_unread ? '<span class="badge rounded-circle p-1 bg-danger flex-shrink-0" style="width: 6px; height: 6px;" title="Unread"></span>' : ''}
                            </div>

                            <p class="text-muted mb-1 small text-wrap" style="font-size: 0.76rem; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                ${notification.message}
                            </p>

                            <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.68rem;">
                                <i class="bi bi-clock"></i>
                                <span>${notification.created_at_human}</span>
                            </div>
                        </div>
                    </a>
                `;
            });
            container.innerHTML = html;
        });
    }

    function toggleSeeMoreNotifications(suffix, btn) {
        const extraItems = document.querySelectorAll('.extra-notif-item-' + suffix);
        const isExpanded = btn.getAttribute('data-expanded') === 'true';

        if (!isExpanded) {
            extraItems.forEach(function(item) {
                item.classList.remove('d-none');
            });
            btn.setAttribute('data-expanded', 'true');
            btn.innerHTML = '<i class="bi bi-chevron-up me-1"></i> Show less';
        } else {
            extraItems.forEach(function(item) {
                item.classList.add('d-none');
                item.classList.add('d-none');
            });
            btn.setAttribute('data-expanded', 'false');
            btn.innerHTML = '<i class="bi bi-chevron-down me-1"></i> See more (' + extraItems.length + ' more)';
        }
    }

    // Run auto-polling every 15 seconds
    setInterval(pollLiveNotifications, 15000);

    // Immediate poll when user switches back to this tab
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            pollLiveNotifications();
        }
    });
</script>
@endonce
