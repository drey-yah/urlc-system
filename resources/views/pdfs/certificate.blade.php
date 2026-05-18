<!DOCTYPE html>
<html>
<head>
    <title>Certificate of Completion - {{ $proposal->id }}</title>
    <style>
        body { font-family: serif; text-align: center; padding: 50px; border: 10px double #333; margin: 20px; }
        .header { font-size: 30px; font-weight: bold; margin-bottom: 10px; text-transform: uppercase; }
        .sub-header { font-size: 18px; margin-bottom: 50px; }
        .presented-to { font-size: 20px; margin-bottom: 20px; }
        .name { font-size: 36px; font-weight: bold; color: #1a56db; margin-bottom: 20px; text-decoration: underline; }
        .content { font-size: 18px; margin-bottom: 50px; padding: 0 40px; }
        .title { font-size: 24px; font-style: italic; font-weight: bold; margin: 20px 0; }
        .signature-box { display: inline-block; margin-top: 50px; margin-right: 50px; margin-left: 50px; width: 250px; border-top: 1px solid #000; padding-top: 5px; font-family: sans-serif; }
    </style>
</head>
<body>
    <div class="header">Certificate of Completion</div>
    <div class="sub-header">University Research Lifecycle System</div>

    <div class="presented-to">This is proudly presented to:</div>
    <div class="name">{{ $proposal->user->name }}</div>

    <div class="content">
        For successfully completing the research entitled:
        <div class="title">"{{ $proposal->title }}"</div>
        
        <br><br>
        
        <!-- PLACEHOLDER FOR OFFICIAL TEXT/FORMS -->
        <div style="padding:10px; background-color:#fff3cd; color:#856404; border:1px solid #ffeeba; text-align:center; font-family: sans-serif; font-size:14px;">
            <strong>[PLACEHOLDER]</strong><br>
            The official Certificate template will be inserted here.
        </div>
        <!-- END PLACEHOLDER -->

    </div>

    <div>
        <div class="signature-box">Date of Completion: {{ $proposal->phase_updated_at ? \Carbon\Carbon::parse($proposal->phase_updated_at)->format('F d, Y') : date('F d, Y') }}</div>
        <div class="signature-box">Director of Research</div>
    </div>
</body>
</html>
