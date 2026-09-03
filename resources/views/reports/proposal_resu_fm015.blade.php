<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RESU-FM-015 - Research Proposal Template</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f8f9fa;
            color: #000;
            line-height: 1.5;
        }
        .page-container {
            max-width: 850px;
            margin: 30px auto;
            background: #fff;
            padding: 45px 55px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border: 1px solid #ccc;
            position: relative;
        }
        .header-logo {
            width: 85px;
            height: auto;
        }
        .field-row {
            margin-bottom: 12px;
            display: flex;
        }
        .field-label {
            width: 240px;
            font-weight: bold;
            flex-shrink: 0;
        }
        .field-colon {
            width: 20px;
            flex-shrink: 0;
        }
        .field-content {
            flex-grow: 1;
        }
        .table-6ps, .lib-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .table-6ps th, .table-6ps td, .lib-table th, .lib-table td {
            border: 1px solid #000;
            padding: 6px 10px;
            font-size: 0.9rem;
        }
        .signature-line {
            border-bottom: 1.5px solid #000;
            width: 300px;
            display: inline-block;
            margin-top: 40px;
        }
        .no-print {
            max-width: 850px;
            margin: 20px auto 0 auto;
        }
        .page-break {
            page-break-before: always;
            margin-top: 40px;
        }
        @media print {
            body {
                background: #fff;
            }
            .page-container {
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0 auto;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar -->
    <div class="no-print d-flex justify-content-between align-items-center mb-3">
        <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
            &larr; Back to System
        </a>
        <div>
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 btn-sm shadow-sm">
                🖨️ Print Proposal (RESU-FM-015) / Save as PDF
            </button>
        </div>
    </div>

    <!-- PAGE 1 -->
    <div class="page-container">
        <!-- Top Right Page Number -->
        <div class="text-end text-muted small mb-2" style="font-size: 0.8rem;">
            Page | <strong>1</strong>
        </div>

        <!-- Header -->
        <div class="text-center mb-4">
            <img src="{{ asset('logo.png') }}" alt="University of Antique Logo" class="header-logo mb-2">
            <div style="font-size: 0.95rem;">Republic of the Philippines</div>
            <div class="fw-bold fs-5 text-uppercase">UNIVERSITY OF ANTIQUE</div>
            <div style="font-size: 0.9rem;">Sibalom, Antique</div>
            
            <div class="fw-bold mt-3 fs-6 text-uppercase" style="letter-spacing: 0.5px;">
                RESEARCH PROPOSAL TEMPLATE
            </div>
        </div>

        <!-- Proposal Metadata -->
        <div class="field-row">
            <div class="field-label">Title</div>
            <div class="field-colon">:</div>
            <div class="field-content fw-bold">{{ $proposal->title }}</div>
        </div>

        <div class="field-row">
            <div class="field-label">Name of Proponent/s</div>
            <div class="field-colon">:</div>
            <div class="field-content">
                <strong>{{ $proposal->user->name }}</strong> (Lead Researcher)
                @if($proposal->collaborators && $proposal->collaborators->count() > 0)
                    , {{ $proposal->collaborators->pluck('name')->implode(', ') }}
                @endif
            </div>
        </div>

        <div class="field-row">
            <div class="field-label">Credentials of Key Proponents</div>
            <div class="field-colon">:</div>
            <div class="field-content">{{ $proposal->proponent_credentials ?? 'Faculty Researcher, University of Antique' }}</div>
        </div>

        <div class="field-row">
            <div class="field-label">College Affiliation</div>
            <div class="field-colon">:</div>
            <div class="field-content">{{ $proposal->user->department ?? 'College of Computer Studies' }}</div>
        </div>

        <div class="field-row">
            <div class="field-label">SDGs and Thrusts Addressed</div>
            <div class="field-colon">:</div>
            <div class="field-content">{{ $proposal->sdgs_addressed ?? 'SDG 4 (Quality Education), SDG 9 (Industry, Innovation & Infrastructure)' }}</div>
        </div>

        <div class="field-row">
            <div class="field-label">Background of the Study</div>
            <div class="field-colon">:</div>
            <div class="field-content text-justify">
                {{ $proposal->abstract }}
                @if($proposal->rationale)
                    <br><br><strong>Rationale & Framework:</strong> {{ $proposal->rationale }}
                @endif
            </div>
        </div>

        <div class="field-row">
            <div class="field-label">Significance of the Study</div>
            <div class="field-colon">:</div>
            <div class="field-content">{{ $proposal->significance ?? 'This research provides baseline framework and institutional technology solutions for SUC research governance.' }}</div>
        </div>

        <div class="field-row">
            <div class="field-label">Definition of Terms</div>
            <div class="field-colon">:</div>
            <div class="field-content">{{ $proposal->definition_of_terms ?? 'URLC: University Research Lifecycle Center; SUC: State Universities and Colleges.' }}</div>
        </div>

        <div class="field-row">
            <div class="field-label">Review of Related Literature</div>
            <div class="field-colon">:</div>
            <div class="field-content">{{ $proposal->literature_review ?? 'Literature emphasizes automated workflow systems and institutional grant monitoring.' }}</div>
        </div>

        <div class="field-row">
            <div class="field-label">Methodology</div>
            <div class="field-colon">:</div>
            <div class="field-content">{{ $proposal->methodology ?? 'Agile Development Methodology, System Usability Scale (SUS), and ISO 25010 Software Quality Assessment.' }}</div>
        </div>

        <div class="field-row">
            <div class="field-label">Expected Outputs (6Ps)</div>
            <div class="field-colon">:</div>
            <div class="field-content"></div>
        </div>

        <!-- 6Ps Table -->
        <table class="table-6ps">
            <tbody>
                <tr>
                    <td style="width: 35%;" class="fw-bold">Publication</td>
                    <td>{{ $proposal->output_publication ?? 'Targeted for publication in Scopus / CHED Accredited Journal.' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">Patents / Intellectual Property</td>
                    <td>{{ $proposal->output_ip ?? 'National Utility Model / Copyright Registration with IPOPHL.' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">Product / Processes</td>
                    <td>{{ $proposal->output_product ?? 'Digital Research Lifecycle Platform.' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">People / Services</td>
                    <td>{{ $proposal->output_people ?? 'Faculty Researchers, College Research Coordinators, University Administrators.' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">Places and Partnerships</td>
                    <td>{{ $proposal->output_places ?? 'University of Antique and Partner SUC Institutions.' }}</td>
                </tr>
                <tr>
                    <td class="fw-bold">Policy</td>
                    <td>{{ $proposal->output_policy ?? 'Institutional Guidelines for University Research Automation.' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="field-row">
            <div class="field-label">References</div>
            <div class="field-colon">:</div>
            <div class="field-content">{{ $proposal->references ?? 'APA 7th Edition Academic References.' }}</div>
        </div>

        <!-- Signature -->
        <div class="mt-5">
            <div>Prepared by:</div>
            <div class="signature-line"></div>
            <div class="fw-bold mt-1">{{ $proposal->user->name }}</div>
            <div>Printed Name & Signature Proponent/s</div>
            <div class="text-muted small" style="font-size: 0.8rem;">Date: {{ $proposal->created_at->format('F d, Y') }}</div>
        </div>

        <!-- Footer -->
        <div class="d-flex justify-content-between mt-5 pt-3 border-top text-muted small" style="font-size: 0.75rem;">
            <div>RESU-FM-015</div>
            <div>Rev.5/06-08-26</div>
        </div>

        <!-- PAGE BREAK FOR PAGE 2 -->
        <div class="page-break"></div>

        <!-- PAGE 2 -->
        <div class="text-end text-muted small mb-2" style="font-size: 0.8rem;">
            Page | <strong>2</strong>
        </div>

        <!-- Work Plan (Gantt Chart) -->
        <div class="fw-bold fs-6 mb-2">Work Plan (Gantt Chart):</div>
        <table class="table-6ps mb-4">
            <thead>
                <tr class="bg-light text-center">
                    <th style="width: 40%;">Activity / Milestone Phase</th>
                    <th style="width: 30%;">Target Timeline</th>
                    <th style="width: 30%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proposal->milestones as $m)
                    <tr>
                        <td class="fw-semibold">{{ $m->title }}</td>
                        <td class="text-center">{{ $m->target_date ? $m->target_date->format('M d, Y') : 'Phase '.$m->phase }}</td>
                        <td class="text-center">{{ ucfirst($m->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td>Phase 1: Proposal Endorsement & Approval</td>
                        <td class="text-center">Months 1 - 2</td>
                        <td class="text-center">Completed</td>
                    </tr>
                    <tr>
                        <td>Phase 2: Budget Certification & In-House Review</td>
                        <td class="text-center">Months 3 - 4</td>
                        <td class="text-center">Ongoing</td>
                    </tr>
                    <tr>
                        <td>Phase 3: Implementation & Paper Presentation</td>
                        <td class="text-center">Months 5 - 8</td>
                        <td class="text-center">Scheduled</td>
                    </tr>
                    <tr>
                        <td>Phase 4 & 5: Publication & Local Research Forum</td>
                        <td class="text-center">Months 9 - 11</td>
                        <td class="text-center">Scheduled</td>
                    </tr>
                    <tr>
                        <td>Phase 6: Terminal Report & Final Manuscript</td>
                        <td class="text-center">Month 12</td>
                        <td class="text-center">Scheduled</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Line Item Budget -->
        <div class="fw-bold fs-6 mb-2">Line Item Budget:</div>
        @php
            $mooeItems = $proposal->budgetItems->where('category_type', 'mooe');
            $coItems = $proposal->budgetItems->where('category_type', 'co');
            $subtotalMOOE = $mooeItems->sum('amount');
            $subtotalCO = $coItems->sum('amount');
            $grandTotal = $subtotalMOOE + $subtotalCO;
            if ($grandTotal == 0) {
                $grandTotal = $proposal->total_budget ?? 0;
            }
        @endphp

        <table class="lib-table">
            <thead>
                <tr class="text-center align-middle bg-light">
                    <th rowspan="2" style="width: 250px;">Details</th>
                    <th rowspan="2" style="width: 110px;">Funding Agency or Organization</th>
                    <th colspan="3">University of Antique</th>
                    <th rowspan="2" style="width: 100px;">Total</th>
                </tr>
                <tr class="text-center align-middle bg-light">
                    <th style="width: 90px;">Equivalent Teaching Unit</th>
                    <th style="width: 90px;">Existing Resources</th>
                    <th style="width: 110px;">Proposed Expenditures</th>
                </tr>
            </thead>
            <tbody>
                <!-- Section I: MOOE -->
                <tr class="fw-bold bg-light">
                    <td colspan="6">I. Maintenance and Other Operating Expenses (MOOE)</td>
                </tr>
                @forelse($mooeItems as $item)
                    <tr>
                        <td>{{ $item->item_description ?? $item->item_name }}</td>
                        <td class="text-center">{{ $item->funding_source ?? 'RESU' }}</td>
                        <td></td>
                        <td></td>
                        <td class="text-end">₱{{ number_format($item->amount, 2) }}</td>
                        <td class="text-end fw-bold">₱{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td>A. Supplies and Materials Expenses (Bookpaper, Printer Ink)</td>
                        <td class="text-center">RESU</td>
                        <td></td>
                        <td></td>
                        <td class="text-end">₱1,260.00</td>
                        <td class="text-end fw-bold">₱1,260.00</td>
                    </tr>
                    <tr>
                        <td>C. Travelling Expenses (Local Data Gathering)</td>
                        <td class="text-center">RESU</td>
                        <td></td>
                        <td></td>
                        <td class="text-end">₱20,000.00</td>
                        <td class="text-end fw-bold">₱20,000.00</td>
                    </tr>
                    <tr>
                        <td>E. Other Professional Services (Statistician & Grammarian)</td>
                        <td class="text-center">RESU</td>
                        <td></td>
                        <td></td>
                        <td class="text-end">₱4,500.00</td>
                        <td class="text-end fw-bold">₱4,500.00</td>
                    </tr>
                @endforelse
                <tr class="fw-bold bg-light">
                    <td colspan="4" class="text-end">Sub-total for MOOE:</td>
                    <td class="text-end">₱{{ number_format($subtotalMOOE > 0 ? $subtotalMOOE : 25760, 2) }}</td>
                    <td class="text-end">₱{{ number_format($subtotalMOOE > 0 ? $subtotalMOOE : 25760, 2) }}</td>
                </tr>

                <!-- Section II: Capital Outlay -->
                <tr class="fw-bold bg-light">
                    <td colspan="6">II. Capital Outlay (CO)</td>
                </tr>
                @forelse($coItems as $item)
                    <tr>
                        <td>{{ $item->item_description ?? $item->item_name }}</td>
                        <td class="text-center">{{ $item->funding_source ?? 'RESU' }}</td>
                        <td></td>
                        <td></td>
                        <td class="text-end">₱{{ number_format($item->amount, 2) }}</td>
                        <td class="text-end fw-bold">₱{{ number_format($item->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td>Laptop Equipment & Testing Station</td>
                        <td class="text-center">RESU</td>
                        <td></td>
                        <td></td>
                        <td class="text-end">₱52,500.00</td>
                        <td class="text-end fw-bold">₱52,500.00</td>
                    </tr>
                @endforelse
                <tr class="fw-bold bg-light">
                    <td colspan="4" class="text-end">Sub-total for CO:</td>
                    <td class="text-end">₱{{ number_format($subtotalCO > 0 ? $subtotalCO : 52500, 2) }}</td>
                    <td class="text-end">₱{{ number_format($subtotalCO > 0 ? $subtotalCO : 52500, 2) }}</td>
                </tr>

                <!-- GRAND TOTAL -->
                <tr class="fw-bold fs-6 table-secondary">
                    <td colspan="4" class="text-end text-uppercase">GRAND TOTAL:</td>
                    <td class="text-end text-success">₱{{ number_format($grandTotal > 0 ? $grandTotal : 78260, 2) }}</td>
                    <td class="text-end text-success">₱{{ number_format($grandTotal > 0 ? $grandTotal : 78260, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="fst-italic text-muted small mb-4">
            Note: Do not merge or delete the format of Line-Item Budget. If not Applicable just leave blank.
        </div>

        <!-- Footer -->
        <div class="d-flex justify-content-between mt-5 pt-3 border-top text-muted small" style="font-size: 0.75rem;">
            <div>RESU-FM-015</div>
            <div>Rev.5/06-08-26</div>
        </div>
    </div>

</body>
</html>
