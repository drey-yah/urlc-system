<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $list->title }}</title>
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
            margin-bottom: 12px;
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
            margin: 16px 0 14px 0;
        }
        .doc-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
            line-height: 1.3;
        }
        .doc-date {
            font-size: 11px;
            font-weight: normal;
        }
        table.research-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 30px;
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
            width: 35%;
            text-align: left;
        }
        .col-authors {
            width: 23%;
            text-align: left;
        }
        .col-college {
            width: 18%;
            text-align: left;
        }
        .col-budget-header {
            width: 24%;
            text-align: center;
        }
        .col-supplies {
            width: 12%;
            text-align: right;
        }
        .col-travel {
            width: 12%;
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
        .sig-group {
            margin-bottom: 30px;
        }
        .sig-label {
            font-size: 11px;
            margin-bottom: 24px;
        }
        .sig-name {
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 240px;
            padding-bottom: 2px;
        }
        .sig-title {
            font-size: 11px;
            margin-top: 4px;
        }
        .sig-date {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="page-indicator">Page | 1</div>

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
        <div class="doc-title">LIST OF RESEARCH FOR INCEPTION (REVIEW FOR FUNDING AND IMPLEMENTATION)</div>
        <div class="doc-date">Date: {{ $list->report_date ? $list->report_date->format('F d, Y') : date('F d, Y') }}</div>
    </div>

    <table class="research-table">
        <thead>
            <tr>
                <th rowspan="2" class="col-title" style="vertical-align: middle;">Research Title</th>
                <th rowspan="2" class="col-authors" style="vertical-align: middle;">Author/s</th>
                <th rowspan="2" class="col-college" style="vertical-align: middle;">College</th>
                <th colspan="2" class="col-budget-header">Line item Budget (PhP)</th>
            </tr>
            <tr>
                <th class="col-supplies" style="font-size: 9px; vertical-align: middle;">Supplies and Materials</th>
                <th class="col-travel" style="font-size: 9px; vertical-align: middle;">Travelling Expenses</th>
            </tr>
        </thead>
        <tbody>
            @php
                $itemIndex = 1;
            @endphp
            @forelse($items as $row)
                <tr>
                    <td class="col-title">
                        <strong>{{ $itemIndex }}.</strong> {{ $row['title'] ?? 'N/A' }}
                    </td>
                    <td class="col-authors">
                        {{ $row['authors'] ?? 'N/A' }}
                    </td>
                    <td class="col-college">
                        {{ $row['college'] ?? 'N/A' }}
                    </td>
                    <td class="col-supplies">
                        {{ $row['supplies_budget'] ?? '0.00' }}
                    </td>
                    <td class="col-travel">
                        {{ $row['travel_budget'] ?? '0.00' }}
                    </td>
                </tr>
                @php
                    $itemIndex++;
                @endphp
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #777; padding: 15px;">
                        No research proposals recorded.
                    </td>
                </tr>
            @endforelse

            {{-- Empty template rows to match official paper form layout --}}
            @for ($i = $itemIndex; $i <= 10; $i++)
                <tr class="empty-row">
                    <td>{{ $i }}.&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="signatures">
        <div class="sig-group">
            <div class="sig-label">Prepared by:</div>
            <div class="sig-name">
                {{ $list->preparer?->name ?? '____________________________________' }}
            </div>
            <div class="sig-title">Staff, Research Unit</div>
        </div>

        <div class="sig-group">
            <div class="sig-label">Noted:</div>
            <div class="sig-name">
                @if($list->status === 'noted' && $list->noter)
                    {{ $list->noter->name }}
                @else
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                @endif
            </div>
            <div class="sig-title">Director, Research Unit</div>
            @if($list->status === 'noted' && $list->noted_at)
                <div class="sig-date">Date Noted: {{ $list->noted_at->format('F d, Y') }}</div>
            @endif
        </div>
    </div>
</body>
</html>
