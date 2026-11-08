<x-app-layout>
    <!-- Action Bar -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('presented_researches.index') }}" class="text-decoration-none">Presented Dossiers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dossier #{{ str_pad($list->id, 5, '0', STR_PAD_LEFT) }}</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h1 class="h4 h-md-3 fw-bold text-dark mb-0">{{ $list->title }}</h1>
                @if($list->status === 'noted')
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 rounded-pill fw-bold">
                        <i class="bi bi-check2-all me-1"></i> Officially Noted by Director
                    </span>
                @else
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2.5 py-1 rounded-pill fw-bold">
                        <i class="bi bi-clock-history me-1"></i> Awaiting Director Noting
                    </span>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('presented_researches.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Back to Dossiers
            </a>

            <a href="{{ route('presented_researches.pdf', $list->id) }}" class="btn btn-primary rounded-pill px-3 py-2 small fw-bold shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download Official Landscape PDF
            </a>

            @if(in_array(auth()->user()->role, ['admin', 'super_admin']) && $list->status === 'pending_noting')
                <button type="button" class="btn btn-success rounded-pill px-3 py-2 small fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#noteModal">
                    <i class="bi bi-check-circle-fill me-1"></i> Note This List
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

    <!-- Document Paper Preview (Landscape Proportion) -->
    <div class="card border-0 shadow-sm rounded-4 mx-auto mb-5" style="max-width: 1050px;">
        <div class="card-body p-4 p-md-5 bg-white text-dark" style="font-family: Arial, sans-serif;">
            
            <!-- Top Right Page Indicator -->
            <div class="text-end text-muted small mb-3" style="font-size: 0.8rem;">
                Page | 1
            </div>

            <!-- Institutional Header -->
            <div class="text-center mb-4">
                <img src="{{ asset('logo.png') }}" alt="University of Antique Seal" style="width: 75px; height: 75px; object-fit: contain;" class="mb-2">
                <div class="small text-muted mb-0">Republic of the Philippines</div>
                <div class="fw-bold text-uppercase fs-5" style="letter-spacing: 0.5px;">UNIVERSITY OF ANTIQUE</div>
                <div class="small text-muted">Sibalom, Antique</div>
            </div>

            <!-- Document Title -->
            <div class="text-center my-4">
                <h5 class="fw-bold text-uppercase text-decoration-underline mb-1 text-dark">LIST OF PRESENTED RESEARCHES</h5>
                <div class="text-dark small">
                    <strong>Date:</strong> {{ $list->report_date ? $list->report_date->format('F d, Y') : date('F d, Y') }}
                </div>
            </div>

            <!-- Research Table (6 Columns matching Landscape template) -->
            <div class="table-responsive my-4">
                <table class="table table-bordered border-dark align-middle mb-0" style="border: 1px solid #000; font-size: 0.82rem;">
                    <thead class="bg-light text-center fw-bold">
                        <tr>
                            <th style="width: 25%; border: 1px solid #000;">Research Title</th>
                            <th style="width: 18%; border: 1px solid #000;">Author/s</th>
                            <th style="width: 11%; border: 1px solid #000;">Date Presented</th>
                            <th style="width: 19%; border: 1px solid #000;">Title of Conference</th>
                            <th style="width: 14%; border: 1px solid #000;">Venue of Conference</th>
                            <th style="width: 13%; border: 1px solid #000;">Type of conference<br><small style="font-weight: normal;">(Local, Regional, International)</small></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $itemIndex = 1;
                        @endphp
                        @forelse($items as $row)
                            <tr>
                                <td style="border: 1px solid #000; padding: 7px 8px;">
                                    <strong>{{ $itemIndex }}.</strong> {{ $row['title'] ?? 'N/A' }}
                                </td>
                                <td style="border: 1px solid #000; padding: 7px 8px;">
                                    {{ $row['authors'] ?? 'N/A' }}
                                </td>
                                <td class="text-center" style="border: 1px solid #000; padding: 7px 8px;">
                                    {{ $row['date_presented'] ?? 'N/A' }}
                                </td>
                                <td style="border: 1px solid #000; padding: 7px 8px;">
                                    {{ $row['conference_title'] ?? 'University Research Colloquium' }}
                                </td>
                                <td style="border: 1px solid #000; padding: 7px 8px;">
                                    {{ $row['venue'] ?? 'University of Antique' }}
                                </td>
                                <td class="text-center" style="border: 1px solid #000; padding: 7px 8px;">
                                    {{ $row['conference_type'] ?? 'Regional' }}
                                </td>
                            </tr>
                            @php
                                $itemIndex++;
                            @endphp
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted p-4" style="border: 1px solid #000;">
                                    No presented research outputs recorded in this dossier.
                                </td>
                            </tr>
                        @endforelse

                        {{-- Additional blank rows if less than 6 items to match paper layout --}}
                        @for ($i = $itemIndex; $i <= 6; $i++)
                            <tr>
                                <td style="border: 1px solid #000; height: 32px; padding: 7px 8px;">{{ $i }}.&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 7px 8px;">&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 7px 8px;">&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 7px 8px;">&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 7px 8px;">&nbsp;</td>
                                <td style="border: 1px solid #000; padding: 7px 8px;">&nbsp;</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <!-- Signatures Section (Prepared by Staff, Noted by Director matching template) -->
            <div class="row mt-5 pt-3">
                <div class="col-12 col-md-6 mb-4 mb-md-0">
                    <div class="small text-muted mb-4">Prepared by:</div>
                    <div class="d-inline-block">
                        <div class="fw-bold text-uppercase border-bottom border-dark pb-1" style="min-width: 250px;">
                            {{ $list->preparer?->name ?? '____________________________________' }}
                        </div>
                        <div class="small text-dark mt-1">Staff, Research Unit</div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="small text-muted mb-4">Noted:</div>
                    <div class="d-inline-block">
                        <div class="fw-bold text-uppercase border-bottom border-dark pb-1" style="min-width: 250px;">
                            @if($list->status === 'noted' && $list->noter)
                                {{ $list->noter->name }}
                            @else
                                <span class="text-muted fst-italic fw-normal">____________________________________</span>
                            @endif
                        </div>
                        <div class="small text-dark mt-1">Director, Research Unit</div>
                        @if($list->status === 'noted' && $list->noted_at)
                            <div class="text-success small mt-1" style="font-size: 0.75rem;">
                                <i class="bi bi-patch-check-fill me-1"></i> Formally Noted on {{ $list->noted_at->format('F d, Y h:i A') }}
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

    <!-- Director Noting Confirmation Modal -->
    @if(in_array(auth()->user()->role, ['admin', 'super_admin']) && $list->status === 'pending_noting')
        <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-0 bg-success bg-opacity-10 p-4 pb-2">
                        <h5 class="modal-title fw-bold text-success d-flex align-items-center gap-2" id="noteModalLabel">
                            <i class="bi bi-check2-circle fs-4"></i> Formally Note Presented Researches
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('presented_researches.note', $list->id) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <p class="text-muted small mb-3">
                                As the <strong>Director of the Research Unit</strong>, your digital sign-off will officially note this dossier containing <strong>{{ count($list->proposal_ids ?? []) }} presented research paper(s)</strong> compiled by the Receiving Staff.
                            </p>
                            <div class="p-3 bg-light rounded-3 small">
                                <div><strong>Dossier:</strong> {{ $list->title }}</div>
                                <div><strong>Prepared by:</strong> {{ $list->preparer?->name ?? 'Staff, Research Unit' }}</div>
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
