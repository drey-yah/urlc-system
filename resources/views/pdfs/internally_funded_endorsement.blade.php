<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Internally-Funded Research Proposal Endorsement Form</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.7in 0.75in 0.7in 0.75in;
        }
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.35;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
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
        .doc-title-block {
            text-align: center;
            margin: 14px 0 14px 0;
        }
        .doc-title {
            font-size: 13px;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .doc-meta {
            font-size: 11px;
            font-weight: normal;
            color: #333;
        }
        table.endorsement-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 25px;
        }
        table.endorsement-table th,
        table.endorsement-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
            font-size: 10.5px;
        }
        table.endorsement-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f2f2f2;
            text-transform: uppercase;
            font-size: 10px;
        }
        .col-num {
            width: 6%;
            text-align: center;
        }
        .col-title {
            width: 44%;
            text-align: left;
        }
        .col-proponents {
            width: 25%;
            text-align: left;
        }
        .col-college {
            width: 10%;
            text-align: center;
        }
        .col-budget {
            width: 15%;
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            background-color: #fafafa;
        }
        .empty-row td {
            height: 22px;
        }
        .signatures {
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .sig-group {
            margin-bottom: 22px;
        }
        .sig-label {
            font-size: 11px;
            margin-bottom: 18px;
            font-weight: normal;
        }
        .sig-name {
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200px;
            padding-bottom: 2px;
            font-size: 11px;
        }
        .sig-title {
            font-size: 10.5px;
            margin-top: 2px;
        }
        .sig-stamp {
            font-size: 9px;
            color: #059669;
            font-style: italic;
            margin-top: 2px;
        }
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .sig-table td {
            vertical-align: top;
            padding: 0;
            border: none;
        }
    </style>
</head>
<body>

    <!-- Institutional Header -->
    <div class="header">
        @if(!empty($logoBase64))
            <img class="logo" src="data:image/png;base64,{{ $logoBase64 }}" alt="University of Antique Seal">
        @else
            <svg class="logo" viewBox="0 0 100 100" style="width: 65px; height: 65px; margin-bottom: 4px;">
                <circle cx="50" cy="50" r="46" fill="#8C1D2C" stroke="#D4AF37" stroke-width="3"/>
                <circle cx="50" cy="50" r="38" fill="#fff" stroke="#D4AF37" stroke-width="1.5"/>
                <text x="50" y="57" font-family="Arial, 'Helvetica Neue', sans-serif" font-size="22" font-weight="bold" fill="#8C1D2C" text-anchor="middle">UA</text>
            </svg>
        @endif
        <div class="inst-country">Republic of the Philippines</div>
        <div class="inst-name">University of Antique</div>
        <div class="inst-location">Sibalom, Antique</div>
    </div>

    <!-- Document Title -->
    <div class="doc-title-block">
        <div class="doc-title">INTERNALLY-FUNDED RESEARCH PROPOSAL ENDORSEMENT FORM</div>
        <div class="doc-meta">
            Date: {{ $endorsement->report_date ? $endorsement->report_date->format('F d, Y') : date('F d, Y') }}
            @if($endorsement->fiscal_year)
                | Fiscal Year: {{ $endorsement->fiscal_year }}
            @endif
        </div>
    </div>

    <!-- Endorsement Table -->
    <table class="endorsement-table">
        <thead>
            <tr>
                <th class="col-num">#</th>
                <th class="col-title">Project Title</th>
                <th class="col-proponents">Proponent/s</th>
                <th class="col-college">College</th>
                <th class="col-budget">Budget (Php)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $itemsList = $items ?? ($endorsement->items ?? []);
                $itemCount = is_countable($itemsList) ? count($itemsList) : 0;
                $minRows = max(5, $itemCount);
            @endphp

            @for($i = 0; $i < $minRows; $i++)
                @if(isset($itemsList[$i]))
                    @php $item = $itemsList[$i]; @endphp
                    <tr>
                        <td class="col-num">{{ $i + 1 }}.</td>
                        <td class="col-title">
                            <strong>{{ $item['project_title'] ?? '' }}</strong>
                        </td>
                        <td class="col-proponents">{{ $item['proponents'] ?? '' }}</td>
                        <td class="col-college">{{ $item['college'] ?? 'UA' }}</td>
                        <td class="col-budget">{{ number_format((float)($item['budget'] ?? 0), 2) }}</td>
                    </tr>
                @else
                    <tr class="empty-row">
                        <td class="col-num">{{ $i + 1 }}.</td>
                        <td class="col-title"></td>
                        <td class="col-proponents"></td>
                        <td class="col-college"></td>
                        <td class="col-budget"></td>
                    </tr>
                @endif
            @endfor

            <!-- Total Row -->
            <tr class="total-row">
                <td colspan="4" style="text-align: right; padding-right: 12px; font-weight: bold;">TOTAL:</td>
                <td class="col-budget">₱{{ number_format((float)$endorsement->total_budget, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Signatories Block -->
    <div class="signatures">
        
        <!-- 1. Prepared by: Director -->
        <div class="sig-group">
            <div class="sig-label">Prepared by:</div>
            <div class="sig-name">
                {{ $endorsement->preparer->name ?? '____________________________________' }}
            </div>
            <div class="sig-title">Director</div>
            @if($endorsement->prepared_at)
                <div class="sig-stamp">Signed: {{ $endorsement->prepared_at->format('M d, Y h:i A') }}</div>
            @endif
        </div>

        <!-- 2. Recommending Approval: VPREI & VPAF -->
        <div class="sig-label" style="margin-bottom: 16px;">Recommending Approval:</div>
        <table class="sig-table">
            <tr>
                <!-- VP for REI -->
                <td style="width: 50%;">
                    <div class="sig-name">
                        @if($endorsement->isVpreiRecommended())
                            {{ $endorsement->vpreiNoter->name ?? 'VPREI' }}
                        @else
                            ____________________________________
                        @endif
                    </div>
                    <div class="sig-title">VP for REI</div>
                    @if($endorsement->isVpreiRecommended())
                        <div class="sig-stamp">Recommended: {{ $endorsement->vprei_signed_at ? $endorsement->vprei_signed_at->format('M d, Y h:i A') : '' }}</div>
                    @endif
                </td>

                <!-- VPAF -->
                <td style="width: 50%;">
                    <div class="sig-name">
                        @if($endorsement->isVpafRecommended())
                            {{ $endorsement->vpafNoter->name ?? 'VPAF' }}
                        @else
                            ____________________________________
                        @endif
                    </div>
                    <div class="sig-title">VPAF</div>
                    @if($endorsement->isVpafRecommended())
                        <div class="sig-stamp">Recommended: {{ $endorsement->vpaf_signed_at ? $endorsement->vpaf_signed_at->format('M d, Y h:i A') : '' }}</div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- 3. Approved: President -->
        <div class="sig-group" style="margin-top: 24px;">
            <div class="sig-label">Approved:</div>
            <div class="sig-name">
                @if($endorsement->isPresidentApproved())
                    {{ $endorsement->presidentApprover->name ?? 'UNIVERSITY PRESIDENT' }}
                @else
                    ____________________________________
                @endif
            </div>
            <div class="sig-title">President</div>
            @if($endorsement->isPresidentApproved())
                <div class="sig-stamp">Approved: {{ $endorsement->president_signed_at ? $endorsement->president_signed_at->format('M d, Y h:i A') : '' }}</div>
            @endif
        </div>

    </div>

</body>
</html>
