<!DOCTYPE html>
<html>
<head>
    <title>Certificate of Completion - {{ $proposal->proposal_code ?? $proposal->id }}</title>
    <style>
        @page {
            size: A4;
            margin: 1.2in 1in 1in 1in;
        }
        body {
            font-family: Georgia, serif;
            font-size: 14px;
            line-height: 1.5;
            color: #000;
            text-align: center;
        }
        .header {
            margin-bottom: 25px;
        }
        .logo {
            width: 85px;
            height: 85px;
            margin-bottom: 5px;
        }
        .inst-country {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 1px 0;
        }
        .inst-name {
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: bold;
            margin: 1px 0;
            text-transform: uppercase;
        }
        .inst-location {
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin: 1px 0;
        }
        .research-unit-heading {
            font-family: Arial, sans-serif;
            font-weight: bold;
            font-size: 13px;
            margin-top: 8px;
            margin-bottom: 1px;
        }
        .research-unit-email {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #0044cc;
            text-decoration: underline;
            margin-top: 1px;
            margin-bottom: 15px;
        }
        .doc-title {
            font-family: "Times New Roman", Times, serif;
            font-size: 20px;
            font-weight: 900;
            margin: 35px 0 20px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .certify-text {
            font-size: 15px;
            margin-bottom: 20px;
        }
        .author-section {
            margin: 25px auto;
            width: 80%;
        }
        .author-name {
            font-size: 20px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }
        .author-label {
            font-family: Arial, sans-serif;
            font-size: 12px;
            font-style: italic;
            color: #444;
        }
        .study-completion-text {
            font-size: 15px;
            margin: 20px 0;
        }
        .proposal-title-box {
            font-size: 16px;
            font-style: italic;
            font-weight: bold;
            margin: 15px auto;
            width: 90%;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .co-authors-section {
            margin: 25px auto;
            text-align: center;
        }
        .co-authors-title {
            font-style: italic;
            font-size: 13px;
            color: #333;
            margin-bottom: 5px;
        }
        .co-author-name {
            font-size: 14px;
            font-weight: bold;
            margin-top: 2px;
            margin-bottom: 2px;
        }
        .co-author-line {
            margin: 5px auto;
            width: 200px;
            border-bottom: 1px solid #000;
            height: 15px;
        }
        .date-given-section {
            font-size: 14px;
            margin: 35px 0;
            line-height: 1.6;
        }
        .signatures {
            margin-top: 50px;
            text-align: center;
        }
        .signature-block {
            display: inline-block;
            margin: 0 auto;
        }
        .signature-line {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin-top: 2px;
        }
        .signature-title {
            font-family: Arial, sans-serif;
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
            text-align: left;
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
        $completionDate = $proposal->phase_updated_at ? \Carbon\Carbon::parse($proposal->phase_updated_at) : now();
        
        // Ordinal day (e.g. 2nd, 24th)
        $day = $completionDate->format('jS');
        // Clean day number without suffix if preferred, but format('jS') matches "this 24th day of October"
        $daySuffix = $completionDate->format('S');
        $dayNumber = $completionDate->format('j');
        
        $month = $completionDate->format('F');
        $year = $completionDate->format('Y');
        
        // Format mm/yr (e.g. 06/2026)
        $monthYear = $completionDate->format('m/Y');
    @endphp

    <div class="header">
        <img class="logo" src="{{ public_path('logo.png') }}" alt="UA Logo">
        <div class="inst-country">Republic of the Philippines</div>
        <div class="inst-name">University of Antique</div>
        <div class="inst-location">Sibalom, Antique</div>
        <div class="research-unit-heading">Research Unit</div>
        <div class="research-unit-email">research@antiquespride.edu.ph</div>
    </div>

    <div class="doc-title">
        Certificate of Research Completion
    </div>

    <div class="certify-text">
        This is to certify that
    </div>

    <div class="author-section">
        <div class="author-name">{{ $proposal->user->name }}</div>
        <div class="author-label">(Author's Name)</div>
    </div>

    <div class="study-completion-text">
        has successfully completed the study in <strong>({{ $monthYear }})</strong> titled:
    </div>

    <div class="proposal-title-box">
        "{{ $proposal->title }}"
    </div>

    <div class="co-authors-section">
        <div class="co-authors-title">Co-author/s:</div>
        @if($proposal->collaborators && $proposal->collaborators->count() > 0)
            @foreach($proposal->collaborators as $collab)
                <div class="co-author-name">{{ $collab->name }}</div>
            @endforeach
        @else
            <div class="co-author-line"></div>
            <div class="co-author-line"></div>
        @endif
    </div>

    <div class="date-given-section">
        Given at University of Antique, Sibalom, Antique this <strong><u>{{ $dayNumber }}{{ $daySuffix }}</u></strong> day of <strong><u>{{ $month }}</u></strong><br>
        in the year of our Lord <strong><u>{{ $year }}</u></strong>.
    </div>

    <div class="signatures">
        <div class="signature-block">
            <br><br>
            <div class="signature-line">____________________________</div>
            <div class="signature-title"><strong>Director, Research Unit</strong></div>
        </div>
    </div>

    <div class="footer">
        <div class="footer-left">RESU-FM-028</div>
        <div class="footer-right">Rev.0/01-19-26</div>
    </div>
</body>
</html>
