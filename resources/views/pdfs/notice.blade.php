<!DOCTYPE html>
<html>
<head>
    <title>Notice of Acceptance - {{ $proposal->id }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 40px; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; margin-bottom: 10px; }
        .content { margin-top: 30px; font-size: 16px; }
        .signature { margin-top: 80px; width: 300px; border-top: 1px solid #000; text-align: center; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">URLC - Notice of Acceptance</div>
        <div>University Research Lifecycle System</div>
    </div>

    <div class="content">
        <p><strong>Date:</strong> {{ date('F d, Y') }}</p>
        <p><strong>To:</strong> {{ $proposal->user->name }}</p>
        <br>
        <p>Dear {{ $proposal->user->name }},</p>
        <p>We are pleased to inform you that your research proposal titled:</p>
        <h3 style="text-align:center;">"{{ $proposal->title }}"</h3>
        <p>has been officially <strong>APPROVED</strong> for implementation.</p>
        
        <br><br>
        
        <!-- PLACEHOLDER FOR OFFICIAL TEXT/FORMS -->
        <div style="padding:20px; background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; text-align:center;">
            <strong>[PLACEHOLDER]</strong><br>
            The official Notice of Acceptance template will be inserted here.
        </div>
        <!-- END PLACEHOLDER -->

    </div>

    <div class="signature">
        Director of Research<br>URLC
    </div>
</body>
</html>
