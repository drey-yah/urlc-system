<!DOCTYPE html>
<html>
<head>
    <title>Notice to Proceed - {{ $proposal->proposal_code ?? $proposal->id }}</title>
    <style>
        @page {
            size: A4;
            margin: 1.2in 1in 1in 1in;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        .logo {
            width: 85px;
            height: 85px;
            margin-bottom: 5px;
        }
        .inst-country {
            font-size: 13px;
            margin: 1px 0;
        }
        .inst-name {
            font-size: 14px;
            font-weight: bold;
            margin: 1px 0;
            text-transform: uppercase;
        }
        .inst-location {
            font-size: 13px;
            margin: 1px 0;
        }
        .doc-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin: 35px 0 25px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .date-label {
            margin-bottom: 25px;
        }
        .salutation {
            margin-bottom: 15px;
            font-weight: normal;
        }
        .congratulations {
            margin-bottom: 15px;
            font-weight: normal;
        }
        .body-text {
            text-align: justify;
            margin-bottom: 20px;
            text-indent: 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }
        .details-table th, .details-table td {
            border: 1px solid #000;
            padding: 8px 12px;
            text-align: left;
            vertical-align: top;
        }
        .details-table td:first-child {
            width: 30%;
            font-weight: bold;
        }
        .details-table td:last-child {
            width: 70%;
        }
        .signatures {
            margin-top: 30px;
        }
        .signature-block {
            margin-bottom: 35px;
        }
        .signature-line {
            margin-bottom: 2px;
        }
        .signature-title {
            font-size: 13px;
            margin-top: 2px;
        }
        .footer {
            position: absolute;
            bottom: -0.4in;
            left: 0;
            right: 0;
            font-size: 11px;
            font-family: Arial, sans-serif;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
        .footer-left {
            float: left;
        }
        .footer-right {
            float: right;
        }
    </style>
</head>
<body>
    @php
        $proponents = $proposal->user->name;
        if ($proposal->collaborators && $proposal->collaborators->count() > 0) {
            $proponents .= ', ' . $proposal->collaborators->pluck('name')->implode(', ');
        }
        
        $evaluationDate = $proposal->phase_updated_at ? \Carbon\Carbon::parse($proposal->phase_updated_at)->format('F d, Y') : date('F d, Y');
    @endphp

    <div class="header">
        <img class="logo" src="{{ public_path('logo.png') }}" alt="UA Logo">
        <div class="inst-country">Republic of the Philippines</div>
        <div class="inst-name">University of Antique</div>
        <div class="inst-location">Sibalom, Antique</div>
    </div>

    <div class="doc-title">
        Notice to Proceed
    </div>

    <div class="date-label">
        Date: {{ date('F d, Y') }}
    </div>

    <div class="salutation">
        Dear Researchers:
    </div>

    <div class="congratulations">
        Congratulations!
    </div>

    <div class="body-text">
        After a thorough review and evaluation by the key Finance Officials of the University on <strong>{{ $evaluationDate }}</strong> and, there being no further revisions of the line-item budget, the Research Unit (RESU) is pleased to inform you that you have complied the requirements for the implementation of your research project.
    </div>

    <table class="details-table">
        <tr>
            <td>Title of Research</td>
            <td>{{ $proposal->title }}</td>
        </tr>
        <tr>
            <td>Proponent/s</td>
            <td>{{ $proponents }}</td>
        </tr>
        <tr>
            <td>College</td>
            <td>{{ $proposal->user->department ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td>Total Budget</td>
            <td>Refer to Line-Item Budget (LIB)</td>
        </tr>
        <tr>
            <td>Implementation Period</td>
            <td>Refer to Approved Proposal Timeline</td>
        </tr>
    </table>

    <div class="body-text" style="margin-bottom: 40px;">
        You may now proceed with the project Implementation.
    </div>

    <div class="signatures">
        <div class="signature-block">
            <div class="signature-line">Truly yours,</div>
            <br><br>
            <div class="signature-line">________________________</div>
            <div class="signature-title">Director, Research Unit</div>
        </div>

        <div class="signature-block">
            <div class="signature-line">Approved:</div>
            <br><br>
            <div class="signature-line">________________________</div>
            <div class="signature-title">VP for Research, Extension, and Innovation</div>
        </div>
    </div>

    <div class="footer">
        <div class="footer-left">RESU-FM-024</div>
        <div class="footer-right">Rev.2/10-13-25</div>
    </div>
</body>
</html>
