<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $summary->title }} - {{ $summary->college }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.8in 0.8in 0.8in 0.8in;
        }
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
        }
        .page-indicator {
            position: absolute;
            top: -0.4in;
            right: 0;
            font-size: 11px;
            color: #666;
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .logo {
            width: 70px;
            height: 70px;
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
        .doc-title-block {
            text-align: center;
            margin: 18px 0 16px 0;
        }
        .doc-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
            letter-spacing: 0.3px;
        }
        .doc-college {
            font-size: 12px;
            font-weight: bold;
        }
        table.research-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            margin-bottom: 35px;
        }
        table.research-table th,
        table.research-table td {
            border: 1px solid #000;
            padding: 6px 7px;
            vertical-align: top;
            font-size: 10px;
        }
        table.research-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f7f7f7;
        }
        .col-title {
            width: 28%;
            text-align: left;
        }
        .col-proponents {
            width: 20%;
            text-align: left;
        }
        .col-duration {
            width: 10%;
            text-align: center;
        }
        .col-started {
            width: 11%;
            text-align: center;
        }
        .col-completed {
            width: 11%;
            text-align: center;
        }
        .col-budget {
            width: 10%;
            text-align: right;
        }
        .col-expenditure {
            width: 10%;
            text-align: right;
        }
        .empty-row td {
            height: 24px;
        }
        .signatures {
            margin-top: 35px;
            page-break-inside: avoid;
            width: 100%;
        }
        .sig-col {
            width: 48%;
            float: left;
        }
        .sig-label {
            font-size: 11px;
            margin-bottom: 25px;
        }
        .sig-name {
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 220px;
            padding-bottom: 2px;
        }
        .sig-sub {
            font-size: 8px;
            color: #666;
            margin-top: 2px;
        }
        .sig-title {
            font-size: 11px;
            margin-top: 3px;
        }
        .sig-date {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
            font-style: italic;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <div class="page-indicator">Page | 1 of 1</div>

    <div class="header">
        @if(!empty($logoBase64))
            <img class="logo" src="data:image/png;base64,{{ $logoBase64 }}" alt="University of Antique Seal">
        @else
            <svg class="logo" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="48" fill="#800000" stroke="#ffc107" stroke-width="3" />
                <circle cx="50" cy="50" r="40" fill="none" stroke="#ffffff" stroke-dasharray="3 3" stroke-width="1" />
                <text x="50" y="58" font-family="'Times New Roman', Times, serif" font-size="28" font-weight="bold" fill="#ffffff" text-anchor="middle">UA</text>
                <text x="50" y="78" font-family="Arial, sans-serif" font-size="10" font-weight="bold" fill="#ffc107" text-anchor="middle" letter-spacing="2">RESU</text>
            </svg>
        @endif
        <div class="inst-country">Republic of the Philippines</div>
        <div class="inst-name">University of Antique</div>
        <div class="inst-location">Sibalom, Antique</div>
    </div>

    <div class="doc-title-block">
        <div class="doc-title">COMPLETED RESEARCHES SUMMARY FORM</div>
        <div class="doc-college">College: <u>{{ $summary->college }}</u></div>
    </div>

    <table class="research-table">
        <thead>
            <tr>
                <th class="col-title">Project Title</th>
                <th class="col-proponents">Proponent/s</th>
                <th class="col-duration">Duration</th>
                <th class="col-started">Date Started</th>
                <th class="col-completed">Date Completed</th>
                <th class="col-budget">Budget</th>
                <th class="col-expenditure">Expenditure</th>
            </tr>
        </thead>
        <tbody>
            @php
                $itemIndex = 1;
            @endphp
            @forelse($items as $row)
                <tr>
                    <td class="col-title">
                        <strong>{{ $itemIndex }}.</strong> {{ $row['project_title'] ?? 'N/A' }}
                    </td>
                    <td class="col-proponents">
                        {{ $row['proponents'] ?? 'N/A' }}
                    </td>
                    <td class="col-duration">
                        {{ $row['duration'] ?? '12 Months' }}
                    </td>
                    <td class="col-started">
                        {{ $row['date_started'] ?? 'N/A' }}
                    </td>
                    <td class="col-completed">
                        {{ $row['date_completed'] ?? 'N/A' }}
                    </td>
                    <td class="col-budget">
                        {{ $row['budget'] ?? '0.00' }}
                    </td>
                    <td class="col-expenditure">
                        {{ $row['expenditure'] ?? '0.00' }}
                    </td>
                </tr>
                @php
                    $itemIndex++;
                @endphp
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #777; padding: 15px;">
                        No completed research projects recorded.
                    </td>
                </tr>
            @endforelse

            {{-- Empty template rows to match official paper form layout up to 10 rows --}}
            @for ($i = $itemIndex; $i <= 10; $i++)
                <tr class="empty-row">
                    <td>{{ $i }}.&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="signatures clearfix">
        <div class="sig-col">
            <div class="sig-label">Prepared by:</div>
            <div class="sig-name">
                {{ $summary->preparer?->name ?? 'Printed Name & Signature' }}
            </div>
            <div class="sig-sub">Printed Name &amp; Signature</div>
            <div class="sig-title">College Research Coordinator</div>
        </div>

        <div class="sig-col" style="float: right;">
            <div class="sig-label">Noted by:</div>
            <div class="sig-name">
                @if($summary->status === 'noted' && $summary->noter)
                    {{ $summary->noter->name }}
                @else
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                @endif
            </div>
            <div class="sig-sub">Printed Name &amp; Signature</div>
            <div class="sig-title">Dean, College of {{ $summary->college }}</div>
            @if($summary->status === 'noted' && $summary->noted_at)
                <div class="sig-date">Date Noted: {{ $summary->noted_at->format('F d, Y') }}</div>
            @endif
        </div>
    </div>
</body>
</html>
