<!DOCTYPE html>
<html>
<head>
    <title>Notice of Acceptance - {{ $proposal->proposal_code ?? $proposal->id }}</title>
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
            margin-bottom: 30px;
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
            margin: 40px 0 30px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .recipient-info {
            margin-bottom: 30px;
        }
        .recipient-info .date-label {
            margin-bottom: 15px;
        }
        .recipient-line {
            margin-top: 2px;
            margin-bottom: 2px;
        }
        .salutation {
            margin-bottom: 20px;
        }
        .body-text {
            text-align: justify;
            text-indent: 50px;
            margin-bottom: 50px;
        }
        .proposal-title {
            font-weight: bold;
            text-decoration: underline;
        }
        .signatures {
            margin-top: 40px;
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
    <div class="header">
        <img class="logo" src="{{ public_path('logo.png') }}" alt="UA Logo">
        <div class="inst-country">Republic of the Philippines</div>
        <div class="inst-name">University of Antique</div>
        <div class="inst-location">Sibalom, Antique</div>
    </div>

    <div class="doc-title">
        Notice of Acceptance of Research Proposal
    </div>

    <div class="recipient-info">
        <div class="date-label">Date: {{ date('F d, Y') }}</div>
        <div class="recipient-line"><strong>{{ $proposal->user->name }}</strong></div>
        <div class="recipient-line">{{ $proposal->user->department ?? 'Faculty Member' }}</div>
        <div class="recipient-line">University of Antique</div>
    </div>

    <div class="salutation">
        Sir/Madam;
    </div>

    <div class="body-text">
        Your Research Proposal titled: <span class="proposal-title">"{{ $proposal->title }}"</span> is accepted for research review, evaluation and improvement by our Institutional experts in preparation for the In-house review.
    </div>

    <div class="signatures">
        <div class="signature-block">
            <div class="signature-line">Prepared by:</div>
            <br><br>
            <div class="signature-line">________________________</div>
            <div class="signature-title">Director, Research Unit</div>
        </div>

        <div class="signature-block">
            <div class="signature-line">Noted:</div>
            <br><br>
            <div class="signature-line">________________________</div>
            <div class="signature-title">VP for REI</div>
        </div>
    </div>

    <div class="footer">
        <div class="footer-left">RESU-FM-023</div>
        <div class="footer-right">Rev.2/01-24-26</div>
    </div>
</body>
</html>
