<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RESU-FM-003 - College Research Proposal Endorsement Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f8f9fa;
            color: #000;
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
        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .form-table th, .form-table td {
            border: 1.5px solid #000;
            padding: 10px 14px;
            vertical-align: top;
        }
        .signature-line {
            border-bottom: 1.5px solid #000;
            width: 320px;
            display: inline-block;
            margin-top: 40px;
        }
        .no-print {
            max-width: 850px;
            margin: 20px auto 0 auto;
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

    <!-- Action Bar (Print / Download) -->
    <div class="no-print d-flex justify-content-between align-items-center mb-3">
        <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-4 btn-sm">
            &larr; Back to System
        </a>
        <div>
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 btn-sm shadow-sm">
                🖨️ Print Form / Save as PDF
            </button>
        </div>
    </div>

    <!-- Official Document Page -->
    <div class="page-container">
        <!-- Top right page number -->
        <div class="text-end text-muted small mb-2" style="font-size: 0.8rem;">
            Page | <strong>1</strong>
        </div>

        <!-- Document Header -->
        <div class="text-center mb-4">
            <img src="{{ asset('logo.png') }}" alt="University of Antique Logo" class="header-logo mb-2">
            <div style="font-size: 0.95rem;">Republic of the Philippines</div>
            <div class="fw-bold fs-5 text-uppercase">UNIVERSITY OF ANTIQUE</div>
            <div style="font-size: 0.9rem;">Sibalom, Antique</div>
            
            <div class="fw-bold mt-3 fs-6 text-uppercase" style="letter-spacing: 0.5px;">
                COLLEGE RESEARCH PROPOSAL ENDORSEMENT FORM
            </div>
            <div class="fw-bold fs-6">
                College of <u>{{ strtoupper($collegeName ?? 'Computer Studies') }}</u>
            </div>
        </div>

        <!-- Form Table -->
        <table class="form-table">
            <thead>
                <tr>
                    <th style="width: 45%;" class="fw-bold">A. Project Title</th>
                    <th style="width: 55%;" class="fw-bold">Proponent/s</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proposals as $index => $prop)
                    <tr>
                        <td>
                            <strong>{{ $index + 1 }}.</strong> {{ $prop->title }}
                            <div class="text-muted small mt-1" style="font-size: 0.8rem;">
                                Proposal Code: {{ $prop->proposal_code ?? 'UA-RP-'.$prop->id }}
                            </div>
                        </td>
                        <td>
                            <strong>{{ $prop->user->name ?? 'Lead Researcher' }}</strong>
                            @if($prop->collaborators && $prop->collaborators->count() > 0)
                                <br><span class="small text-dark">Co-proponents: {{ $prop->collaborators->pluck('name')->implode(', ') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td><strong>1.</strong> {{ $singleProposal->title ?? 'Research Proposal Title' }}</td>
                        <td>
                            <strong>{{ $singleProposal->user->name ?? 'Lead Researcher' }}</strong>
                            @if(isset($singleProposal) && $singleProposal->collaborators && $singleProposal->collaborators->count() > 0)
                                <br><span class="small text-dark">Co-proponents: {{ $singleProposal->collaborators->pluck('name')->implode(', ') }}</span>
                            @endif
                        </td>
                    </tr>
                @endforelse

                <!-- Row B: Attachment -->
                <tr>
                    <td class="fw-bold">
                        B. Attachment:<br>
                        &nbsp;&nbsp;&nbsp;&nbsp;Research Proposal Write-up
                    </td>
                    <td class="align-middle">
                        <span style="font-size: 1.1rem;">[ &#x2611; ]</span> Complete Research Proposal Write-up Attached
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Signatures Section -->
        <div class="mt-5">
            <div class="mb-5">
                <div>Prepared by:</div>
                <div class="signature-line"></div>
                <div class="fw-bold mt-1">{{ $coordinatorName ?? 'College Research Coordinator' }}</div>
                <div>College Research Coordinator</div>
                <div class="text-muted small" style="font-size: 0.8rem;">Date: {{ now()->format('F d, Y') }}</div>
            </div>

            <div>
                <div>Noted by:</div>
                <div class="signature-line"></div>
                <div class="fw-bold mt-1">{{ $deanName ?? 'College Dean' }}</div>
                <div>Dean</div>
                <div class="text-muted small" style="font-size: 0.8rem;">Date: {{ now()->format('F d, Y') }}</div>
            </div>
        </div>

        <!-- Document Footer -->
        <div class="d-flex justify-content-between mt-5 pt-4 border-top text-muted small" style="font-size: 0.75rem;">
            <div>RESU-FM-003</div>
            <div>Rev.2/01-26-24</div>
        </div>
    </div>

</body>
</html>
