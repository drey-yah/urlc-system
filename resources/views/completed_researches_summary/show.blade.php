<x-app-layout>
    <!-- Action Bar -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('completed_researches_summary.index') }}" class="text-decoration-none">Completed Summary Forms</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Form #{{ str_pad($summary->id, 5, '0', STR_PAD_LEFT) }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h1 class="h4 h-md-3 fw-bold text-dark mb-0">{{ $summary->title }}</h1>
                @if($summary->status === 'noted')
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 rounded-pill fw-bold">
                        <i class="bi bi-check2-all me-1"></i> Formally Noted by College Dean
                    </span>
                @else
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1 rounded-pill fw-bold">
                        <i class="bi bi-clock-history me-1"></i> Awaiting Dean Noting
                    </span>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('completed_researches_summary.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Back to Summary Forms
            </a>

            <a href="{{ route('completed_researches_summary.pdf', $summary->id) }}" class="btn btn-primary rounded-pill px-3 py-2 small fw-bold shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download Official Portrait PDF
            </a>

            @if(in_array(auth()->user()->role, ['dean', 'super_admin']) && $summary->status === 'pending_noting')
                <button type="button" class="btn btn-success rounded-pill px-3 py-2 small fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#noteModal">
                    <i class="bi bi-check-circle-fill me-1"></i> Note This Summary Form
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
            
            <!-- Top Right Page Indicator -->
            <div class="text-end text-muted small mb-3" style="font-size: 0.8rem;">
                Page | 1 of 1
            </div>

            <!-- Institutional Header -->
            <div class="text-center mb-4">
                <img src="{{ asset('logo.png') }}" alt="University of Antique Seal" style="width: 75px; height: 75px; object-fit: contain;" class="mb-2">
                <div class="small text-muted mb-0">Republic of the Philippines</div>
                <div class="fw-bold text-uppercase fs-5" style="letter-spacing: 0.5px;">UNIVERSITY OF ANTIQUE</div>
                <div class="small text-muted">Sibalom, Antique</div>
            </div>

            <!-- Document Title & College Subheader -->
            <div class="text-center my-4">
                <h5 class="fw-bold text-uppercase text-dark mb-2" style="font-size: 1.05rem; letter-spacing: 0.3px;">
                    COMPLETED RESEARCHES SUMMARY FORM
                </h5>
                <div class="fw-bold text-dark mt-1" style="font-size: 0.95rem;">
                    College: <span class="text-decoration-underline">{{ $summary->college }}</span>
                </div>
            </div>

            <!-- Table (7 Columns matching UA template) -->
            <div class="table-responsive my-4">
                <table class="table table-bordered border-dark align-middle mb-0" style="border: 1px solid #000; font-size: 0.85rem;">
                    <thead class="bg-light text-center fw-bold">
                        <tr>
                            <th style="width: 28%; border: 1px solid #000; vertical-align: middle;">Project Title</th>
                            <th style="width: 20%; border: 1px solid #000; vertical-align: middle;">Proponent/s</th>
                            <th style="width: 11%; border: 1px solid #000; vertical-align: middle;">Duration</th>
                            <th style="width: 11%; border: 1px solid #000; vertical-align: middle;">Date Started</th>
                            <th style="width: 11%; border: 1px solid #000; vertical-align: middle;">Date Completed</th>
                            <th style="width: 10%; border: 1px solid #000; vertical-align: middle;">Budget</th>
                            <th style="width: 10%; border: 1px solid #000; vertical-align: middle;">Expenditure</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $itemIndex = 1;
                        @endphp
                        @forelse($items as $row)
                            <tr>
                                <td style="border: 1px solid #000; padding: 7px 8px;">
                                    <strong>{{ $itemIndex }}.</strong> {{ $row['project_title'] ?? 'N/A' }}
                                </td>
                                <td style="border: 1px solid #000; padding: 7px 8px;">
                                    {{ $row['proponents'] ?? 'N/A' }}
                                </td>
                                <td class="text-center" style="border: 1px solid #000; padding: 7px 6px;">
                                    {{ $row['duration'] ?? '12 Months' }}
                                </td>
                                <td class="text-center" style="border: 1px solid #000; padding: 7px 6px;">
                                    {{ $row['date_started'] ?? 'N/A' }}
                                </td>
                                <td class="text-center" style="border: 1px solid #000; padding: 7px 6px;">
                                    {{ $row['date_completed'] ?? 'N/A' }}
                                </td>
                                <td class="text-end" style="border: 1px solid #000; padding: 7px 6px;">
                                    {{ $row['budget'] ?? '0.00' }}
                                </td>
                                <td class="text-end" style="border: 1px solid #000; padding: 7px 6px;">
                                    {{ $row['expenditure'] ?? '0.00' }}
                                </td>
                            </tr>
                            @php
                                $itemIndex++;
                            @endphp
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted p-4" style="border: 1px solid #000;">
                                    No completed research projects recorded in this summary form.
                                </td>
                            </tr>
                        @endforelse

                        {{-- Additional blank rows if less than 10 items to match official paper layout --}}
                        @for ($i = $itemIndex; $i <= 10; $i++)
                            <tr>
                                <td style="border: 1px solid #000; height: 35px; padding: 7px 8px;">{{ $i }}.&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 7px 8px;">&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 7px 6px;">&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 7px 6px;">&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 7px 6px;">&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 7px 6px;">&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 7px 6px;">&nbsp;</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <!-- Signatures Section (Prepared by Coordinator, Noted by Dean matching template) -->
            <div class="row mt-5 pt-3">
                <div class="col-12 col-sm-6 mb-4 mb-sm-0">
                    <div class="small text-muted mb-4">Prepared by:</div>
                    <div class="d-inline-block">
                        <div class="fw-bold text-uppercase border-bottom border-dark pb-1" style="min-width: 260px;">
                            {{ $summary->preparer?->name ?? 'Printed Name & Signature' }}
                        </div>
                        <div class="small text-muted" style="font-size: 0.72rem;">Printed Name & Signature</div>
                        <div class="small text-dark mt-1">College Research Coordinator</div>
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <div class="small text-muted mb-4">Noted by:</div>
                    <div class="d-inline-block">
                        <div class="fw-bold text-uppercase border-bottom border-dark pb-1" style="min-width: 260px;">
                            @if($summary->status === 'noted' && $summary->noter)
                                {{ $summary->noter->name }}
                            @else
                                <span class="text-muted fw-normal">Printed Name & Signature</span>
                            @endif
                        </div>
                        <div class="small text-muted" style="font-size: 0.72rem;">Printed Name & Signature</div>
                        <div class="small text-dark mt-1">Dean, {{ $summary->college }}</div>
                        @if($summary->status === 'noted' && $summary->noted_at)
                            <div class="text-success small mt-1" style="font-size: 0.75rem;">
                                <i class="bi bi-patch-check-fill me-1"></i> Formally Noted on {{ $summary->noted_at->format('F d, Y h:i A') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($summary->remarks)
                <div class="mt-4 pt-3 border-top">
                    <div class="small text-muted fw-bold">Remarks / Internal Notes:</div>
                    <div class="small text-secondary">{{ $summary->remarks }}</div>
                </div>
            @endif

        </div>
    </div>

    <!-- College Dean Noting Confirmation Modal -->
    @if(in_array(auth()->user()->role, ['dean', 'super_admin']) && $summary->status === 'pending_noting')
        <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-0 bg-success bg-opacity-10 p-4 pb-2">
                        <h5 class="modal-title fw-bold text-success d-flex align-items-center gap-2" id="noteModalLabel">
                            <i class="bi bi-check2-circle fs-4"></i> Note Completed Researches Summary Form
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('completed_researches_summary.note', $summary->id) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <p class="text-muted small mb-3">
                                As the <strong>College Dean</strong> of <strong>{{ $summary->college }}</strong>, your formal endorsement registers that you have reviewed this summary form containing <strong>{{ count($summary->proposal_ids ?? []) }} completed research project(s)</strong> prepared by your College Research Coordinator.
                            </p>
                            <div class="p-3 bg-light rounded-3 small">
                                <div><strong>College:</strong> {{ $summary->college }}</div>
                                <div><strong>Prepared by:</strong> {{ $summary->preparer?->name ?? 'College Research Coordinator' }}</div>
                                <div><strong>Date of Noting:</strong> {{ date('F d, Y') }}</div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 small" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success rounded-pill px-4 py-2 small fw-bold">
                                <i class="bi bi-pen-fill me-1"></i> Confirm & Officially Note
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
