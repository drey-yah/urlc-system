<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Accomplishment Report - {{ $report->college }} - {{ $report->quarter }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0.5in 0.6in 0.5in 0.6in;
        }
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            font-size: 9.5px;
            line-height: 1.3;
            color: #000;
        }
        .page-break {
            page-break-after: always;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
        }
        .logo {
            width: 55px;
            height: 55px;
            margin-bottom: 2px;
        }
        .inst-country {
            font-size: 10px;
            margin: 0;
        }
        .inst-name {
            font-size: 12px;
            font-weight: bold;
            margin: 1px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .inst-location {
            font-size: 10px;
            margin: 0;
        }
        .doc-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 3px;
            margin-bottom: 2px;
        }
        .doc-meta {
            font-size: 10px;
            font-weight: bold;
        }
        table.custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            margin-bottom: 12px;
        }
        table.custom-table th,
        table.custom-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: middle;
            font-size: 8.5px;
        }
        table.custom-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f7f7f7;
        }
        .section-header {
            font-weight: bold;
            font-size: 10px;
            margin-top: 6px;
            margin-bottom: 3px;
        }
        .sub-section-header {
            font-weight: bold;
            font-size: 9px;
            margin-top: 4px;
            margin-bottom: 2px;
        }
        .note-box {
            font-size: 8px;
            font-style: italic;
            margin-top: -8px;
            margin-bottom: 10px;
            color: #333;
        }
        .page-footer {
            position: fixed;
            bottom: -0.2in;
            left: 0;
            right: 0;
            font-size: 8.5px;
            color: #000;
        }
        .page-indicator-top {
            text-align: right;
            font-size: 9px;
            color: #666;
            margin-bottom: 4px;
        }
        .signatures-wrap {
            width: 100%;
            margin-top: 15px;
        }
        .sig-box {
            width: 32%;
            float: left;
        }
        .sig-label {
            font-size: 9.5px;
            margin-bottom: 18px;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            width: 90%;
            font-weight: bold;
            text-transform: uppercase;
            padding-bottom: 2px;
            min-height: 14px;
        }
        .sig-role {
            font-size: 9px;
            margin-top: 2px;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

    <!-- ================= PAGE 1 ================= -->
    <div class="page-indicator-top">Page | 1</div>

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
        <div class="doc-meta" style="margin-top: 3px;">College (<u>{{ $report->college }}</u>)</div>
        <div class="doc-title">RESEARCH ACCOMPLISHMENT REPORT</div>
        <div class="doc-meta"><u>{{ $report->quarter }}</u>, <u>{{ $report->school_year }}</u></div>
    </div>

    <div class="section-header">A. Targets vs. Accomplishment</div>
    <table class="custom-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 40%; text-align: left; padding-left: 6px;">PERFORMANCE INDICATORS</th>
                <th colspan="4" style="width: 18%;">TARGETS</th>
                <th colspan="4" style="width: 18%;">ACCOMPLISHMENTS</th>
                <th rowspan="2" style="width: 24%;">REMARKS</th>
            </tr>
            <tr>
                <th style="width: 4.5%;">Q1</th>
                <th style="width: 4.5%;">Q2</th>
                <th style="width: 4.5%;">Q3</th>
                <th style="width: 4.5%;">Q4</th>
                <th style="width: 4.5%;">Q1</th>
                <th style="width: 4.5%;">Q2</th>
                <th style="width: 4.5%;">Q3</th>
                <th style="width: 4.5%;">Q4</th>
            </tr>
        </thead>
        <tbody>
            @php $secA = $report->section_a ?? []; @endphp
            @for ($i = 1; $i <= 8; $i++)
                @php $row = $secA[$i] ?? []; @endphp
                <tr>
                    <td style="padding-left: 6px;">{{ $i }}. {{ $row['indicator'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $row['t_q1'] ?? '0' }}</td>
                    <td style="text-align: center;">{{ $row['t_q2'] ?? '0' }}</td>
                    <td style="text-align: center;">{{ $row['t_q3'] ?? '0' }}</td>
                    <td style="text-align: center;">{{ $row['t_q4'] ?? '0' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $row['a_q1'] ?? '0' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $row['a_q2'] ?? '0' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $row['a_q3'] ?? '0' }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $row['a_q4'] ?? '0' }}</td>
                    <td>{{ $row['remarks'] ?? '' }}</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <table style="width: 100%; font-size: 8px; margin-top: 30px;">
        <tr>
            <td style="text-align: left;">{{ $report->form_code }}</td>
            <td style="text-align: right;">{{ $report->revision_no }}</td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- ================= PAGE 2 ================= -->
    <div class="page-indicator-top">Page | 2</div>

    <div class="section-header">B. RESEARCH ACTIVITIES</div>
    <div class="sub-section-header">B.1 Activities Conducted/Sponsored</div>
    <table class="custom-table" style="margin-bottom: 8px;">
        <thead>
            <tr>
                <th style="width: 38%;">Title of Activity/ies</th>
                <th style="width: 18%;">Date</th>
                <th style="width: 24%;">Venue</th>
                <th style="width: 20%;">Persons Involved</th>
            </tr>
        </thead>
        <tbody>
            @php $b1List = $report->section_b['b1'] ?? []; @endphp
            @forelse($b1List as $b1)
                <tr>
                    <td>{{ $b1['title'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $b1['date'] ?? '' }}</td>
                    <td>{{ $b1['venue'] ?? '' }}</td>
                    <td>{{ $b1['persons'] ?? '' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align: center; color: #888;">No activities conducted recorded.</td></tr>
            @endforelse
            @for ($k = count($b1List); $k < 2; $k++)
                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            @endfor
        </tbody>
    </table>

    <div class="sub-section-header">B.2 Activities Attended</div>
    <table class="custom-table" style="margin-bottom: 8px;">
        <thead>
            <tr>
                <th style="width: 32%;">Title of Activity/ies</th>
                <th style="width: 14%;">Type of Activity</th>
                <th style="width: 12%;">Date</th>
                <th style="width: 14%;">Venue</th>
                <th style="width: 14%;">Name of Person Involved</th>
                <th style="width: 14%;">Sponsoring Agency</th>
            </tr>
        </thead>
        <tbody>
            @php $b2List = $report->section_b['b2'] ?? []; @endphp
            @forelse($b2List as $b2)
                <tr>
                    <td>{{ $b2['title'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $b2['type'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $b2['date'] ?? '' }}</td>
                    <td>{{ $b2['venue'] ?? '' }}</td>
                    <td>{{ $b2['person'] ?? '' }}</td>
                    <td>{{ $b2['sponsor'] ?? '' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align: center; color: #888;">No activities attended recorded.</td></tr>
            @endforelse
            @for ($k = count($b2List); $k < 2; $k++)
                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            @endfor
        </tbody>
    </table>

    <div class="section-header">C. On-Going Researches</div>
    <table class="custom-table" style="margin-bottom: 8px;">
        <thead>
            <tr>
                <th style="width: 38%;">Title</th>
                <th style="width: 22%;">Proponent</th>
                <th style="width: 14%;">College</th>
                <th style="width: 12%;">Time Frame</th>
                <th style="width: 14%;">Date of In-house Review</th>
            </tr>
        </thead>
        <tbody>
            @php $cList = $report->section_c ?? []; @endphp
            @forelse($cList as $cRow)
                <tr>
                    <td>{{ $cRow['title'] ?? '' }}</td>
                    <td>{{ $cRow['proponent'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $cRow['college'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $cRow['time_frame'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $cRow['review_date'] ?? '' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align: center; color: #888;">No ongoing researches recorded.</td></tr>
            @endforelse
            @for ($k = count($cList); $k < 2; $k++)
                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            @endfor
        </tbody>
    </table>

    <div class="section-header">D. Completed Researches</div>
    <table class="custom-table" style="margin-bottom: 4px;">
        <thead>
            <tr>
                <th style="width: 38%;">Title</th>
                <th style="width: 22%;">Proponent</th>
                <th style="width: 16%;">College</th>
                <th style="width: 12%;">Date Started</th>
                <th style="width: 12%;">Date Completed</th>
            </tr>
        </thead>
        <tbody>
            @php $dList = $report->section_d ?? []; @endphp
            @forelse($dList as $dRow)
                <tr>
                    <td>{{ $dRow['title'] ?? '' }}</td>
                    <td>{{ $dRow['proponent'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $dRow['college'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $dRow['date_started'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $dRow['date_completed'] ?? '' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align: center; color: #888;">No completed researches recorded.</td></tr>
            @endforelse
            @for ($k = count($dList); $k < 2; $k++)
                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            @endfor
        </tbody>
    </table>
    <div class="note-box" style="margin-top: 2px;">
        *NOTE: Completed &bull; Terminal Report/Abstracts of research outputs &bull; Certificate of completion signed by the Research Director/Vice President for REI (c/o RESU)
    </div>

    <table style="width: 100%; font-size: 8px; margin-top: 15px;">
        <tr>
            <td style="text-align: left;">{{ $report->form_code }}</td>
            <td style="text-align: right;">{{ $report->revision_no }}</td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- ================= PAGE 3 ================= -->
    <div class="page-indicator-top">Page | 3</div>

    <div class="section-header">E. Research Presentation</div>
    <table class="custom-table" style="margin-bottom: 4px;">
        <thead>
            <tr>
                <th style="width: 8%;">Campus/Unit</th>
                <th style="width: 7%;">Type</th>
                <th style="width: 3%;">#</th>
                <th style="width: 18%;">Research Title</th>
                <th style="width: 6%;">Duration</th>
                <th style="width: 8%;">Started/Completed</th>
                <th style="width: 8%;">Date Presented</th>
                <th style="width: 9%;">Conf Type</th>
                <th style="width: 9%;">Venue</th>
                <th style="width: 12%;">Conference Title</th>
                <th style="width: 8%;">Author/s</th>
                <th style="width: 4%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @php $eList = $report->section_e ?? []; $eNum = 1; @endphp
            @forelse($eList as $eRow)
                <tr>
                    <td>{{ $eRow['unit'] ?? '' }}</td>
                    <td>{{ $eRow['type'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $eNum++ }}</td>
                    <td>{{ $eRow['title'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $eRow['duration'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $eRow['dates'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $eRow['date_presented'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $eRow['conf_type'] ?? '' }}</td>
                    <td>{{ $eRow['venue'] ?? '' }}</td>
                    <td>{{ $eRow['conf_title'] ?? '' }}</td>
                    <td>{{ $eRow['authors'] ?? '' }}</td>
                    <td>{{ $eRow['remarks'] ?? '' }}</td>
                </tr>
            @empty
                <tr><td colspan="12" style="text-align: center; color: #888;">No research presentations recorded.</td></tr>
            @endforelse
            @for ($k = count($eList); $k < 2; $k++)
                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            @endfor
        </tbody>
    </table>
    <div class="note-box" style="margin-top: 2px; margin-bottom: 12px;">
        *NOTE: Presented Researches &bull; Call for Paper/Invitation &bull; Notice of Acceptance &bull; Conference proceeding indicating research article presented &bull; Program of activities &bull; Certificate of participation / recognition
    </div>

    <div class="section-header">F. Publication</div>
    <table class="custom-table" style="margin-bottom: 4px;">
        <thead>
            <tr>
                <th style="width: 9%;">Campus/Unit</th>
                <th style="width: 3%;">#</th>
                <th style="width: 19%;">Research Title</th>
                <th style="width: 6%;">Duration</th>
                <th style="width: 7%;">Date Started</th>
                <th style="width: 7%;">Date Completed</th>
                <th style="width: 8%;">Date Published</th>
                <th style="width: 10%;">Type of Journal</th>
                <th style="width: 13%;">Name of Publication</th>
                <th style="width: 6%;">Vol No.</th>
                <th style="width: 8%;">Author/s</th>
                <th style="width: 4%;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @php $fList = $report->section_f ?? []; $fNum = 1; @endphp
            @forelse($fList as $fRow)
                <tr>
                    <td>{{ $fRow['unit'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $fNum++ }}</td>
                    <td>{{ $fRow['title'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $fRow['duration'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $fRow['date_started'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $fRow['date_completed'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $fRow['date_published'] ?? '' }}</td>
                    <td>{{ $fRow['journal_type'] ?? '' }}</td>
                    <td>{{ $fRow['pub_name'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $fRow['vol_no'] ?? '' }}</td>
                    <td>{{ $fRow['authors'] ?? '' }}</td>
                    <td>{{ $fRow['remarks'] ?? '' }}</td>
                </tr>
            @empty
                <tr><td colspan="12" style="text-align: center; color: #888;">No publications recorded.</td></tr>
            @endforelse
            @for ($k = count($fList); $k < 2; $k++)
                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            @endfor
        </tbody>
    </table>
    <div class="note-box" style="margin-top: 2px;">
        *NOTE: Published Researches &bull; Full copy of the journal's title page, and 1st page of article published &bull; Complete Manuscript
    </div>

    <table style="width: 100%; font-size: 8px; margin-top: 20px;">
        <tr>
            <td style="text-align: left;">{{ $report->form_code }}</td>
            <td style="text-align: right;">{{ $report->revision_no }}</td>
        </tr>
    </table>

    <div class="page-break"></div>

    <!-- ================= PAGE 4 ================= -->
    <div class="page-indicator-top">Page | 4</div>

    <div class="section-header">G. Utilization</div>
    <table class="custom-table" style="margin-bottom: 4px;">
        <thead>
            <tr>
                <th style="width: 30%;">Product Name/Methods/Process/Technology</th>
                <th style="width: 25%;">Patent Number/Product Description</th>
                <th style="width: 25%;">Benefiting Industry/Community</th>
                <th style="width: 20%;">Date Started and Date Completed</th>
            </tr>
        </thead>
        <tbody>
            @php $gList = $report->section_g ?? []; @endphp
            @forelse($gList as $gRow)
                <tr>
                    <td>{{ $gRow['product'] ?? '' }}</td>
                    <td>{{ $gRow['patent'] ?? '' }}</td>
                    <td>{{ $gRow['beneficiary'] ?? '' }}</td>
                    <td style="text-align: center;">{{ $gRow['dates'] ?? '' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align: center; color: #888;">No utilization recorded.</td></tr>
            @endforelse
            @for ($k = count($gList); $k < 2; $k++)
                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            @endfor
        </tbody>
    </table>
    <div class="note-box" style="margin-top: 2px; margin-bottom: 12px;">
        *NOTE: Outputs utilized in the last 3 years &bull; Proof of utilization &bull; MOAs/MOUs &bull; Reports with testimonies &bull; Certifications
    </div>

    <div class="section-header">H. Evidences/Picture of Activities</div>
    <div style="border: 1px solid #000; padding: 6px; font-size: 8.5px; margin-bottom: 25px;">
        <table style="width: 100%; margin-bottom: 4px;">
            <tr>
                <td style="width: 35%;"><strong>Title:</strong> {{ $report->section_h['title'] ?? '' }}</td>
                <td style="width: 35%;"><strong>Venue:</strong> {{ $report->section_h['venue'] ?? '' }}</td>
                <td style="width: 30%;"><strong>Date:</strong> {{ $report->section_h['date'] ?? '' }}</td>
            </tr>
        </table>
        <div><strong>Pictures / Documentation:</strong></div>
        <div style="color: #444; font-size: 8px; margin-top: 2px;">{{ $report->section_h['pictures_note'] ?? 'Documentation photos on official file with College Research Unit.' }}</div>
    </div>

    <!-- Official 3 Signatories Structure -->
    <div class="signatures-wrap clearfix">
        <div class="sig-box">
            <div class="sig-label">Submitted by:</div>
            <div class="sig-line">
                {{ $report->preparer?->name ?? '' }}
            </div>
            <div class="sig-role">College Research Coordinator</div>
        </div>

        <div class="sig-box">
            <div class="sig-label">Noted:</div>
            <div class="sig-line">
                {{ $report->noter?->name ?? '' }}
            </div>
            <div class="sig-role">College Dean</div>
            @if($report->noted_at)
                <div style="font-size: 7.5px; color: #555; font-style: italic;">Date: {{ $report->noted_at->format('M d, Y') }}</div>
            @endif
        </div>

        <div class="sig-box">
            <div class="sig-label">Submitted to:</div>
            <div class="sig-line">
                {{ $report->receiver?->name ?? '' }}
            </div>
            <div class="sig-role">Director, Research Unit</div>
            @if($report->received_at)
                <div style="font-size: 7.5px; color: #555; font-style: italic;">Received: {{ $report->received_at->format('M d, Y') }}</div>
            @endif
        </div>
    </div>

    <table style="width: 100%; font-size: 8px; margin-top: 30px;">
        <tr>
            <td style="text-align: left;">{{ $report->form_code }}</td>
            <td style="text-align: right;">{{ $report->revision_no }}</td>
        </tr>
    </table>

</body>
</html>
