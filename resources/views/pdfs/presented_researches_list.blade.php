<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>List of Presented Researches - {{ $list->title }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0.5in 0.6in 0.5in 0.6in;
        }
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.35;
            color: #000;
        }
        .page-indicator {
            position: absolute;
            top: -0.3in;
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
            width: 65px;
            height: 65px;
            margin-bottom: 3px;
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
            margin: 12px 0 12px 0;
        }
        .doc-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 3px;
        }
        .doc-date {
            font-size: 11px;
            font-weight: normal;
        }
        table.presented-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 25px;
        }
        table.presented-table th,
        table.presented-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
            font-size: 10px;
        }
        table.presented-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f7f7f7;
        }
        .col-title {
            width: 25%;
            text-align: left;
        }
        .col-authors {
            width: 18%;
            text-align: left;
        }
        .col-date {
            width: 11%;
            text-align: center;
        }
        .col-conf {
            width: 19%;
            text-align: left;
        }
        .col-venue {
            width: 14%;
            text-align: left;
        }
        .col-type {
            width: 13%;
            text-align: center;
        }
        .empty-row td {
            height: 22px;
        }
        .signatures-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
            border: none;
        }
        .signatures-table td {
            border: none;
            padding: 0;
            vertical-align: top;
            width: 50%;
        }
        .sig-label {
            font-size: 11px;
            margin-bottom: 22px;
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
            font-size: 10px;
            margin-top: 3px;
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
        <div class="doc-title">LIST OF PRESENTED RESEARCHES</div>
        <div class="doc-date">Date: {{ $list->report_date ? $list->report_date->format('F d, Y') : date('F d, Y') }}</div>
    </div>

    <table class="presented-table">
        <thead>
            <tr>
                <th class="col-title">Research Title</th>
                <th class="col-authors">Author/s</th>
                <th class="col-date">Date Presented</th>
                <th class="col-conf">Title of Conference</th>
                <th class="col-venue">Venue of Conference</th>
                <th class="col-type">Type of conference<br><small style="font-weight: normal;">(Local, Regional, International)</small></th>
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
                    <td class="col-date">
                        {{ $row['date_presented'] ?? 'N/A' }}
                    </td>
                    <td class="col-conf">
                        {{ $row['conference_title'] ?? 'University Research Colloquium' }}
                    </td>
                    <td class="col-venue">
                        {{ $row['venue'] ?? 'University of Antique' }}
                    </td>
                    <td class="col-type">
                        {{ $row['conference_type'] ?? 'Regional' }}
                    </td>
                </tr>
                @php
                    $itemIndex++;
                @endphp
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #777; padding: 15px;">
                        No presented research outputs recorded in this dossier.
                    </td>
                </tr>
            @endforelse

            {{-- Pad table with blank rows to match paper layout --}}
            @for ($i = $itemIndex; $i <= 8; $i++)
                <tr class="empty-row">
                    <td>{{ $i }}.&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <!-- Signatures side by side matching landscape layout -->
    <table class="signatures-table">
        <tr>
            <td>
                <div class="sig-label">Prepared by:</div>
                <div>
                    <span class="sig-name">{{ $list->preparer?->name ?? '____________________________________' }}</span>
                    <div class="sig-title">Staff, Research Unit</div>
                </div>
            </td>
            <td>
                <div class="sig-label">Noted:</div>
                <div>
                    <span class="sig-name">{{ ($list->status === 'noted' && $list->noter) ? $list->noter->name : '____________________________________' }}</span>
                    <div class="sig-title">Director, Research Unit</div>
                    @if($list->status === 'noted' && $list->noted_at)
                        <div class="sig-date">Officially noted on {{ $list->noted_at->format('F d, Y h:i A') }}</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
