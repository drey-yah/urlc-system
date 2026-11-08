<x-app-layout>
    <!-- Action Bar -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('service_credits.index') }}" class="text-decoration-none">Service Credits</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dossier #{{ str_pad($list->id, 5, '0', STR_PAD_LEFT) }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h1 class="h4 h-md-3 fw-bold text-dark mb-0">{{ $list->title }}</h1>
                @if($list->status === 'approved')
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 rounded-pill fw-bold">
                        <i class="bi bi-patch-check-fill me-1"></i> Officially Approved by VPREI
                    </span>
                @else
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1 rounded-pill fw-bold">
                        <i class="bi bi-clock-history me-1"></i> Awaiting VPREI Executive Approval
                    </span>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('service_credits.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Back to Dossiers
            </a>

            <a href="{{ route('service_credits.pdf', $list->id) }}" class="btn btn-primary rounded-pill px-3 py-2 small fw-bold shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download Official Portrait PDF
            </a>

            @if(in_array(auth()->user()->role, ['vprei', 'super_admin']) && $list->status === 'pending_approval')
                <button type="button" class="btn btn-success rounded-pill px-3 py-2 small fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="bi bi-check-circle-fill me-1"></i> Approve Service Credits List
                </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Document Paper Preview (Portrait Proportion matching official template) -->
    <div class="card border-0 shadow-sm rounded-4 mx-auto mb-5" style="max-width: 860px;">
        <div class="card-body p-4 p-md-5 bg-white text-dark" style="font-family: Arial, sans-serif;">
            
            <!-- Institutional Header -->
            <div class="text-center mb-4">
                <img src="{{ asset('logo.png') }}" alt="University of Antique Seal" style="width: 75px; height: 75px; object-fit: contain;" class="mb-2">
                <div class="small text-muted mb-0">Republic of the Philippines</div>
                <div class="fw-bold text-uppercase fs-5" style="letter-spacing: 0.5px;">UNIVERSITY OF ANTIQUE</div>
                <div class="small text-muted">Sibalom, Antique</div>
                <div class="fw-bold text-dark mt-2" style="font-size: 0.95rem;">Research Unit</div>
                <div class="small text-primary text-decoration-underline" style="font-size: 0.82rem;">research@antiquespride.edu.ph</div>
            </div>

            <!-- Document Title -->
            <div class="text-center my-4">
                <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem; letter-spacing: 0.2px;">
                    List of Researchers Qualified to Avail Service Credits
                </h5>
            </div>

            <!-- Table (6 Columns matching UA template) -->
            <div class="table-responsive my-4">
                <table class="table table-bordered border-dark align-middle mb-0" style="border: 1px solid #000; font-size: 0.85rem;">
                    <thead class="bg-light text-center fw-bold">
                        <tr>
                            <th style="width: 6%; border: 1px solid #000; vertical-align: middle;">No.</th>
                            <th style="width: 25%; border: 1px solid #000; vertical-align: middle;">Name of Researcher/s</th>
                            <th style="width: 14%; border: 1px solid #000; vertical-align: middle;">College</th>
                            <th style="width: 29%; border: 1px solid #000; vertical-align: middle;">Title of Research</th>
                            <th style="width: 13%; border: 1px solid #000; vertical-align: middle;">Date Completed</th>
                            <th style="width: 13%; border: 1px solid #000; vertical-align: middle;">Service Credits Granted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $itemIndex = 1;
                        @endphp
                        @forelse($items as $row)
                            <tr>
                                <td class="text-center fw-bold" style="border: 1px solid #000; padding: 8px 6px;">
                                    {{ $row['no'] ?? $itemIndex }}.
                                </td>
                                <td style="border: 1px solid #000; padding: 8px 8px;">
                                    {{ $row['researchers'] ?? 'N/A' }}
                                </td>
                                <td style="border: 1px solid #000; padding: 8px 8px;">
                                    {{ $row['college'] ?? 'N/A' }}
                                </td>
                                <td style="border: 1px solid #000; padding: 8px 8px;">
                                    {{ $row['title'] ?? 'N/A' }}
                                </td>
                                <td class="text-center" style="border: 1px solid #000; padding: 8px 6px;">
                                    {{ $row['date_completed'] ?? 'N/A' }}
                                </td>
                                <td class="text-center fw-semibold text-primary" style="border: 1px solid #000; padding: 8px 6px;">
                                    {{ $row['service_credits'] ?? '15 Days' }}
                                </td>
                            </tr>
                            @php
                                $itemIndex++;
                            @endphp
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted p-4" style="border: 1px solid #000;">
                                    No researchers recorded in this service credits qualification list.
                                </td>
                            </tr>
                        @endforelse

                        {{-- Additional blank rows if less than 8 items to match paper layout --}}
                        @for ($i = $itemIndex; $i <= 8; $i++)
                            <tr>
                                <td class="text-center" style="border: 1px solid #000; height: 35px; padding: 8px 6px;">{{ $i }}.&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 8px 8px;">&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 8px 8px;">&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 8px 8px;">&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 8px 6px;">&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 8px 6px;">&nbsp;</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <!-- Mandatory Footnote Note matching template -->
            <div class="fst-italic text-dark mb-5" style="font-size: 0.82rem;">
                Attachments: Terminal Report (including Full Manuscript), and Certificate of Completion
            </div>

            <!-- Signatures Section (Prepared by Director, Approved by VPREI matching template) -->
            <div class="row mt-4 pt-2">
                <div class="col-12 col-sm-6 mb-4 mb-sm-0">
                    <div class="small text-muted mb-4">Prepared by:</div>
                    <div class="d-inline-block">
                        <div class="fw-bold text-uppercase border-bottom border-dark pb-1" style="min-width: 250px;">
                            {{ $list->preparer?->name ?? '____________________________________' }}
                        </div>
                        <div class="small text-dark mt-1">Director, Research Unit</div>
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="small text-muted mb-4">Approved by:</div>
                    <div class="d-inline-block">
                        <div class="fw-bold text-uppercase border-bottom border-dark pb-1" style="min-width: 250px;">
                            @if($list->status === 'approved' && $list->approver)
                                {{ $list->approver->name }}
                            @else
                                <span class="text-muted fst-italic fw-normal">____________________________________</span>
                            @endif
                        </div>
                        <div class="small text-dark mt-1">VP for Research, Extension, and Innovation</div>
                        @if($list->status === 'approved' && $list->approved_at)
                            <div class="text-success small mt-1" style="font-size: 0.75rem;">
                                <i class="bi bi-patch-check-fill me-1"></i> Formally Approved on {{ $list->approved_at->format('F d, Y h:i A') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($list->remarks)
                <div class="mt-4 pt-3 border-top">
                    <div class="small text-muted fw-bold">Remarks / Internal Notes:</div>
                    <div class="small text-secondary">{{ $list->remarks }}</div>
                </div>
            @endif

        </div>
    </div>

    <!-- VPREI Executive Approval Confirmation Modal -->
    @if(in_array(auth()->user()->role, ['vprei', 'super_admin']) && $list->status === 'pending_approval')
        <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-0 bg-success bg-opacity-10 p-4 pb-2">
                        <h5 class="modal-title fw-bold text-success d-flex align-items-center gap-2" id="approveModalLabel">
                            <i class="bi bi-check2-circle fs-4"></i> Approve Service Credits Roster
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('service_credits.approve', $list->id) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <p class="text-muted small mb-3">
                                As the <strong>Vice President for Research, Extension, and Innovation</strong>, your executive approval confirms that the faculty researchers listed below have fulfilled all institutional research obligations and are formally granted service credits.
                            </p>
                            <div class="p-3 bg-light rounded-3 small">
                                <div><strong>Dossier:</strong> {{ $list->title }}</div>
                                <div><strong>Prepared by:</strong> {{ $list->preparer?->name ?? 'Director, Research Unit' }}</div>
                                <div><strong>Qualified Projects:</strong> {{ count($list->proposal_ids ?? []) }} Research(es)</div>
                                <div><strong>Date of Approval:</strong> {{ date('F d, Y') }}</div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 small" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success rounded-pill px-4 py-2 small fw-bold">
                                <i class="bi bi-pen-fill me-1"></i> Confirm & Officially Approve
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
