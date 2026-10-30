<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $fiscalYear }} Budget Allocation Per Program/College and Campus - University of Antique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            background: #fff;
        }
        .header-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .header-section h6 {
            font-size: 11px;
            margin: 0;
            font-weight: normal;
        }
        .header-section h5 {
            font-size: 13px;
            margin: 2px 0;
            font-weight: bold;
        }
        .header-section .doc-title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 15px;
            text-transform: capitalize;
        }
        table.matrix-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table.matrix-table th, table.matrix-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: middle;
        }
        table.matrix-table th {
            text-align: center;
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .college-header {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .subtotal-row {
            font-weight: bold;
            background-color: #eaeaea;
        }
        .campus-header {
            background-color: #e2e3e5;
            font-weight: bold;
            text-align: center;
        }
        .signature-block {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .sig-line {
            display: inline-block;
            border-bottom: 1px solid #000;
            width: 220px;
            margin-top: 35px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 10mm;
            }
            @page {
                size: legal portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid py-3">
        {{-- Print button toolbar --}}
        <div class="no-print d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded border">
            <div>
                <strong>Official Sheet Preview</strong> — Ready for printer / PDF save.
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-primary btn-sm px-4 fw-bold">
                    Print / Save as PDF
                </button>
                <button onclick="window.close()" class="btn btn-outline-secondary btn-sm px-3">
                    Close
                </button>
            </div>
        </div>

        {{-- Official Header --}}
        <div class="header-section">
            <h6>Republic of the Philippines</h6>
            <h5>UNIVERSITY OF ANTIQUE</h5>
            <h6>Sibalom, Antique</h6>
            <div class="doc-title">{{ $fiscalYear }} Budget Allocation Per Program/College and Campus</div>
        </div>

        {{-- Official Table --}}
        <table class="matrix-table">
            <thead>
                <tr>
                    <th rowspan="3" style="width: 32%;">PROGRAMS</th>
                    <th rowspan="3" style="width: 3%;"></th>
                    <th rowspan="3" style="width: 7%;">% Implementation</th>
                    <th rowspan="3" style="width: 7%;">% Traveling</th>
                    <th colspan="2" style="width: 18%;">Traveling-Local</th>
                    <th style="width: 10%;">foreign</th>
                    <th style="width: 11%;">implementation</th>
                    <th rowspan="3" style="width: 12%;">Total</th>
                </tr>
                <tr>
                    <th style="font-size: 9px;">GAA</th>
                    <th style="font-size: 9px;">TF</th>
                    <th style="font-size: 9px;">TF</th>
                    <th style="font-size: 9px;">TF</th>
                </tr>
                <tr>
                    <th style="font-size: 8px;">106,000.00</th>
                    <th>70,000.00</th>
                    <th>70,000.00</th>
                    <th>416,000.00</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grouped as $campusName => $collegeGroups)
                    @if($campusName !== 'Main Campus')
                        <tr class="campus-header">
                            <td colspan="9">{{ strtoupper($campusName) }}</td>
                        </tr>
                    @endif

                    @foreach($collegeGroups as $collegeName => $allocations)
                        @if($collegeName)
                            <tr class="college-header">
                                <td colspan="9">{{ strtoupper($collegeName) }}</td>
                            </tr>
                        @endif

                        @php
                            $programRows = $allocations->where('is_allotment_row', false);
                            $allotmentRow = $allocations->where('is_allotment_row', true)->first();
                        @endphp

                        @foreach($programRows as $item)
                            <tr>
                                <td>
                                    {{ $item->program_name }}
                                    @if($item->source_of_fund)
                                        <span class="fst-italic small">({{ $item->source_of_fund }})</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->sequence_no }}</td>
                                <td class="text-center">{{ $item->implementation_pct > 0 ? number_format($item->implementation_pct, 5) . '%' : '0.00000%' }}</td>
                                <td class="text-center">{{ $item->traveling_pct > 0 ? number_format($item->traveling_pct, 5) . '%' : '-' }}</td>
                                <td class="text-end">{{ $item->traveling_local_gaa > 0 ? number_format($item->traveling_local_gaa, 2) : '-' }}</td>
                                <td class="text-end">{{ $item->traveling_local_tf > 0 ? number_format($item->traveling_local_tf, 2) : '-' }}</td>
                                <td class="text-end">{{ $item->traveling_foreign_tf > 0 ? number_format($item->traveling_foreign_tf, 2) : '-' }}</td>
                                <td class="text-end">{{ $item->implementation_tf > 0 ? number_format($item->implementation_tf, 2) : '-' }}</td>
                                <td class="text-end fw-bold">{{ $item->total_allocation > 0 ? number_format($item->total_allocation, 2) : '-' }}</td>
                            </tr>
                        @endforeach

                        {{-- College Subtotal / Allotted Budget Row --}}
                        @if($allotmentRow)
                            @php
                                $rowLabel = $allotmentRow->program_name ?: ('TOTAL' . ($collegeName ? ' (' . $collegeName . ')' : ''));
                                $isGaaOnly = ($allotmentRow->traveling_local_tf == 0 && $allotmentRow->traveling_foreign_tf == 0 && $allotmentRow->implementation_tf == 0 && !empty($allotmentRow->source_of_fund));
                            @endphp
                            <tr class="subtotal-row">
                                <td class="fw-bold">{{ $rowLabel }}</td>
                                <td class="text-center fw-bold">{{ $allotmentRow->sequence_no ?? 1 }}</td>
                                <td class="text-center">{{ $allotmentRow->implementation_pct > 0 ? number_format($allotmentRow->implementation_pct, 5) . '%' : '-' }}</td>
                                <td class="text-center">{{ $allotmentRow->traveling_pct > 0 ? number_format($allotmentRow->traveling_pct, 5) . '%' : '-' }}</td>
                                <td class="text-end fw-bold">{{ $allotmentRow->traveling_local_gaa > 0 ? number_format($allotmentRow->traveling_local_gaa, 2) : '-' }}</td>
                                @if($isGaaOnly)
                                    <td colspan="3" class="text-center fst-italic" style="font-size: 9px;">({{ $allotmentRow->source_of_fund }})</td>
                                @else
                                    <td class="text-end">{{ $allotmentRow->traveling_local_tf > 0 ? number_format($allotmentRow->traveling_local_tf, 2) : '-' }}</td>
                                    <td class="text-end">{{ $allotmentRow->traveling_foreign_tf > 0 ? number_format($allotmentRow->traveling_foreign_tf, 2) : '-' }}</td>
                                    <td class="text-end">{{ $allotmentRow->implementation_tf > 0 ? number_format($allotmentRow->implementation_tf, 2) : '-' }}</td>
                                @endif
                                <td class="text-end fw-bold">{{ number_format($allotmentRow->total_allocation, 2) }}</td>
                            </tr>
                        @elseif($collegeName && $programRows->count() > 1)
                            <tr class="subtotal-row">
                                <td>TOTAL</td>
                                <td class="text-center">{{ $programRows->count() }}</td>
                                <td class="text-center">{{ number_format($programRows->sum('implementation_pct'), 5) }}%</td>
                                <td class="text-center">{{ number_format($programRows->sum('traveling_pct'), 5) }}%</td>
                                <td class="text-end">{{ number_format($programRows->sum('traveling_local_gaa'), 2) }}</td>
                                <td class="text-end">{{ number_format($programRows->sum('traveling_local_tf'), 2) }}</td>
                                <td class="text-end">{{ number_format($programRows->sum('traveling_foreign_tf'), 2) }}</td>
                                <td class="text-end">{{ number_format($programRows->sum('implementation_tf'), 2) }}</td>
                                <td class="text-end">{{ number_format($programRows->sum('total_allocation'), 2) }}</td>
                            </tr>
                        @endif
                    @endforeach

                    {{-- TF Pool Subtotal row right after Caluya Campus --}}
                    @if($campusName === 'Caluya Campus' && (empty($campus) || $campus === 'all' || $campus === 'Caluya Campus'))
                        <tr class="subtotal-row" style="background-color: #f2f2f2; font-weight: bold;">
                            <td class="fw-bold ps-2">Subtotal</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-end fw-bold">{{ number_format($tfPoolMetrics['total_gaa'], 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($tfPoolMetrics['total_local_tf'], 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($tfPoolMetrics['total_foreign_tf'], 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($tfPoolMetrics['total_implementation_tf'], 2) }}</td>
                            <td class="text-end"></td>
                        </tr>
                    @endif
                @endforeach

                {{-- Final Total Row matching official report --}}
                <tr class="subtotal-row" style="background-color: #d1d5db; font-size: 11px; font-weight: bold;">
                    <td colspan="4" class="fw-bold ps-2">Total</td>
                    <td class="text-end fw-bold">{{ number_format($metrics['total_gaa'], 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($metrics['total_local_tf'], 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($metrics['total_foreign_tf'], 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($metrics['total_implementation_tf'], 2) }}</td>
                    <td class="text-end fw-bold">{{ number_format($metrics['grand_total'], 2) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Programs Summary Block --}}
        <div class="row mt-4">
            <div class="col-6">
                <table style="width: 250px; font-size: 10px; border-collapse: collapse;">
                    <tr>
                        <td colspan="2" class="fw-bold pb-1">No. of Programs</td>
                    </tr>
                    <tr>
                        <td>Main Campus</td>
                        <td class="text-end fw-bold">53</td>
                    </tr>
                    <tr>
                        <td>Tibiao Campus</td>
                        <td class="text-end fw-bold">14</td>
                    </tr>
                    <tr>
                        <td>Hamtic Campus</td>
                        <td class="text-end fw-bold">8</td>
                    </tr>
                    <tr style="border-top: 1px solid #000;">
                        <td class="fw-bold pt-1">Total</td>
                        <td class="text-end fw-bold pt-1">75</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Official Signatures Block --}}
        <div class="row signature-block">
            <div class="col-4">
                <div>Prepared by:</div>
                <div class="sig-line"></div>
                <div class="fw-bold mt-1">GERALD N. SANTILLANA</div>
                <div class="small">Educational Research Assistant I</div>
            </div>
            <div class="col-4">
                <div style="visibility: hidden;">Prepared by:</div>
                <div class="sig-line"></div>
                <div class="fw-bold mt-1">RAZEL MAE C. CORDERO</div>
                <div class="small">University Research Associate I</div>
            </div>
            <div class="col-4">
                <div>Noted by:</div>
                <div class="sig-line"></div>
                <div class="fw-bold mt-1">DEBBIE JEAN S. RIEGO, PhD</div>
                <div class="small">Director, Research Unit</div>
            </div>
        </div>
    </div>
</body>
</html>
