@php
    $user = auth()->user();
    if (!$user) return;

    $userRoles = $user->allRoles();
    $activeRole = $user->activeRole();
    $isMulti = count($userRoles) > 1 || $user->isSuperAdmin();
    $isMobileView = $isMobile ?? false;

    $roleMeta = [
        'researcher' => ['label' => 'Researcher', 'icon' => 'bi-mortarboard-fill', 'color' => '#059669', 'bg' => 'rgba(5, 150, 105, 0.12)'],
        'reviewer' => ['label' => 'Evaluator / Reviewer', 'icon' => 'bi-clipboard-check-fill', 'color' => '#2563eb', 'bg' => 'rgba(37, 99, 235, 0.12)'],
        'coordinator' => ['label' => 'Coordinator', 'icon' => 'bi-check-circle-fill', 'color' => '#7c3aed', 'bg' => 'rgba(124, 58, 237, 0.12)'],
        'dean' => ['label' => 'Dean', 'icon' => 'bi-bank2', 'color' => '#dc2626', 'bg' => 'rgba(220, 38, 38, 0.12)'],
        'staff' => ['label' => 'Receiving Staff', 'icon' => 'bi-inbox-fill', 'color' => '#0891b2', 'bg' => 'rgba(8, 145, 178, 0.12)'],
        'recording_staff' => ['label' => 'Recording Staff', 'icon' => 'bi-journal-text', 'color' => '#d97706', 'bg' => 'rgba(217, 119, 6, 0.12)'],
        'budget_officer' => ['label' => 'Budget Officer', 'icon' => 'bi-cash-stack', 'color' => '#b45309', 'bg' => 'rgba(180, 83, 9, 0.12)'],
        'sao_finance' => ['label' => 'Finance Officer', 'icon' => 'bi-bank', 'color' => '#4f46e5', 'bg' => 'rgba(79, 70, 229, 0.12)'],
        'vprei' => ['label' => 'VPREI', 'icon' => 'bi-award-fill', 'color' => '#8C1D2C', 'bg' => 'rgba(140, 29, 44, 0.12)'],
        'president' => ['label' => 'President', 'icon' => 'bi-person-workspace', 'color' => '#8C1D2C', 'bg' => 'rgba(140, 29, 44, 0.12)'],
        'admin' => ['label' => 'Research Director', 'icon' => 'bi-shield-check', 'color' => '#8C1D2C', 'bg' => 'rgba(140, 29, 44, 0.12)'],
        'super_admin' => ['label' => 'Super Admin', 'icon' => 'bi-shield-lock-fill', 'color' => '#111827', 'bg' => 'rgba(17, 24, 39, 0.12)'],
        'funding_agency' => ['label' => 'Funding Agency', 'icon' => 'bi-building', 'color' => '#0d9488', 'bg' => 'rgba(13, 148, 136, 0.12)'],
    ];

    $currentMeta = $roleMeta[$activeRole] ?? [
        'label' => ucfirst(str_replace('_', ' ', $activeRole)),
        'icon' => 'bi-person-badge-fill',
        'color' => '#4b5563',
        'bg' => 'rgba(75, 85, 99, 0.12)'
    ];
@endphp

@if($isMulti)
    <div class="dropdown role-switcher-wrapper">
        @if($isMobileView)
            <button type="button" 
                    class="btn btn-sm btn-link text-white p-1 text-decoration-none dropdown-toggle d-flex align-items-center gap-1" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false" 
                    title="Switch Portal Role">
                <span class="badge rounded-pill shadow-xs d-inline-flex align-items-center gap-1 px-2 py-1" 
                      style="background: {{ $currentMeta['bg'] }}; color: var(--ua-gold, #E5A93C); border: 1px solid rgba(229,169,60,0.3); font-size: 0.72rem;">
                    <i class="bi {{ $currentMeta['icon'] }}"></i>
                    <span>{{ $currentMeta['label'] }}</span>
                </span>
            </button>
        @else
            <button type="button" 
                    class="btn btn-white bg-white border rounded-pill shadow-xs d-flex align-items-center gap-2 py-1.5 px-3 dropdown-toggle notif-bell-btn" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false" 
                    style="transition: all 0.2s ease;">
                <span class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                      style="width: 22px; height: 22px; background: {{ $currentMeta['bg'] }}; color: {{ $currentMeta['color'] }}; font-size: 0.75rem;">
                    <i class="bi {{ $currentMeta['icon'] }}"></i>
                </span>
                <span class="small fw-bold text-dark text-truncate" style="max-width: 150px; font-size: 0.8rem;">
                    {{ $currentMeta['label'] }}
                </span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-1.5 py-0.5 fw-semibold" style="font-size: 0.65rem;">
                    Active View
                </span>
            </button>
        @endif

        <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2" style="width: 280px; z-index: 1060;">
            <div class="px-2.5 py-1.5 mb-1 border-bottom">
                <div class="text-uppercase fw-bold text-muted" style="font-size: 0.68rem; letter-spacing: 0.05em;">Switch Portal View</div>
                <div class="small text-muted" style="font-size: 0.74rem;">Select which role interface to access</div>
            </div>

            @foreach($userRoles as $roleKey)
                @php
                    $isRoleActive = $roleKey === $activeRole;
                    $meta = $roleMeta[$roleKey] ?? [
                        'label' => ucfirst(str_replace('_', ' ', $roleKey)),
                        'icon' => 'bi-person-badge-fill',
                        'color' => '#4b5563',
                        'bg' => 'rgba(75, 85, 99, 0.12)'
                    ];
                @endphp

                <form action="{{ route('role.switch') }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="role" value="{{ $roleKey }}">
                    <button type="submit" 
                            class="dropdown-item rounded-3 py-2 px-2.5 d-flex align-items-center justify-content-between my-0.5 {{ $isRoleActive ? 'bg-light fw-bold text-dark' : 'text-secondary' }}"
                            style="font-size: 0.82rem; transition: background 0.15s ease;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                                  style="width: 26px; height: 26px; background: {{ $meta['bg'] }}; color: {{ $meta['color'] }}; font-size: 0.8rem;">
                                <i class="bi {{ $meta['icon'] }}"></i>
                            </span>
                            <span>{{ $meta['label'] }}</span>
                        </div>
                        @if($isRoleActive)
                            <span class="badge bg-success rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                <i class="bi bi-check-lg"></i> Current
                            </span>
                        @else
                            <i class="bi bi-arrow-right-short text-muted fs-6"></i>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    </div>
@endif
