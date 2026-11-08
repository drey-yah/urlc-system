<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>List of Approved Researches - {{ $list->title }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.8in 0.8in 0.8in 0.8in;
        }
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 12px;
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
            font-size: 12px;
            margin: 1px 0;
        }
        .inst-name {
            font-size: 14px;
            font-weight: bold;
            margin: 1px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .inst-location {
            font-size: 12px;
            margin: 1px 0;
        }
        .doc-title-block {
            text-align: center;
            margin: 18px 0 16px 0;
        }
        .doc-title {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 4px;
        }
        .doc-date {
            font-size: 12px;
            font-weight: normal;
        }
        table.research-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            margin-bottom: 30px;
        }
        table.research-table th,
        table.research-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            font-size: 11px;
        }
        table.research-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f7f7f7;
        }
        .col-title {
            width: 44%;
            text-align: left;
        }
        .col-proponents {
            width: 26%;
            text-align: left;
        }
        .col-timeframe {
            width: 15%;
            text-align: center;
        }
        .col-budget {
            width: 15%;
            text-align: right;
        }
        .empty-row td {
            height: 22px;
        }
        .signatures {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .sig-group {
            margin-bottom: 28px;
        }
        .sig-label {
            font-size: 12px;
            margin-bottom: 22px;
        }
        .sig-name {
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 220px;
            padding-bottom: 2px;
        }
        .sig-title {
            font-size: 11px;
            margin-top: 3px;
        }
        .sig-date {
            font-size: 10px;
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
        <div class="doc-title">List of Approved Researches</div>
        <div class="doc-date">Date: {{ $list->report_date ? $list->report_date->format('F d, Y') : date('F d, Y') }}</div>
    </div>

    <table class="research-table">
        <thead>
            <tr>
                <th class="col-title">Research Title</th>
                <th class="col-proponents">Proponent/s</th>
                <th class="col-timeframe">Time Frame</th>
                <th class="col-budget">Budget</th>
            </tr>
        </thead>
        <tbody>
            @php
                $itemIndex = 1;
            @endphp
            @forelse($proposals as $proposal)
                @php
                    // Compute Proponents
                    $leadName = $proposal->user ? $proposal->user->name : 'N/A';
                    $collabNames = $proposal->collaborators ? $proposal->collaborators->pluck('name')->toArray() : [];
                    $allProponents = array_merge([$leadName], $collabNames);

                    // Compute Time Frame
                    $timeFrame = '12 Months';
                    if ($proposal->milestones && $proposal->milestones->count() > 0) {
                        $minDate = $proposal->milestones->min('start_date');
                        $maxDate = $proposal->milestones->max('target_date');
                        if ($minDate && $maxDate) {
                            $timeFrame = \Carbon\Carbon::parse($minDate)->format('M Y') . ' - ' . \Carbon\Carbon::parse($maxDate)->format('M Y');
                        } elseif ($maxDate) {
                            $timeFrame = 'Until ' . \Carbon\Carbon::parse($maxDate)->format('M Y');
                        }
                    } elseif ($proposal->created_at) {
                        $timeFrame = 'FY ' . $proposal->created_at->format('Y');
                    }

                    // Compute Budget
                    $budget = $proposal->total_budget ?? 0;
                    if ((!$budget || $budget == 0) && $proposal->budgetItems && $proposal->budgetItems->count() > 0) {
                        $budget = $proposal->budgetItems->sum('amount');
                    }
                @endphp
                <tr>
                    <td class="col-title">
                        <strong>{{ $itemIndex }}.</strong> {{ $proposal->title }}
                    </td>
                    <td class="col-proponents">
                        {{ implode(', ', $allProponents) }}
                    </td>
                    <td class="col-timeframe">
                        {{ $timeFrame }}
                    </td>
                    <td class="col-budget">
                        PHP {{ number_format((float)$budget, 2) }}
                    </td>
                </tr>
                @php
                    $itemIndex++;
                @endphp
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; color: #777; padding: 15px;">
                        No approved research proposals included in this dossier.
                    </td>
                </tr>
            @endforelse

            {{-- Pad table with blank rows to match the institutional template layout if rows are few --}}
            @for ($i = $itemIndex; $i <= 10; $i++)
                <tr class="empty-row">
                    <td>{{ $i }}.&nbsp;</td>
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
            <div>
                <span class="sig-name">{{ $list->preparer?->name ?? '____________________________________' }}</span>
                <div class="sig-title">Director, Research Unit</div>
            </div>
        </div>

        <div class="sig-group">
            <div class="sig-label">Noted:</div>
            <div>
                <span class="sig-name">{{ ($list->status === 'noted' && $list->noter) ? $list->noter->name : '____________________________________' }}</span>
                <div class="sig-title">VP for REI</div>
                @if($list->status === 'noted' && $list->noted_at)
                    <div class="sig-date">Officially noted on {{ $list->noted_at->format('F d, Y h:i A') }}</div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
