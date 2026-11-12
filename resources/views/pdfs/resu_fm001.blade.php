<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Criteria on Approval of Research Proposals (RESU-FM-001)</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.6in 0.75in 0.5in 0.75in;
        }
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.35;
            color: #000;
        }
        .top-page-num {
            text-align: right;
            font-size: 10px;
            color: #444;
            margin-bottom: 2px;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
        }
        .logo {
            width: 65px;
            height: 65px;
            margin-bottom: 4px;
        }
        .inst-country {
            font-size: 11px;
            margin: 1px 0;
        }
        .inst-name {
            font-size: 13px;
            font-weight: bold;
            margin: 1px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .inst-location {
            font-size: 11px;
            margin: 1px 0;
        }
        .form-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            margin: 14px 0 16px 0;
            letter-spacing: 0.2px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 11px;
        }
        .meta-label {
            width: 175px;
            font-weight: normal;
        }
        .meta-colon {
            width: 15px;
            text-align: center;
        }
        .meta-value {
            font-weight: bold;
        }

        /* Criteria Table */
        table.criteria-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.criteria-table th,
        table.criteria-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 11px;
        }
        table.criteria-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f7f7f7;
        }
        table.criteria-table th.criteria-th {
            text-align: center;
            width: 82%;
        }
        table.criteria-table th.rating-th {
            text-align: center;
            width: 18%;
        }
        table.criteria-table td.rating-val {
            text-align: center;
            font-weight: bold;
            font-size: 11.5px;
        }
        table.criteria-table tr.total-row td {
            font-weight: bold;
        }
        table.criteria-table tr.total-row td.total-label {
            text-align: center;
            letter-spacing: 1px;
        }

        .policy-note {
            font-size: 10.5px;
            font-style: italic;
            margin-top: 6px;
            margin-bottom: 16px;
        }

        /* Remarks lined section */
        .remarks-container {
            margin-top: 10px;
            margin-bottom: 25px;
        }
        .remarks-line {
            width: 100%;
            border-bottom: 1px solid #000;
            height: 22px;
            line-height: 22px;
            font-size: 10.5px;
            color: #111;
        }
        .remarks-text-block {
            font-size: 11px;
            line-height: 1.5;
            color: #111;
            padding: 4px 0;
            text-align: justify;
        }

        /* Sign-off section */
        .signoff-section {
            margin-top: 25px;
            width: 100%;
        }
        .signoff-label {
            font-size: 11px;
            margin-bottom: 30px;
        }
        .signoff-line {
            width: 260px;
            border-bottom: 1px solid #000;
            margin-bottom: 3px;
            text-align: center;
            font-weight: bold;
            font-size: 11.5px;
            padding-bottom: 2px;
        }
        .signoff-subtext {
            width: 260px;
            font-size: 10px;
            text-align: center;
            color: #333;
        }
        .signoff-date {
            width: 260px;
            font-size: 9.5px;
            text-align: center;
            color: #666;
            margin-top: 3px;
        }

        /* Footer */
        .footer-table {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            color: #333;
        }
        .footer-table td {
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="top-page-num">Page | 1 of 1</div>

    <div class="header">
        @if(!empty($logoBase64))
            <img class="logo" src="data:image/png;base64,{{ $logoBase64 }}" alt="University of Antique Seal">
        @else
            <svg class="logo" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="48" fill="#791d1d" stroke="#d97706" stroke-width="3"/>
                <text x="50" y="58" font-size="28" font-weight="bold" fill="#fff" text-anchor="middle">UA</text>
            </svg>
        @endif
        <div class="inst-country">Republic of the Philippines</div>
        <div class="inst-name">UNIVERSITY OF ANTIQUE</div>
        <div class="inst-location">Sibalom, Antique</div>
    </div>

    <div class="form-title">Criteria on Approval of Research Proposals</div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Project Proposal Title</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $proposal->title }}</td>
        </tr>
        <tr>
            <td class="meta-label">Proponent/s</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">
                {{ $proposal->user->name }} (Lead)
                @if($proposal->collaborators && $proposal->collaborators->count() > 0)
                    , {{ $proposal->collaborators->pluck('name')->implode(', ') }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="meta-label">Implementing College/Unit</td>
            <td class="meta-colon">:</td>
            <td class="meta-value">{{ $proposal->college_affiliation ?: ($proposal->user->department ?? 'General') }}</td>
        </tr>
    </table>

    <table class="criteria-table">
        <thead>
            <tr>
                <th class="criteria-th">Criteria</th>
                <th class="rating-th">Rating (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>A.</strong> Innovativeness/Novelty/Creativity (15%)</td>
                <td class="rating-val">{{ number_format($evaluation->score_a ?? 0, 2) }}%</td>
            </tr>
            <tr>
                <td><strong>B.</strong> Time Schedule and Cost (5%)</td>
                <td class="rating-val">{{ number_format($evaluation->score_b ?? 0, 2) }}%</td>
            </tr>
            <tr>
                <td><strong>C.</strong> Applicability (Results applicable for research, extension, continuing professional education and has potential IPSs) (25%)</td>
                <td class="rating-val">{{ number_format($evaluation->score_c ?? 0, 2) }}%</td>
            </tr>
            <tr>
                <td><strong>D.</strong> Relevance (Contribution to new knowledge/S &amp; T Advancement) (25%)</td>
                <td class="rating-val">{{ number_format($evaluation->score_d ?? 0, 2) }}%</td>
            </tr>
            <tr>
                <td><strong>E.</strong> Effectiveness (Responsiveness to the needs of stakeholders/beneficiaries) (30%)</td>
                <td class="rating-val">{{ number_format($evaluation->score_e ?? 0, 2) }}%</td>
            </tr>
            <tr class="total-row">
                <td class="total-label">TOTAL</td>
                <td class="rating-val" style="font-size: 12px; color: {{ ($evaluation->total_score ?? 0) >= 80 ? '#065f46' : '#991b1b' }};">
                    {{ number_format($evaluation->total_score ?? 0, 2) }}%
                </td>
            </tr>
        </tbody>
    </table>

    <div class="policy-note">
        &bull; A rating of at least 80% shall be the basis for approving R &amp; D proposals for implementation
    </div>

    <!-- Qualitative Feedback / Lined Remarks -->
    <div class="remarks-container">
        @php
            $remarksText = trim(($evaluation->comments ? "Remarks/Comments: " . $evaluation->comments : '') . ($evaluation->suggestions ? " | Suggestions: " . $evaluation->suggestions : ''));
            $linesNeeded = 8;
        @endphp

        @if(!empty($remarksText))
            <div class="remarks-text-block">
                <strong>Evaluator Remarks &amp; Recommendations:</strong><br>
                {!! nl2br(e($remarksText)) !!}
            </div>
            @php $linesNeeded = 4; @endphp
        @endif

        @for($i = 0; $i < $linesNeeded; $i++)
            <div class="remarks-line"></div>
        @endfor
    </div>

    <!-- Sign-off Block -->
    <div class="signoff-section">
        <div class="signoff-label">Evaluated by:</div>
        <div class="signoff-line">
            {{ $evaluation->reviewer->name ?? (auth()->user()->name ?? 'Assigned Reviewer') }}
        </div>
        <div class="signoff-subtext">Printed Name &amp; Signature</div>
        @if(!empty($evaluation->evaluated_at))
            <div class="signoff-date">Date: {{ \Carbon\Carbon::parse($evaluation->evaluated_at)->format('F d, Y') }}</div>
        @endif
    </div>

    <!-- Official Form Footer -->
    <table class="footer-table">
        <tr>
            <td style="text-align: left; width: 50%;">RESU-FM- 001</td>
            <td style="text-align: right; width: 50%;">Rev.2/01-26-24</td>
        </tr>
    </table>
</body>
</html>
