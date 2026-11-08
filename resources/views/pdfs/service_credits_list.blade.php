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
        .inst-unit {
            font-size: 12px;
            font-weight: bold;
            margin-top: 5px;
        }
        .inst-email {
            font-size: 10px;
            color: #0d6efd;
            text-decoration: underline;
        }
        .doc-title-block {
            text-align: center;
            margin: 18px 0 14px 0;
        }
        .doc-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 4px;
            letter-spacing: 0.2px;
        }
        table.research-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 8px;
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
        .col-no {
            width: 6%;
            text-align: center;
        }
        .col-researchers {
            width: 25%;
            text-align: left;
        }
        .col-college {
            width: 14%;
            text-align: left;
        }
        .col-title {
            width: 29%;
            text-align: left;
        }
        .col-date {
            width: 13%;
            text-align: center;
        }
        .col-credits {
            width: 13%;
            text-align: center;
        }
        .empty-row td {
            height: 24px;
        }
        .attachments-note {
            font-size: 10px;
            font-style: italic;
            margin-bottom: 35px;
        }
        .signatures {
            margin-top: 30px;
            page-break-inside: avoid;
            width: 100%;
        }
        .sig-group {
            margin-bottom: 28px;
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
        <div class="inst-unit">Research Unit</div>
        <div class="inst-email">research@antiquespride.edu.ph</div>
    </div>

    <div class="doc-title-block">
        <div class="doc-title">List of Researchers Qualified to Avail Service Credits</div>
    </div>

    <table class="research-table">
        <thead>
            <tr>
                <th class="col-no">No.</th>
                <th class="col-researchers">Name of Researcher/s</th>
                <th class="col-college">College</th>
                <th class="col-title">Title of Research</th>
                <th class="col-date">Date Completed</th>
                <th class="col-credits">Service Credits Granted</th>
            </tr>
        </thead>
        <tbody>
            @php
                $itemIndex = 1;
            @endphp
            @forelse($items as $row)
                <tr>
                    <td class="col-no">
                        <strong>{{ $row['no'] ?? $itemIndex }}.</strong>
                    </td>
                    <td class="col-researchers">
                        {{ $row['researchers'] ?? 'N/A' }}
                    </td>
                    <td class="col-college">
                        {{ $row['college'] ?? 'N/A' }}
                    </td>
                    <td class="col-title">
                        {{ $row['title'] ?? 'N/A' }}
                    </td>
                    <td class="col-date">
                        {{ $row['date_completed'] ?? 'N/A' }}
                    </td>
                    <td class="col-credits">
                        {{ $row['service_credits'] ?? '15 Days' }}
                    </td>
                </tr>
                @php
                    $itemIndex++;
                @endphp
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #777; padding: 15px;">
                        No researchers recorded.
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
                    <td>&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="attachments-note">
        Attachments: Terminal Report (including Full Manuscript), and Certificate of Completion
    </div>

    <div class="signatures">
        <div class="sig-group">
            <div class="sig-label">Prepared by:</div>
            <div class="sig-name">
                {{ $list->preparer?->name ?? '____________________________________' }}
            </div>
            <div class="sig-title">Director, Research Unit</div>
        </div>

        <div class="sig-group">
            <div class="sig-label">Approved by:</div>
            <div class="sig-name">
                @if($list->status === 'approved' && $list->approver)
                    {{ $list->approver->name }}
                @else
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                @endif
            </div>
            <div class="sig-title">VP for Research, Extension, and Innovation</div>
            @if($list->status === 'approved' && $list->approved_at)
                <div class="sig-date">Date Approved: {{ $list->approved_at->format('F d, Y') }}</div>
            @endif
        </div>
    </div>
</body>
</html>
